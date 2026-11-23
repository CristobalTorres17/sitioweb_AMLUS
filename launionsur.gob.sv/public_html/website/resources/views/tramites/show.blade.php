@extends('layouts.app')

@section('content')
    @php
        $resolveMediaUrl = fn($media, ?string $fallback = null): ?string => \App\Support\MediaUrlResolver::resolve(
            $media,
            $fallback,
        );

        $isProcedure = true;
        $sectionLabel = 'Trámites';
        $sectionSingular = 'trámite';
        $categoryName = data_get($service, 'category.name', 'Trámite Municipal');
        $primaryMedia = $service->media->first();
        $downloadUrl = $resolveMediaUrl($primaryMedia, null);
        $downloadLabel =
            data_get($primaryMedia, 'original_filename') ?: data_get($primaryMedia, 'filename') ?: 'Formulario PDF';
        $requirements = collect($service->requirements ?? []);
        $steps = collect($service->steps ?? []);
        $relatedItems = collect($relatedItems ?? []);
        $detailRoute = 'tramites.show';
        $indexRoute = 'tramites.index';
    @endphp

    <section class="relative overflow-hidden bg-primary-container px-6 py-16 text-on-primary md:px-8 md:py-20">
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_38%),radial-gradient(circle_at_bottom_left,rgba(255,255,255,0.10),transparent_32%)]">
        </div>
        <div class="relative mx-auto max-w-7xl">
            <nav class="mb-4 flex flex-wrap items-center gap-2 text-sm text-on-primary/55">
                <a href="{{ route('home') }}" class="transition hover:text-on-primary">Inicio</a>
                <span>/</span>
                <a href="{{ route($indexRoute) }}" class="transition hover:text-on-primary">{{ $sectionLabel }}</a>
                <span>/</span>
                <span class="text-on-primary">{{ $service->title }}</span>
            </nav>

            <div class="grid gap-8 md:grid-cols-12 md:items-end">
                <div class="md:col-span-8">
                    <div
                        class="inline-flex rounded-full border border-on-primary/15 bg-on-primary/10 px-4 py-1.5 text-xs font-bold uppercase tracking-[0.24em] text-secondary-container">
                        {{ $categoryName }}
                    </div>

                    <h1 class="mt-5 max-w-4xl text-5xl font-black tracking-tighter text-white md:text-6xl lg:text-7xl">
                        @if ($service->icon)
                            <i class="{{ $service->icon }} mr-3 text-4xl md:text-5xl"></i>
                        @endif
                        {{ $service->title }}
                    </h1>

                    <p class="mt-5 max-w-2xl text-base leading-7 text-on-primary/80 md:text-lg">
                        {{ $service->description ?? 'Información del ' . $sectionSingular . ' municipal disponible a continuación.' }}
                    </p>
                </div>

                {{-- Right column removed from hero — moved below banner for consistent cards layout --}}
            </div>
        </div>
    </section>

    @php
        $afterHeroBlocksDetail = collect(data_get($cmsPageBlocksByZone ?? [], 'after_hero', []));
    @endphp
    @if ($afterHeroBlocksDetail->isNotEmpty())
        @foreach ($afterHeroBlocksDetail as $cmsBlock)
            {!! $cmsBlock->content !!}
        @endforeach
    @endif

    <section class="bg-background">
        <div class="mx-auto max-w-7xl px-6 py-16 md:px-8 lg:py-20">

            <div class="grid gap-4 md:grid-cols-12 md:items-start">
                <div class="space-y-6 md:col-span-8">
                    <div class="rounded-2xl bg-surface-container-lowest p-6 md:p-8 ambient-shadow">
                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.22em] text-primary/60">Tiempo estimado</p>
                                @if ($service->estimated_time)
                                    <p class="mt-2 text-lg font-semibold text-primary">{{ $service->estimated_time }}</p>
                                @else
                                    <p class="mt-2 text-sm text-on-surface-variant">No especificado</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.22em] text-primary/60">Costo</p>
                                @if ($service->cost)
                                    <p class="mt-2 text-lg font-semibold text-primary">{{ $service->cost }}</p>
                                @else
                                    <p class="mt-2 text-sm text-on-surface-variant">Consultar en alcaldía</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.22em] text-primary/60">Horario</p>
                                @if ($service->schedule)
                                    <p class="mt-2 text-sm font-medium text-on-surface">{{ $service->schedule }}</p>
                                @else
                                    <p class="mt-2 text-sm text-on-surface-variant">No especificado</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.22em] text-primary/60">Categoría</p>
                                <p class="mt-2 text-lg font-semibold text-primary">{{ $categoryName }}</p>
                            </div>
                        </div>

                        <div class="mt-6 border-t border-on-primary/20 pt-6">
                            <h2 class="mb-4 text-2xl font-bold text-primary">Documentación Requerida</h2>

                            @php $docItems = is_array($service->documentation) ? $service->documentation : ($service->documentation ? [$service->documentation] : []); @endphp
                            @if (count($docItems) > 0)
                                <ul class="space-y-2">
                                    @foreach ($docItems as $doc)
                                        <li class="flex items-start gap-2 text-sm text-on-surface">
                                            <span
                                                class="material-symbols-outlined mt-0.5 text-base text-primary">description</span>
                                            {{ $doc }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-on-surface-variant">Sin documentación adicional requerida.</p>
                            @endif
                        </div>

                        <div class="mt-6 border-t border-on-primary/20 pt-6">
                            <h2 class="mb-4 text-2xl font-bold text-primary">Requisitos Previos</h2>

                            <ul class="space-y-3">
                                @forelse ($requirements as $requirement)
                                    @php
                                        $isBasic = data_get($requirement, 'is_basic');

                                        if ($isBasic === null) {
                                            $isBasic = data_get($requirement, 'basic');
                                        }

                                        if ($isBasic === null) {
                                            $isBasic = !data_get($requirement, 'is_conditional', false);
                                        }
                                    @endphp

                                    <li class="flex items-start gap-3">
                                        <span
                                            class="material-symbols-outlined text-xl {{ $isBasic ? 'text-green-600' : 'text-slate-400' }}">
                                            check_circle
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-on-surface">
                                                {{ data_get($requirement, 'title') }}
                                            </p>

                                            @if (data_get($requirement, 'description'))
                                                <p class="mt-1 text-xs leading-5 text-on-surface-variant">
                                                    {{ data_get($requirement, 'description') }}
                                                </p>
                                            @endif
                                        </div>
                                    </li>
                                @empty
                                    <li
                                        class="rounded-lg bg-surface-container-low px-4 py-3 text-sm text-on-surface-variant">
                                        No hay requisitos publicados para este {{ $sectionSingular }}.
                                    </li>
                                @endforelse
                            </ul>

                            <div
                                class="mt-4 rounded-lg bg-surface-container-low p-3 text-xs leading-5 text-on-surface-variant">
                                Nota: toda la documentación debe estar en formato PDF legible.
                            </div>
                        </div>

                        <div class="mt-6 border-t border-on-primary/20 pt-6">
                            <h2 class="mb-4 text-2xl font-bold text-primary">Pasos a Seguir</h2>

                            <ol class="space-y-6">
                                @forelse ($steps as $index => $step)
                                    <li class="flex gap-5">
                                        <span
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-container text-lg font-bold text-on-primary">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <h3 class="text-lg font-bold text-primary">
                                                {{ data_get($step, 'title') }}
                                            </h3>
                                            <p class="mt-1 text-sm leading-6 text-on-surface-variant">
                                                {{ data_get($step, 'description') ?: 'Siga esta indicación para completar este ' . $sectionSingular . '.' }}
                                            </p>
                                        </div>
                                    </li>
                                @empty
                                    <li
                                        class="rounded-lg bg-surface-container-low px-4 py-3 text-sm text-on-surface-variant">
                                        No hay pasos configurados para este {{ $sectionSingular }}.
                                    </li>
                                @endforelse
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 md:col-span-4 md:sticky md:top-24">
                    {{-- Lista de elementos relacionados provista por el controlador --}}

                    @if ($relatedItems->isNotEmpty())
                        <div class="rounded-2xl bg-surface-container-lowest p-6 md:p-8 ambient-shadow">
                            <div class="mb-6">
                                <p class="text-xs font-bold uppercase tracking-[0.22em] text-primary/60">Misma categoría</p>
                                <h2 class="mt-2 text-2xl font-bold text-primary">{{ $sectionLabel }} de la misma categoría
                                </h2>
                            </div>

                            <ul role="list" aria-label="{{ $sectionLabel }} de la misma categoría" class="space-y-3">
                                @foreach ($relatedItems as $item)
                                    <li>
                                        <a href="{{ route($detailRoute, $item->slug) }}" role="option" tabindex="0"
                                            class="block rounded-xl border border-primary/20 bg-primary/5 p-4 transition hover:bg-primary/10 focus:outline-none focus:ring-2 focus:ring-primary">
                                            <p class="text-sm font-semibold text-primary">{{ $item->title }}</p>

                                            @if ($item->estimated_time)
                                                <p class="mt-2 text-xs text-primary/70">Tiempo estimado:
                                                    {{ $item->estimated_time }}</p>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php
                        $sidebarBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'sidebar', []));
                    @endphp
                    @foreach ($sidebarBlocks as $cmsBlock)
                        {!! $cmsBlock->content !!}
                    @endforeach

                    {{-- Gestión Digital moved below the main grid (rendered after the grid) --}}
                </div>
            </div>

            @include('partials.page-nav-widget')

            {{-- Gestión Digital: full-width card placed below both columns --}}
            <div class="mt-10">
                <div class="mx-auto max-w-7xl px-6 md:px-8">
                    <div class="rounded-xl bg-surface-container-lowest p-6 text-on-surface ambient-shadow">
                        <h3 class="mb-2 text-xl font-bold">Gestión Digital</h3>
                        <p class="mb-6 text-sm text-on-surface-variant">Inicie su {{ $sectionSingular }} desde cualquier
                            lugar.</p>

                        <a href="{{ route('contact.show') }}"
                            class="mb-3 inline-flex w-full items-center justify-center rounded-lg bg-secondary-container px-5 py-3 text-center font-bold text-on-secondary-container transition hover:-translate-y-0.5">
                            <span class="material-symbols-outlined mr-2 text-[18px]">bolt</span>
                            Iniciar {{ $sectionSingular === 'trámite' ? 'Trámite' : 'Servicio' }} en Línea
                        </a>

                        @if ($downloadUrl)
                            <a href="{{ $downloadUrl }}" target="_blank" rel="noreferrer"
                                class="inline-flex w-full items-center justify-center rounded-lg border border-on-primary/20 px-5 py-3 text-center text-sm font-semibold text-on-primary transition hover:bg-on-primary/5">
                                <span class="material-symbols-outlined mr-2 text-[18px]">download</span>
                                Descargar Formulario PDF
                            </a>

                            <p class="mt-3 text-xs text-on-primary/55">{{ $downloadLabel }}</p>
                        @else
                            <div
                                class="rounded-lg border border-on-primary/15 bg-on-primary/5 px-4 py-3 text-sm text-on-primary/70">
                                No hay formulario descargable disponible para este {{ $sectionSingular }}.</div>
                        @endif

                        <div class="mt-6 border-t border-on-primary/20 pt-6">
                            <p class="mb-3 text-xs font-bold uppercase tracking-[0.22em] text-on-primary/50">Asistencia
                                Técnica</p>

                            <div class="space-y-3 text-sm text-on-primary/80">
                                <a href="tel:+50326097000" class="flex items-center gap-3 transition hover:text-on-primary">
                                    <span class="material-symbols-outlined text-[20px] text-secondary-container">call</span>
                                    <span>+503 2609-7000</span>
                                </a>

                                <a href="mailto:info@launionsur.gob.sv"
                                    class="flex items-center gap-3 transition hover:text-on-primary">
                                    <span class="material-symbols-outlined text-[20px] text-secondary-container">mail</span>
                                    <span>info@launionsur.gob.sv</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @php
        $midContentBlocksDetail = collect(data_get($cmsPageBlocksByZone ?? [], 'mid_content', []));
        $beforeFooterBlocksDetail = collect(data_get($cmsPageBlocksByZone ?? [], 'before_footer', []));
    @endphp

    @if ($midContentBlocksDetail->isNotEmpty())
        <div class="mx-auto max-w-7xl px-6 py-10 md:px-8">
            @foreach ($midContentBlocksDetail as $cmsBlock)
                {!! $cmsBlock->content !!}
            @endforeach
        </div>
    @endif

    @if ($beforeFooterBlocksDetail->isNotEmpty())
        <div class="mx-auto max-w-7xl px-6 py-10 md:px-8">
            @foreach ($beforeFooterBlocksDetail as $cmsBlock)
                {!! $cmsBlock->content !!}
            @endforeach
        </div>
    @endif
@endsection
