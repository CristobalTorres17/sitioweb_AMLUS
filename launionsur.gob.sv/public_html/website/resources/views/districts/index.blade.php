@extends('layouts.app')

@section('content')
    @php
        $resolveMediaUrl = fn($media, ?string $fallback = null): ?string => \App\Support\MediaUrlResolver::resolve(
            $media,
            $fallback,
        );

        // Build JS-friendly map of slug → color for the interactive SVG
        $colorMap = $districts->mapWithKeys(fn($d) => [$d->svg_path_id => $d->color ?? '#f5a800'])->toJson();
        $slugMap = $districts->mapWithKeys(fn($d) => [$d->svg_path_id => route('districts.show', $d->slug)])->toJson();
    @endphp

    {{-- HERO --}}
    <section class="relative overflow-hidden bg-primary text-white pt-32 pb-16">
        <div class="absolute inset-0 bg-linear-to-br from-primary via-primary/90 to-primary/70 opacity-95"></div>
        <div class="relative z-10 mx-auto max-w-screen-2xl px-6 md:px-8">
            <p
                class="inline-flex rounded-full bg-secondary-container/95 px-4 py-1.5 text-xs font-bold tracking-[0.24em] uppercase text-on-secondary-container">
                Municipio de La Unión
            </p>
            <h1 class="mt-4 text-5xl font-black tracking-tighter text-white md:text-6xl lg:text-7xl">
                Distritos Municipales
            </h1>
            <p class="mt-4 max-w-2xl text-lg text-white/75">
                Explora los {{ $districts->count() }} distritos que conforman el municipio de La Unión, El Salvador.
            </p>
        </div>
    </section>

    <section class="bg-background">
        <div class="mx-auto max-w-screen-2xl px-6 py-16 md:px-8">
            <div class="min-w-0 space-y-10">
                @php
                    $afterHeroBlocksDist = collect(data_get($cmsPageBlocksByZone ?? [], 'after_hero', []));
                @endphp
                @if ($afterHeroBlocksDist->isNotEmpty())
                    @foreach ($afterHeroBlocksDist as $cmsBlock)
                        {!! $cmsBlock->content !!}
                    @endforeach
                @endif

                {{-- MAIN: map + cards --}}
                <section class="mx-auto max-w-screen-2xl px-6 py-16 md:px-8">
                    <div class="flex flex-col gap-12 lg:flex-row lg:items-start">

                        {{-- INTERACTIVE SVG MAP --}}
                        <div class="w-full lg:sticky lg:top-24 lg:w-[42%]">
                            <div class="rounded-2xl bg-white p-4 shadow-md border border-gray-100">
                                <p class="mb-3 text-xs uppercase tracking-widest text-gray-400 font-semibold">Mapa
                                    interactivo</p>
                                <div id="district-map-wrapper" class="relative">
                                    {{-- Tooltip --}}
                                    <div id="map-tooltip"
                                        class="pointer-events-none absolute z-20 hidden rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white shadow-lg">
                                    </div>

                                    {{-- The SVG is loaded inline so we can style/script it --}}
                                    <div id="svg-map-container" class="w-full">
                                        @php
                                            $svgPath = public_path('assets/mapa.svg');
                                            echo file_exists($svgPath)
                                                ? file_get_contents($svgPath)
                                                : '<!-- mapa.svg not found -->';
                                        @endphp
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- DISTRICT CARDS --}}
                        <div class="flex-1">
                            <h2 class="mb-6 text-2xl font-bold text-primary">Todos los distritos</h2>

                            @if ($districts->isEmpty())
                                <p class="text-on-surface-variant">No hay distritos publicados aún.</p>
                            @else
                                <div class="grid gap-4 sm:grid-cols-2">
                                    @foreach ($districts as $district)
                                        @php
                                            $thumbUrl = $resolveMediaUrl(data_get($district, 'thumbnail'), null);
                                            $color = data_get($district, 'color', '#f5a800');
                                            $icon = data_get($district, 'icon');
                                            $summary = \Illuminate\Support\Str::limit(
                                                strip_tags(data_get($district, 'short_summary', '')),
                                                100,
                                            );
                                        @endphp
                                        <a href="{{ route('districts.show', $district->slug) }}"
                                            data-svg-id="{{ $district->svg_path_id }}"
                                            class="district-card group relative flex items-start gap-4 overflow-hidden rounded-xl bg-white p-5 shadow-sm border border-gray-100 transition hover:-translate-y-0.5 hover:shadow-md">
                                            {{-- Color bar --}}
                                            <span class="absolute left-0 top-0 h-full w-1.5 rounded-l-xl"
                                                style="background-color: {{ $color }};"></span>

                                            {{-- Icon / thumbnail --}}
                                            <div class="ml-1 flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                                                style="background-color: {{ $color }}22;">
                                                @if ($thumbUrl)
                                                    <img src="{{ $thumbUrl }}" alt="{{ $district->name }}"
                                                        class="h-10 w-10 rounded-lg object-cover" />
                                                @elseif($icon)
                                                    <span class="material-symbols-outlined text-2xl"
                                                        style="color: {{ $color }};">{{ $icon }}</span>
                                                @else
                                                    <span class="material-symbols-outlined text-2xl"
                                                        style="color: {{ $color }};">location_city</span>
                                                @endif
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <h3 class="font-bold text-primary group-hover:underline">
                                                    {{ $district->name }}</h3>
                                                @if ($summary)
                                                    <p class="mt-0.5 text-xs text-on-surface-variant line-clamp-2">
                                                        {{ $summary }}
                                                    </p>
                                                @endif
                                                <span class="mt-2 inline-flex items-center gap-1 text-xs font-semibold"
                                                    style="color: {{ $color }};">
                                                    Ver distrito
                                                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </section>

                {{-- SVG Map interactivity --}}
                <script>
                    (function() {
                        const colorMap = @json($districts->mapWithKeys(fn($d) => [$d->svg_path_id => $d->color ?? '#f5a800']));
                        const slugMap = @json($districts->mapWithKeys(fn($d) => [$d->svg_path_id => route('districts.show', $d->slug)]));
                        const nameMap = @json($districts->mapWithKeys(fn($d) => [$d->svg_path_id => $d->name]));

                        const tooltip = document.getElementById('map-tooltip');
                        const wrapper = document.getElementById('district-map-wrapper');
                        const container = document.getElementById('svg-map-container');
                        const svg = container ? container.querySelector('svg') : null;

                        if (!svg) return;

                        svg.style.width = '100%';
                        svg.style.height = 'auto';
                        svg.style.display = 'block';

                        // Style each path
                        Object.entries(colorMap).forEach(([pathId, color]) => {
                            const el = svg.getElementById(pathId);
                            if (!el) return;
                            el.style.fill = color + '99'; // semi-transparent
                            el.style.stroke = color;
                            el.style.strokeWidth = '1.5';
                            el.style.cursor = 'pointer';
                            el.style.transition = 'fill 0.18s ease';

                            el.addEventListener('mouseenter', function(e) {
                                el.style.fill = color;
                                if (tooltip && wrapper) {
                                    tooltip.textContent = nameMap[pathId] || pathId;
                                    tooltip.style.display = 'block';
                                }
                            });

                            el.addEventListener('mousemove', function(e) {
                                if (!tooltip || !wrapper) return;
                                const rect = wrapper.getBoundingClientRect();
                                tooltip.style.top = (e.clientY - rect.top - 36) + 'px';
                                tooltip.style.left = (e.clientX - rect.left + 12) + 'px';
                            });

                            el.addEventListener('mouseleave', function() {
                                el.style.fill = color + '99';
                                if (tooltip) tooltip.style.display = 'none';
                            });

                            el.addEventListener('click', function() {
                                const url = slugMap[pathId];
                                if (url) window.location.href = url;
                            });
                        });

                        // Hovering over a district card highlights the map path
                        document.querySelectorAll('.district-card').forEach(function(card) {
                            const svgId = card.getAttribute('data-svg-id');
                            if (!svgId) return;
                            const pathEl = svg.getElementById(svgId);
                            const color = colorMap[svgId];
                            if (!pathEl || !color) return;

                            card.addEventListener('mouseenter', function() {
                                pathEl.style.fill = color;
                            });
                            card.addEventListener('mouseleave', function() {
                                pathEl.style.fill = color + '99';
                            });
                        });
                    })();
                </script>

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
            </div>
        </div>
    </section>
@endsection
