@extends('layouts.app')

@section('content')
    @php
        $resolveMediaUrl = fn($media, ?string $fallback = null): ?string => \App\Support\MediaUrlResolver::resolve(
            $media,
            $fallback,
        );

        $heroUrl = $resolveMediaUrl($district->heroImage, asset('assets/images/backgrounds/slider-1-1.jpg'));
        $thumbUrl = $resolveMediaUrl($district->thumbnail, null);
        $mapImgUrl = $resolveMediaUrl($district->mapImage, null);
        $color = $district->color ?? '#f5a800';
        $icon = $district->icon;
    @endphp

    {{-- HERO --}}
    <section class="relative overflow-hidden bg-primary text-white pt-32 pb-20">
        <div class="absolute inset-0">
            <img src="{{ $heroUrl }}" alt="{{ $district->name }}" class="h-full w-full object-cover"
                fetchpriority="high" />
            <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/80 to-primary/30"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-screen-2xl px-6 md:px-8">
            <a href="{{ route('districts.index') }}"
                class="inline-flex items-center gap-1 text-sm text-white/70 hover:text-white mb-4">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Todos los distritos
            </a>

            <p class="inline-flex rounded-full px-4 py-1.5 text-xs font-bold tracking-[0.24em] uppercase"
                style="background-color: {{ $color }}33; color: {{ $color }};">
                @if ($icon)
                    <span class="material-symbols-outlined mr-1 text-sm">{{ $icon }}</span>
                @endif
                Distrito Municipal
            </p>

            <h1 class="mt-4 text-5xl font-black tracking-tighter text-white md:text-6xl lg:text-7xl">
                {{ $district->name }}
            </h1>

            @if ($district->short_summary)
                <p class="mt-4 max-w-2xl text-lg text-white/80">
                    {{ $district->short_summary }}
                </p>
            @endif
        </div>
    </section>

    @include('partials.page-nav-widget')

    {{-- MAIN CONTENT --}}
    <section class="mx-auto max-w-screen-2xl px-6 py-16 md:px-8">
        <div class="flex flex-col gap-12 lg:flex-row lg:items-start">

            {{-- Description --}}
            <article class="flex-1 min-w-0">
                @if ($district->description)
                    <div class="prose prose-lg max-w-none text-on-surface-variant">
                        {!! nl2br(e($district->description)) !!}
                    </div>
                @endif

                {{-- Map image --}}
                @if ($mapImgUrl)
                    <div class="mt-10">
                        <h2 class="mb-4 text-xl font-bold text-primary">Ubicación en el mapa</h2>
                        <img src="{{ $mapImgUrl }}" alt="Mapa del distrito {{ $district->name }}"
                            class="w-full max-w-lg rounded-2xl border shadow-md" />
                    </div>
                @endif
            </article>

            {{-- Sidebar: map + district info --}}
            <aside class="w-full shrink-0 lg:w-80">
                {{-- District SVG highlight --}}
                @if ($district->svg_path_id)
                    <div class="rounded-2xl bg-white p-4 shadow-sm border border-gray-100 mb-6">
                        <p class="mb-2 text-xs uppercase tracking-widest text-gray-400 font-semibold">Ubicación</p>
                        <div id="mini-map-container" class="w-full">
                            @php
                                $svgPath = public_path('assets/mapa.svg');
                                echo file_exists($svgPath) ? file_get_contents($svgPath) : '';
                            @endphp
                        </div>
                    </div>
                @endif

                {{-- Info card --}}
                <div class="rounded-2xl bg-white p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl"
                            style="background-color: {{ $color }}22;">
                            <span class="material-symbols-outlined text-xl"
                                style="color: {{ $color }};">location_city</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-primary">{{ $district->name }}</h3>
                            <p class="text-xs text-gray-400">Municipio de La Unión</p>
                        </div>
                    </div>
                    <a href="{{ route('contact.show') }}"
                        class="mt-2 block w-full rounded-lg py-2.5 text-center text-sm font-bold text-white transition hover:opacity-90"
                        style="background-color: {{ $color }};">
                        Contactar alcaldía
                    </a>
                    <a href="{{ route('districts.index') }}"
                        class="mt-2 block w-full rounded-lg border py-2.5 text-center text-sm font-semibold text-primary transition hover:bg-gray-50">
                        Ver todos los distritos
                    </a>
                </div>
            </aside>
        </div>
    </section>

    @if ($related->isNotEmpty())
        @php
            $afterHeroBlocksDist = collect(data_get($cmsPageBlocksByZone ?? [], 'after_hero', []));
        @endphp
        @if ($afterHeroBlocksDist->isNotEmpty())
            @foreach ($afterHeroBlocksDist as $cmsBlock)
                {!! $cmsBlock->content !!}
            @endforeach
        @endif

        {{-- RELATED DISTRICTS --}}
        <section class="bg-surface-container-low py-12">
            <div class="mx-auto max-w-screen-2xl px-6 md:px-8">
                <h2 class="mb-6 text-2xl font-bold text-primary">Otros distritos</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($related as $rel)
                        @php
                            $relThumb = $resolveMediaUrl(null, null);
                            $relColor = data_get($rel, 'color', '#f5a800');
                            $relIcon = data_get($rel, 'icon');
                            $relSum = \Illuminate\Support\Str::limit(
                                strip_tags(data_get($rel, 'short_summary', '')),
                                80,
                            );
                        @endphp
                        <a href="{{ route('districts.show', $rel->slug) }}"
                            class="group flex items-start gap-3 rounded-xl bg-white p-4 shadow-sm border border-gray-100 transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg"
                                style="background-color: {{ $relColor }}22;">
                                <span class="material-symbols-outlined text-xl" style="color: {{ $relColor }};">
                                    {{ $relIcon ?: 'location_city' }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-primary group-hover:underline">{{ $rel->name }}</p>
                                @if ($relSum)
                                    <p class="mt-0.5 text-xs text-on-surface-variant line-clamp-2">{{ $relSum }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Mini-map SVG interactivity --}}
    @if ($district->svg_path_id)
        <script>
            (function() {
                const container = document.getElementById('mini-map-container');
                if (!container) return;
                const svg = container.querySelector('svg');
                if (!svg) return;

                svg.style.width = '100%';
                svg.style.height = 'auto';
                svg.style.display = 'block';

                // Dim all paths, then highlight the active district
                const color = @json($color);
                const activeId = @json($district->svg_path_id);

                svg.querySelectorAll('path').forEach(function(path) {
                    path.style.fill = '#e5e7eb';
                    path.style.stroke = '#d1d5db';
                    path.style.strokeWidth = '1';
                });

                const active = svg.getElementById(activeId);
                if (active) {
                    active.style.fill = color;
                    active.style.stroke = color;
                    active.style.strokeWidth = '2';
                }
            })();
        </script>
    @endif

    @php
        $beforeFooterBlocksDist = collect(data_get($cmsPageBlocksByZone ?? [], 'before_footer', []));
    @endphp
    @if ($beforeFooterBlocksDist->isNotEmpty())
        <div class="mx-auto max-w-screen-2xl px-6 py-10 md:px-8">
            @foreach ($beforeFooterBlocksDist as $cmsBlock)
                {!! $cmsBlock->content !!}
            @endforeach
        </div>
    @endif
@endsection
