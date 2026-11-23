@extends('layouts.app')

@section('content')
    @php
        $searchTerm = request('q');
        $activeCategory = request('categoria');
        $afterHeroBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'after_hero', []));
        $midContentBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'mid_content', []));
        $beforeFooterBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'before_footer', []));

        $resolveMediaUrl = fn($media, string $fallback): string => \App\Support\MediaUrlResolver::resolve(
            $media,
            $fallback,
        ) ?? $fallback;

        $categoryChips = collect($categories ?? [])
            ->filter()
            ->values();

        if ($categoryChips->isEmpty()) {
            $categoryChips = collect([
                (object) ['name' => 'Cultura', 'slug' => 'cultura'],
                (object) ['name' => 'Obras', 'slug' => 'obras'],
                (object) ['name' => 'Salud', 'slug' => 'salud'],
                (object) ['name' => 'Educación', 'slug' => 'educacion'],
            ]);
        }

        $heroImage = asset('assets/images/resources/main-slider-img-1-1.png');

        $featuredStory = $events->first();
        $gridStories = collect($events->items())->slice(1);
    @endphp

    <section class="bg-background">
        <div
            class="mx-auto max-w-screen-2xl px-6 pt-12 md:px-8 lg:grid lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-8 lg:items-start">
            <div class="min-w-0 space-y-10">
                @if ($afterHeroBlocks->isNotEmpty())
                    @foreach ($afterHeroBlocks as $cmsBlock)
                        {!! $cmsBlock->content !!}
                    @endforeach
                @endif

                <section class="bg-background">
                    <div class="mx-auto max-w-screen-2xl px-6 pt-12 md:px-8">
                        <header class="mb-12 max-w-3xl">
                            <p class="text-xs font-bold tracking-[0.24em] uppercase text-secondary">Noticias institucionales
                            </p>
                            <h1 class="mt-4 text-5xl font-extrabold tracking-tight text-primary md:text-6xl">Noticias y
                                Eventos</h1>
                            <p class="mt-4 text-lg leading-8 text-on-surface-variant">
                                Manténgase al día con los anuncios, actividades y gestiones más recientes de la
                                Municipalidad de La
                                Unión Sur.
                            </p>
                        </header>

                        <section class="mb-12 rounded-2xl bg-surface-container-low p-4 ambient-shadow md:p-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <form method="GET" action="{{ route('events.index') }}"
                                    class="flex flex-1 flex-col gap-3 md:flex-row md:items-center">
                                    @if ($activeCategory)
                                        <input type="hidden" name="categoria" value="{{ $activeCategory }}" />
                                    @endif

                                    <label class="relative flex-1">
                                        <span
                                            class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[20px] text-on-surface-variant">search</span>
                                        <input type="search" name="q" value="{{ $searchTerm }}"
                                            placeholder="Buscar noticia o evento"
                                            class="w-full rounded-full bg-surface-container-lowest py-3 pl-12 pr-4 text-sm text-primary outline-none ring-1 ring-transparent transition placeholder:text-on-surface-variant focus:ring-secondary" />
                                    </label>

                                    <button type="submit"
                                        class="inline-flex items-center justify-center rounded-full bg-primary px-5 py-3 text-sm font-bold text-on-primary transition hover:bg-primary-container">
                                        Buscar
                                    </button>
                                </form>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-3">
                                <a href="{{ route('events.index', array_filter(['q' => $searchTerm])) }}"
                                    class="chip {{ !$activeCategory ? 'chip-active' : 'chip-idle' }}">
                                    Todas
                                </a>

                                @foreach ($categoryChips as $category)
                                    @php
                                        $slug = data_get($category, 'slug');
                                        $isActive = $activeCategory === $slug;
                                        $categoryUrl = route(
                                            'events.index',
                                            array_filter(['categoria' => $slug, 'q' => $searchTerm]),
                                        );
                                    @endphp

                                    <a href="{{ $categoryUrl }}"
                                        class="chip {{ $isActive ? 'chip-active' : 'chip-idle' }}">
                                        {{ data_get($category, 'name') }}
                                    </a>
                                @endforeach
                            </div>
                        </section>

                        @if ($featuredStory)
                            @php
                                $featuredImageUrl = $resolveMediaUrl(
                                    data_get($featuredStory, 'featuredImage'),
                                    $heroImage,
                                );

                                $featuredRawDescription = trim((string) data_get($featuredStory, 'description', ''));

                                if ($featuredStory->type === 'article') {
                                    $featuredCategoryName = 'Noticia';
                                } else {
                                    $featuredCategoryName =
                                        data_get($featuredStory, 'metadata.legacy_event_type') ?:
                                        data_get($featuredStory, 'metadata.category') ?:
                                        data_get($featuredStory, 'metadata.tipo') ?:
                                        'Evento';
                                }

                                $featuredDate = data_get($featuredStory, 'date');
                                $featuredExcerpt = \Illuminate\Support\Str::limit(
                                    strip_tags(
                                        $featuredRawDescription !== ''
                                            ? $featuredRawDescription
                                            : 'Sin descripción disponible.',
                                    ),
                                    180,
                                );
                            @endphp

                            <article class="relative mb-10 overflow-hidden  bg-primary text-on-primary ambient-shadow">
                                <div class="absolute inset-0">
                                    <img src="{{ $featuredImageUrl }}" alt="{{ data_get($featuredStory, 'title') }}"
                                        class="h-full w-full object-cover object-center" loading="lazy" />
                                    <div class="absolute inset-0 bg-linear-to-t from-primary via-primary/65 to-primary/10">
                                    </div>
                                </div>

                                <div class="relative px-6 py-12 md:px-10 md:py-16 lg:px-14 lg:py-20">
                                    <span
                                        class="inline-flex  bg-secondary-container px-4 py-1 text-[11px] font-bold uppercase tracking-[0.2em] text-on-secondary-container">
                                        Destacada · {{ $featuredCategoryName }}
                                    </span>

                                    <h2 class="mt-6 max-w-3xl text-3xl font-black tracking-tight text-white md:text-5xl">
                                        {{ data_get($featuredStory, 'title') }}
                                    </h2>

                                    <p class="mt-5 max-w-2xl text-sm leading-7 text-on-primary/85 md:text-base">
                                        {{ $featuredExcerpt }}
                                    </p>

                                    <div
                                        class="mt-6 flex flex-wrap items-center gap-4 text-xs font-medium text-on-primary/80 md:text-sm">
                                        <span>{{ optional($featuredDate)->format('d M Y') }}</span>
                                    </div>

                                    <a href="{{ route('events.show', data_get($featuredStory, 'slug')) }}"
                                        class="mt-8 inline-flex items-center gap-2 rounded-full bg-secondary-container px-5 py-3 text-sm font-bold text-on-secondary-container transition hover:-translate-y-0.5">
                                        Leer noticia
                                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                                    </a>
                                </div>
                            </article>
                        @endif

                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                            @forelse ($gridStories as $item)
                                @php
                                    $thumbnailUrl = $resolveMediaUrl(data_get($item, 'featuredImage'), $heroImage);
                                    $rawDescription = trim((string) data_get($item, 'description', ''));

                                    // Get category name based on type
                                    if ($item->type === 'article') {
                                        $categoryName = 'Noticia';
                                    } else {
                                        $categoryName =
                                            data_get($item, 'metadata.legacy_event_type') ?:
                                            data_get($item, 'metadata.category') ?:
                                            data_get($item, 'metadata.tipo') ?:
                                            'Evento';
                                    }

                                    $excerpt = \Illuminate\Support\Str::limit(
                                        strip_tags(
                                            $rawDescription !== '' ? $rawDescription : 'Sin descripción disponible.',
                                        ),
                                        120,
                                    );

                                    $date = data_get($item, 'date');
                                @endphp

                                <article
                                    class="overflow-hidden rounded-xl bg-surface-container-lowest ambient-shadow transition-transform duration-300 hover:-translate-y-0.5">
                                    <div class="aspect-16/10 overflow-hidden bg-surface-container-low">
                                        <img src="{{ $thumbnailUrl }}" alt="{{ data_get($item, 'title') }}"
                                            class="h-full w-full object-cover object-center transition duration-500 hover:scale-[1.03]"
                                            loading="lazy" />
                                    </div>

                                    <div class="p-6">
                                        <div
                                            class="flex items-center justify-between gap-3 text-xs font-bold tracking-[0.18em] uppercase text-on-surface-variant">
                                            <span
                                                class="rounded-full bg-secondary-container px-3 py-1 text-[11px] text-on-secondary-container">{{ $categoryName }}</span>
                                            <span>{{ optional($date)->format('d M Y') }}</span>
                                        </div>

                                        <h2 class="mt-4 text-lg font-bold text-primary">{{ data_get($item, 'title') }}</h2>
                                        <p class="mt-3 text-sm leading-6 text-on-surface-variant">{{ $excerpt }}</p>

                                        <a href="{{ route('events.show', data_get($item, 'slug')) }}"
                                            class="mt-5 inline-flex items-center gap-1 font-semibold text-secondary">
                                            Leer más
                                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                                        </a>
                                    </div>
                                </article>
                            @empty
                                @if (!$featuredStory)
                                    <div
                                        class="rounded-xl bg-surface-container-lowest p-8 text-on-surface-variant ambient-shadow md:col-span-2 xl:col-span-3">
                                        Todavía no hay noticias o eventos publicados.
                                    </div>
                                @endif
                            @endforelse
                        </div>

                        @if ($midContentBlocks->isNotEmpty())
                            <div class="mt-10 space-y-6">
                                @foreach ($midContentBlocks as $cmsBlock)
                                    {!! $cmsBlock->content !!}
                                @endforeach
                            </div>
                        @endif

                        @if ($events->hasPages())
                            <div class="mt-12">
                                {{ $events->links() }}
                            </div>
                        @endif
                        @if ($beforeFooterBlocks->isNotEmpty())
                            <div class="mt-10 space-y-6">
                                @foreach ($beforeFooterBlocks as $cmsBlock)
                                    {!! $cmsBlock->content !!}
                                @endforeach
                            </div>
                        @endif
                    </div>


            </div>
            <aside class="hidden lg:block lg:sticky lg:top-28">
                @include('partials.page-nav-widget', ['compact' => true])
            </aside>
    </section>
@endsection
