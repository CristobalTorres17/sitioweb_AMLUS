<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\District;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $results = $this->searchResults($query, 20);

        return view('search.index', [
            'title' => $query !== '' ? 'Búsqueda: ' . $query : 'Búsqueda',
            'query' => $query,
            'results' => $results,
            'count' => $results->count(),
        ]);
    }

    public function searchApi(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', $request->query('query', '')));
        $results = $this->searchResults($query, 15);

        return response()->json([
            'success' => true,
            'query' => $query,
            'count' => $results->count(),
            'has_more' => false,
            'results' => $results,
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function searchResults(string $query, int $limit): Collection
    {
        if ($query === '') {
            return collect();
        }

        $eventResults = $this->searchEventResults($query, $limit);
        $serviceResults = $this->searchServiceResults($query, $limit);
        $districtResults = $this->searchDistrictResults($query, $limit);
        $pageResults = $this->searchPageResults($query);

        return $eventResults
            ->concat($serviceResults)
            ->concat($districtResults)
            ->concat($pageResults)
            ->take($limit)
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function searchEventResults(string $query, int $limit): Collection
    {
        $searchTerm = '%' . $query . '%';

        $results = Event::published()
            ->where(function ($builder) use ($searchTerm) {
                $builder->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhere('content', 'like', $searchTerm)
                    ->orWhere('location', 'like', $searchTerm)
                    ->orWhere('organizer', 'like', $searchTerm);
            })
            ->orderByDesc('event_date')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'event_date', 'description', 'location', 'organizer']);

        return $results->map(function (Event $event): array {
            return [
                'type' => 'event',
                'type_label' => 'Noticia',
                'title' => $event->title,
                'url' => route('events.show', $event->slug),
                'date' => optional($event->event_date)->toDateString(),
                'excerpt' => Str::limit(strip_tags((string) ($event->description ?? $event->content ?? '')), 160),
            ];
        });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function searchServiceResults(string $query, int $limit): Collection
    {
        $searchTerm = '%' . $query . '%';

        $services = Service::active()
            ->with([
                'category' => function ($builder) {
                    $builder->withoutGlobalScope('service_type');
                },
            ])
            ->whereIn('kind', [ServiceCategory::TYPE_SERVICE, ServiceCategory::TYPE_PROCEDURE])
            ->where(function ($builder) use ($searchTerm) {
                $builder->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                        $categoryQuery->withoutGlobalScope('service_type')->where(function ($nestedQuery) use ($searchTerm) {
                            $nestedQuery->where('name', 'like', $searchTerm)
                                ->orWhere('description', 'like', $searchTerm);
                        });
                    });
            })
            ->orderBy('order')
            ->limit($limit)
            ->get(['id', 'category_id', 'title', 'slug', 'description', 'estimated_time', 'order']);

        return $services->map(function (Service $service): array {
            $type = data_get($service, 'kind') === ServiceCategory::TYPE_PROCEDURE
                ? 'procedure'
                : 'service';

            return [
                'type' => $type,
                'type_label' => $type === 'procedure' ? 'Trámite' : 'Servicio',
                'title' => $service->title,
                'url' => $type === 'procedure'
                    ? route('tramites.show', $service->slug)
                    : route('services.show', $service->slug),
                'excerpt' => Str::limit(strip_tags((string) ($service->description ?? '')), 160),
            ];
        });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function searchDistrictResults(string $query, int $limit): Collection
    {
        $searchTerm = '%' . $query . '%';

        $districts = District::active()
            ->where(function ($builder) use ($searchTerm) {
                $builder->where('name', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhere('short_summary', 'like', $searchTerm);
            })
            ->orderBy('position')
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'description', 'short_summary', 'position']);

        return $districts->map(function (District $district): array {
            return [
                'type' => 'district',
                'type_label' => 'Distrito',
                'title' => $district->name,
                'url' => route('districts.show', $district->slug),
                'excerpt' => Str::limit(strip_tags((string) ($district->short_summary ?? $district->description ?? '')), 160),
            ];
        });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function searchPageResults(string $query): Collection
    {
        $pages = collect([
            [
                'title' => 'Nosotros',
                'route' => 'about',
                'description' => 'Información institucional de la Municipalidad de La Unión Sur.',
                'keywords' => ['institución', 'alcaldía', 'municipalidad', 'gobierno', 'nosotros'],
            ],
            [
                'title' => 'Equipo',
                'route' => 'team',
                'description' => 'Conoce al equipo que acompaña la gestión municipal.',
                'keywords' => ['equipo', 'personal', 'funcionarios', 'alcaldía'],
            ],
            [
                'title' => 'Preguntas Frecuentes',
                'route' => 'faq',
                'description' => 'Respuestas a consultas comunes sobre trámites y servicios.',
                'keywords' => ['faq', 'preguntas', 'consultas', 'ayuda'],
            ],
            [
                'title' => 'Precios',
                'route' => 'pricing',
                'description' => 'Planes y tarifas publicadas en el sitio.',
                'keywords' => ['precios', 'tarifas', 'costos'],
            ],
            [
                'title' => 'Mapa',
                'route' => 'mapa',
                'description' => 'Ubicación y puntos de interés en el municipio.',
                'keywords' => ['mapa', 'ubicación', 'dirección'],
            ],
            [
                'title' => 'Proyectos',
                'route' => 'projects',
                'description' => 'Listado de proyectos municipales publicados.',
                'keywords' => ['proyectos', 'obras', 'planes'],
            ],
            [
                'title' => 'Testimonios',
                'route' => 'testimonials',
                'description' => 'Opiniones y testimonios destacados.',
                'keywords' => ['testimonios', 'opiniones', 'voces'],
            ],
            [
                'title' => 'Contacto',
                'route' => 'contact.show',
                'description' => 'Formulario y canales de contacto institucional.',
                'keywords' => ['contacto', 'correo', 'teléfono', 'ayuda'],
            ],
        ]);

        $searchTerm = Str::lower($query);

        return $pages
            ->filter(function (array $page) use ($searchTerm): bool {
                $haystack = Str::lower($page['title'] . ' ' . $page['description'] . ' ' . implode(' ', $page['keywords']));

                return Str::contains($haystack, $searchTerm);
            })
            ->map(function (array $page): array {
                return [
                    'type' => 'page',
                    'type_label' => 'Página',
                    'title' => $page['title'],
                    'url' => route($page['route']),
                    'excerpt' => $page['description'],
                ];
            });
    }
}
