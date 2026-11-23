<?php

namespace App\Http\Controllers;

use App\Models\Page as CmsPage;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return $this->pageView('about', 'Nosotros');
    }

    public function team(): View
    {
        return $this->pageView('team', 'Equipo');
    }

    public function faq(): View
    {
        return $this->pageView('faq', 'Preguntas Frecuentes');
    }

    public function pricing(): View
    {
        return $this->pageView('pricing', 'Precios');
    }

    public function mapa(): View
    {
        return $this->pageView('mapa', 'Mapa');
    }

    public function integridadTransparencia(): View
    {
        $page = CmsPage::query()
            ->published()
            ->where('slug', 'integridad-transparencia')
            ->first();

        if ($page) {
            return view('pages.cms', [
                'title' => $page->title,
                'page' => $page,
            ]);
        }

        return $this->pageView('integridad-transparencia', 'Integridad y Transparencia');
    }

    public function projects(): View
    {
        return $this->pageView('projects', 'Proyectos');
    }

    public function projectDetails(string $slug): View
    {
        return $this->pageView('project-details', 'Detalle de Proyecto', ['slug' => $slug]);
    }

    public function testimonials(): View
    {
        return $this->pageView('testimonials', 'Testimonios');
    }

    public function cms(string $slug): View
    {
        $page = CmsPage::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.cms', [
            'title' => $page->title,
            'page' => $page,
        ]);
    }

    private function pageView(string $view, string $title, array $data = []): View
    {
        return view('pages.' . $view, array_merge([
            'title' => $title,
        ], $data));
    }
}
