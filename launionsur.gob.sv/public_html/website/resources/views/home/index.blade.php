@extends('layouts.app')

{{-- Preload the first hero image so the browser's preloader fetches it early --}}
@push('head')
    @php
        $renderHeroTitle = function (string $title): string {
            $words = preg_split('/\s+/', trim($title)) ?: [];

            if (count($words) <= 1) {
                return '<span class="text-white">' . e($title) . '</span>';
            }

            $accentSize = count($words) >= 5 ? 2 : 1;
            $mainWords = implode(' ', array_slice($words, 0, max(1, count($words) - $accentSize)));
            $accentWords = implode(' ', array_slice($words, -$accentSize));

            return '<span class="block text-white">' .
                e($mainWords) .
                '</span>' .
                '<span class="block text-secondary-container">' .
                e($accentWords) .
                '</span>';
        };

        $firstBanner = collect($banners ?? [])
            ->filter(function ($b) {
                $media = data_get($b, 'media') ?: [];
                if (!empty($media) && count($media) > 0) {
                    return true;
                }
                if (!empty(data_get($b, 'image_path')) || !empty(data_get($b, 'image_url'))) {
                    return true;
                }
                return false;
            })
            ->first();
        $firstMedia = data_get($firstBanner, 'media.0');
        $firstCandidate =
            data_get($firstMedia, 'url') ?:
            data_get($firstMedia, 'path') ?:
            data_get($firstMedia, 'file_path') ?:
            data_get($firstMedia, 'storage_path') ?:
            data_get($firstMedia, 'disk_path') ?:
            data_get($firstBanner, 'image_url') ?:
            data_get($firstBanner, 'image_path');
        $heroPreloadUrl =
            \App\Support\MediaUrlResolver::resolve(
                $firstCandidate,
                asset('assets/images/resources/main-slider-img-1-1.png'),
            ) ?? asset('assets/images/resources/main-slider-img-1-1.png');
    @endphp
    <link rel="preload" as="image" href="{{ $heroPreloadUrl }}" fetchpriority="high" />
@endpush

@push('styles')
    <style>
        [data-hero-slider] [data-hero-image] {
            transform: translate3d(120%, 0, 0);
            opacity: 0;
            transition: transform 700ms ease-out, opacity 700ms ease-out;
            will-change: transform, opacity;
        }

        [data-hero-slider] [data-hero-slide][data-active="true"] [data-hero-image] {
            transform: translate3d(0, 0, 0);
            opacity: 1;
        }
    </style>
@endpush

@section('content')
    @php
        $resolveMediaUrl = fn($media, string $fallback): string => \App\Support\MediaUrlResolver::resolve(
            $media,
            $fallback,
        ) ?? $fallback;

        $heroSlides = collect($banners ?? [])
            ->filter(function ($b) {
                $media = data_get($b, 'media') ?: [];
                if (!empty($media) && count($media) > 0) {
                    return true;
                }
                if (!empty(data_get($b, 'image_path')) || !empty(data_get($b, 'image_url'))) {
                    return true;
                }
                return false;
            })
            ->values();

        if ($heroSlides->isEmpty()) {
            $heroSlides = collect([
                (object) [
                    'title' => 'Construyendo el Futuro de nuestra Comunidad',
                    'subtitle' => 'Gestión pública cercana, digital y transparente para todos los ciudadanos.',
                    'button_text' => 'Ver Trámites en Línea',
                    'button_url' => route('tramites.index'),
                    'secondary_text' => 'Transparencia Activa',
                    'secondary_url' => route('about'),
                    'media' => collect(),
                ],
            ]);
        }

        $featuredServices = collect($featuredServices ?? ($services ?? []))
            ->filter()
            ->values()
            ->take(4);
        $latestEvents = collect($events ?? [])
            ->filter()
            ->values();
        $mayor =
            $mayor ??
            (object) [
                'name' => 'Alcaldía Municipal',
                'role' => 'Compromiso y excelencia',
                'quote' => 'Trabajamos para que cada trámite sea claro, cercano y digno de la confianza ciudadana.',
                'stats' => [
                    ['value' => '95%', 'label' => 'Gestión Realizada'],
                    ['value' => '12K+', 'label' => 'Vecinos Atendidos'],
                ],
                'image_url' => asset('assets/images/resources/main-slider-img-1-1.png'),
            ];

        $heroBaseImage = asset('assets/images/resources/main-slider-img-1-1.png');
        $afterHeroBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'after_hero', []));
    @endphp

    @if ($heroSlides->isNotEmpty())
        <section class="relative min-h-117.5 overflow-hidden bg-primary text-on-primary" data-hero-slider>
            <div class="absolute inset-0">
                @php
                    // Fondo fijo del hero (se usará cuando no haya slides o como base visual).
                    // Preferimos un asset llamado "slider-1-1" si está disponible; si no,
                    // se usa el fallback definido en `$heroBaseImage`.
                    $prefBg = asset('assets/images/backgrounds/slider-1-1.jpg');
                    $heroBg = file_exists(public_path('assets/images/backgrounds/slider-1-1.jpg'))
                        ? $prefBg
                        : $heroBaseImage;
                @endphp

                <img src="{{ $heroBg }}" alt="Hero background" class="h- w-full object-cover" loading="eager" />
                <div class="absolute inset-0 bg-linear-to-r from-primary/30 via-primary/50 to-transparent"></div>

                <div class="absolute inset-0 flex items-center justify-center pointer-events-none px-0">
                    @foreach ($heroSlides as $index => $slide)
                        @php
                            $slideImageUrl = $resolveMediaUrl(data_get($slide, 'image_url'), $heroBaseImage);

                            $slideTitle = data_get($slide, 'title') ?: 'Construyendo el Futuro de nuestra Comunidad';
                            $slideSubtitle =
                                data_get($slide, 'subtitle') ?:
                                'Gestión pública cercana, digital y transparente para todos los ciudadanos.';
                            $slideButtonText = data_get($slide, 'button_text') ?: 'Ver Trámites en Línea';
                            $slideButtonUrl = data_get($slide, 'button_url') ?: route('tramites.index');
                            $slideSecondaryText = data_get($slide, 'secondary_text') ?: 'Transparencia Activa';
                            $slideSecondaryUrl = data_get($slide, 'secondary_url') ?: route('about');
                        @endphp

                        <div data-hero-slide data-active="{{ $index === 0 ? 'true' : 'false' }}"
                            data-hero-title="{{ e($slideTitle) }}" data-hero-subtitle="{{ e($slideSubtitle) }}"
                            data-hero-button-text="{{ e($slideButtonText) }}"
                            data-hero-button-url="{{ e($slideButtonUrl) }}"
                            data-hero-secondary-text="{{ e($slideSecondaryText) }}"
                            data-hero-secondary-url="{{ e($slideSecondaryUrl) }}"
                            class="absolute left-0 right-0 transition-opacity duration-700 {{ $index === 0 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}"
                            aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                            <div class="absolute inset-0 bg-linear-to-r from-primary/40 via-primary/90 to-transparent">
                            </div>
                            <div data-hero-image
                                class="relative ml-auto mr-8 h-80 w-full max-w-5xl overflow-hidden rounded-3xl md:mr-12 md:h-120 lg:mr-16 lg:h-155">
                                <img src="{{ $slideImageUrl }}" alt="{{ $slideTitle }}"
                                    class="w-full h-full object-contain object-right"
                                    {{ $index === 0 ? 'fetchpriority="high"' : 'loading="lazy"' }} />

                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            <div class="relative z-10 mx-auto flex min-h-117.5 max-w-screen-2xl items-center px-6 py-16 md:px-8">
                <div class="max-w-3xl">
                    <div
                        class="inline-flex items-center gap-2 rounded-full bg-secondary-container/95 px-4 py-1.5 text-on-secondary-container shadow-sm">
                        <img src="{{ asset('assets/images/lusur.png') }}" alt="Logo" class="h-5">
                        <span class="text-xs font-bold tracking-[0.22em] uppercase">Alcaldía de La Unión Sur</span>
                    </div>

                    <h1 data-hero-title-display
                        class="mt-6 max-w-2xl text-5xl font-black tracking-tighter text-white md:text-6xl lg:text-7xl">
                        {!! $renderHeroTitle(data_get($heroSlides->first(), 'title', 'Construyendo el Futuro de nuestra Comunidad')) !!}
                    </h1>

                    <p data-hero-subtitle-display class="mt-6 max-w-2xl text-base leading-7 text-on-primary/80 md:text-lg">
                        {{ data_get($heroSlides->first(), 'subtitle', 'Gestión pública cercana, digital y transparente para todos los ciudadanos.') }}
                    </p>

                    <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                        <a data-hero-button-display
                            href="{{ data_get($heroSlides->first(), 'button_url', route('tramites.index')) }}"
                            class="inline-flex items-center justify-center rounded-lg bg-secondary-container px-6 py-3 font-bold text-on-secondary-container transition-transform duration-300 hover:-translate-y-0.5">
                            <span
                                data-hero-button-text-display>{{ data_get($heroSlides->first(), 'button_text', 'Ver Trámites en Línea') }}</span>
                        </a>

                        <a data-hero-secondary-display
                            href="{{ data_get($heroSlides->first(), 'secondary_url', route('about')) }}"
                            class="inline-flex items-center justify-center rounded-lg border border-on-primary/20 bg-on-primary/5 px-6 py-3 font-bold text-on-primary transition-colors duration-300 hover:bg-on-primary/10">
                            <span
                                data-hero-secondary-text-display>{{ data_get($heroSlides->first(), 'secondary_text', 'Transparencia Activa') }}</span>
                        </a>
                    </div>

                    <div class="mt-12 flex items-center gap-4">
                        <button type="button" data-hero-prev
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-on-primary/20 bg-on-primary/5 text-on-primary transition hover:bg-on-primary/10"
                            aria-label="Banner anterior">
                            <span class="material-symbols-outlined text-[22px]">chevron_left</span>
                        </button>
                        <button type="button" data-hero-next
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-on-primary/20 bg-on-primary/5 text-on-primary transition hover:bg-on-primary/10"
                            aria-label="Siguiente banner">
                            <span class="material-symbols-outlined text-[22px]">chevron_right</span>
                        </button>

                        @if ($heroSlides->count() > 1)
                            <div class="ml-2 flex items-center gap-2">
                                @foreach ($heroSlides as $index => $slide)
                                    <button type="button" data-hero-dot
                                        class="h-2.5 w-8 rounded-full transition-colors {{ $index === 0 ? 'bg-secondary-container' : 'bg-white/30' }}"
                                        aria-label="Ir al banner {{ $index + 1 }}"
                                        aria-current="{{ $index === 0 ? 'true' : 'false' }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif
    <section class="bg-background">
        <div
            class="mx-auto max-w-screen-2xl px-6 py-10 md:px-8 lg:grid lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-8 lg:items-start">
            <div class="min-w-0 space-y-10">
                @if ($afterHeroBlocks->isNotEmpty())
                    @foreach ($afterHeroBlocks as $cmsBlock)
                        <section>
                            <div class="mx-auto max-w-none py-20">
                                {!! $cmsBlock->content !!}
                            </div>
                        </section>
                    @endforeach
                @endif

                <section class="bg-background">
                    <div class="mx-auto max-w-none py-20">
                        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                            <div>
                                <p class="text-xs font-bold tracking-[0.24em] uppercase text-secondary">Trámites y servicios
                                </p>
                                <h2 class="mt-3 text-4xl font-black tracking-tight text-primary md:text-5xl">Trámites y
                                    servicios</h2>
                            </div>

                            <div class="flex gap-4">
                                <a href="{{ route('services.index') }}"
                                    class="inline-flex items-center gap-2 font-semibold text-secondary transition hover:text-primary">
                                    Ver todos los servicios
                                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                                </a>

                                <a href="{{ route('tramites.index') }}"
                                    class="inline-flex items-center gap-2 font-semibold text-secondary transition hover:text-primary">
                                    Ver todos los trámites
                                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                                </a>
                            </div>
                        </div>

                        <div class="mt-10 grid auto-rows-[180px] gap-6 lg:grid-cols-4">
                            @foreach ($featuredItems as $index => $item)
                                @php
                                    $isPrimary = $index === 0;
                                    $serviceIcon =
                                        data_get($item, 'icon') ?:
                                        ['description', 'payments', 'groups', 'public'][$index % 4];
                                    $categoryName = data_get($item, 'category_name', 'Servicio municipal');
                                    $detailRoute =
                                        data_get($item, 'type') === 'tramite' ? 'tramites.show' : 'services.show';
                                @endphp

                                <article
                                    class="overflow-hidden rounded-xl p-6 ambient-shadow transition-transform duration-300 hover:-translate-y-1 {{ $isPrimary ? 'bg-primary-container text-on-primary lg:col-span-2 lg:row-span-2' : 'bg-surface-container-lowest text-on-surface lg:col-span-1' }}">
                                    <div class="flex h-full flex-col">
                                        <div
                                            class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl {{ $isPrimary ? 'bg-secondary-container text-on-secondary-container' : 'bg-secondary-container/40 text-secondary' }}">
                                            <span class="material-symbols-outlined text-[28px]">{{ $serviceIcon }}</span>
                                        </div>

                                        <p
                                            class="text-xs font-bold tracking-[0.22em] uppercase {{ $isPrimary ? 'text-on-primary/60' : 'text-secondary' }}">
                                            {{ $categoryName }}
                                        </p>

                                        <h3
                                            class="mt-3 text-2xl font-black tracking-tight {{ $isPrimary ? 'text-white' : 'text-primary' }}">
                                            {{ data_get($item, 'title') }}
                                        </h3>

                                        <p
                                            class="mt-3 max-w-xl text-sm leading-6 {{ $isPrimary ? 'text-on-primary/80' : 'text-on-surface-variant' }}">
                                            {{ \Illuminate\Support\Str::limit(strip_tags(data_get($item, 'description', '')), $isPrimary ? 220 : 110) }}
                                        </p>

                                        <div class="mt-auto pt-6">
                                            <a href="{{ route($detailRoute, data_get($item, 'slug')) }}"
                                                class="inline-flex items-center gap-2 font-bold {{ $isPrimary ? 'text-secondary-container' : 'text-secondary' }}">
                                                Saber más
                                                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach

                            {{-- <article class="rounded-xl bg-primary-container p-6 text-on-primary ambient-shadow lg:col-span-2">
                                <div class="flex h-full flex-col justify-between gap-6 sm:flex-row sm:items-center">
                                    <div>
                                        <p class="text-xs font-bold tracking-[0.22em] uppercase text-on-primary/60">Reporte ciudadano
                                        </p>
                                        <h3 class="mt-3 text-3xl font-black tracking-tight text-white">Canal directo para incidencias y
                                            solicitudes</h3>
                                        <p class="mt-3 max-w-xl text-sm leading-6 text-on-primary/80">
                                            Envíe reportes de alumbrado, limpieza, baches o cualquier situación que requiera atención
                                            municipal.
                                        </p>
                                    </div>

                                    <a href="{{ route('contact.show') }}"
                                        class="inline-flex items-center justify-center rounded-lg bg-secondary-container px-6 py-3 font-bold text-on-secondary-container transition-transform duration-300 hover:-translate-y-0.5">
                                        Abrir reporte
                                    </a>
                                </div>
                            </article> --}}
                        </div>
                    </div>
                </section>

                @php
                    // 'content' zone is the general-purpose "main content" zone used by several templates.
                    // We merge it with 'mid_content' so blocks assigned to either zone appear here.
                    $midContentBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'mid_content', []))->concat(
                        collect(data_get($cmsPageBlocksByZone ?? [], 'content', [])),
                    );
                @endphp

                @if ($midContentBlocks->isNotEmpty())
                    @foreach ($midContentBlocks as $cmsBlock)
                        <section class="mx-auto max-w-none py-20">
                            {!! $cmsBlock->content !!}
                        </section>
                    @endforeach
                @endif

                <section class="bg-surface-container-low/50">
                    <div class="mx-auto max-w-none py-20">
                        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                            <div>
                                <p class="text-xs font-bold tracking-[0.24em] uppercase text-secondary">Noticias y eventos
                                </p>
                                <h2 class="mt-3 text-4xl font-black tracking-tight text-primary md:text-5xl">Últimas
                                    noticias</h2>
                            </div>

                            <a href="{{ route('events.index') }}"
                                class="inline-flex items-center gap-2 font-semibold text-secondary transition hover:text-primary">
                                Ver todas las noticias
                                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                            </a>
                        </div>

                        <div class="mt-10 grid gap-6 lg:grid-cols-3">
                            @forelse ($latestEvents as $event)
                                @php
                                    $eventMedia = data_get($event, 'featuredImage');
                                    $eventImageUrl = $resolveMediaUrl(
                                        $eventMedia,
                                        asset('assets/images/resources/main-slider-img-1-1.png'),
                                    );
                                    $eventCategory =
                                        data_get($event, 'category.name') ?:
                                        data_get($event, 'metadata.category', 'Noticias');
                                    $eventExcerpt = \Illuminate\Support\Str::limit(
                                        strip_tags(data_get($event, 'description', '')),
                                        120,
                                    );
                                @endphp

                                <article
                                    class="overflow-hidden rounded-xl bg-surface-container-lowest ambient-shadow transition hover:-translate-y-1 lg:flex">
                                    <img src="{{ $eventImageUrl }}" alt="{{ data_get($event, 'title') }}"
                                        class="h-48 w-full object-cover lg:h-auto lg:w-44" loading="lazy" />

                                    <div class="flex flex-1 flex-col p-6">
                                        <div
                                            class="flex items-center gap-3 text-xs font-bold tracking-[0.18em] uppercase text-on-surface-variant">
                                            <span
                                                class="rounded-full bg-secondary-container px-3 py-1 text-[11px] text-on-secondary-container">{{ $eventCategory }}</span>
                                            <span>{{ optional(data_get($event, 'event_date'))->format('d M Y') }}</span>
                                        </div>

                                        <h3 class="mt-4 text-lg font-bold text-primary">
                                            {{ data_get($event, 'title') }}
                                        </h3>

                                        <p class="mt-3 text-sm leading-6 text-on-surface-variant">
                                            {{ $eventExcerpt }}
                                        </p>

                                        <div class="mt-5">
                                            <a href="{{ route('events.show', data_get($event, 'slug')) }}"
                                                class="inline-flex items-center gap-2 font-semibold text-secondary">
                                                Leer más
                                                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div
                                    class="rounded-xl bg-surface-container-lowest p-8 text-on-surface-variant ambient-shadow lg:col-span-3">
                                    Todavía no hay noticias publicadas.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>

                @php

                @endphp

                {{-- <section class="bg-background">
                    <div class="mx-auto max-w-screen-2xl px-6 py-20 md:px-8">
                        <div class="grid gap-8 lg:grid-cols-[0.92fr,1.08fr] lg:items-center">
                            <div class="relative overflow-hidden rounded-3xl ambient-shadow">
                                <img src="{{ data_get($mayor, 'image_url') }}" alt="{{ data_get($mayor, 'name') }}"
                                    class="h-140 w-full object-cover" loading="lazy" />
                                <div class="absolute inset-0 bg-linear-to-t from-primary/90 via-primary/30 to-transparent"></div>

                                <div
                                    class="absolute inset-x-5 bottom-5 rounded-2xl bg-surface-container-lowest/95 p-5 text-primary backdrop-blur-sm">
                                    <p class="text-xs font-bold tracking-[0.22em] uppercase text-secondary">
                                        {{ data_get($mayor, 'role', 'Alcaldía Municipal') }}</p>
                                    <h3 class="mt-2 text-2xl font-black tracking-tight">
                                        {{ data_get($mayor, 'name', 'Alcaldía Municipal') }}</h3>
                                </div>
                            </div>

                            <div class="space-y-8">
                                <div>
                                    <p class="text-xs font-bold tracking-[0.24em] uppercase text-secondary">Mensaje de la Alcaldía</p>
                                    <h2 class="mt-3 text-4xl font-black tracking-tight text-primary md:text-5xl">Estamos para servirle
                                    </h2>
                                </div>

                                <blockquote
                                    class="border-l-4 border-secondary pl-6 text-2xl font-light italic leading-10 text-primary md:text-3xl">
                                    “{{ data_get($mayor, 'quote', 'Trabajamos para que cada trámite sea claro, cercano y digno de la confianza ciudadana.') }}”
                                </blockquote>

                                <p class="max-w-2xl text-base leading-7 text-on-surface-variant">
                                    La gestión municipal se construye con transparencia, modernización y un trato humano que facilite la
                                    vida diaria de cada vecino.
                                </p>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    @foreach (data_get($mayor, 'stats', [['value' => '95%', 'label' => 'Gestión Realizada'], ['value' => '12K+', 'label' => 'Vecinos Atendidos']]) as $stat)
                                        <div class="rounded-2xl bg-surface-container-lowest p-6 ambient-shadow">
                                            <p class="text-4xl font-black tracking-tight text-secondary md:text-5xl">
                                                {{ data_get($stat, 'value') }}</p>
                                            <p class="mt-2 text-sm font-semibold uppercase tracking-[0.18em] text-on-surface-variant">
                                                {{ data_get($stat, 'label') }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </section> --}}

                @php
                    $beforeFooterBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'before_footer', []));
                @endphp

                @if ($beforeFooterBlocks->isNotEmpty())
                    <div class="mx-auto max-w-none py-10">
                        @foreach ($beforeFooterBlocks as $cmsBlock)
                            {!! $cmsBlock->content !!}
                        @endforeach
                    </div>
                @endif

                <section class="bg-primary text-on-primary">
                    <div
                        class="mx-auto flex max-w-none flex-col gap-8 px-0 py-20 md:flex-row md:items-center md:justify-between">
                        <div class="max-w-2xl">
                            <p class="text-xs font-bold tracking-[0.24em] uppercase text-secondary-container">Estamos para
                                servirle</p>
                            <h2 class="mt-3 text-4xl font-black tracking-tight text-white md:text-5xl">Atención municipal
                                clara, humana
                                y disponible.</h2>
                            <p class="mt-4 max-w-xl text-base leading-7 text-on-primary/80">
                                Acceda a los servicios institucionales o comuníquese con nuestro equipo para recibir
                                orientación
                                directa.
                            </p>
                        </div>

                        <div class="flex flex-col gap-4 sm:flex-row">
                            <a href="{{ route('tramites.index') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-secondary-container px-6 py-3 font-bold text-on-secondary-container transition-transform duration-300 hover:-translate-y-0.5">
                                Ver Trámites en Línea
                            </a>

                            <a href="tel:+50326097000"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-on-primary/20 bg-on-primary/5 px-6 py-3 font-bold text-on-primary transition-colors duration-300 hover:bg-on-primary/10">
                                <span class="material-symbols-outlined text-[20px]">call</span>
                                +503 2609 7000
                            </a>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="hidden lg:block lg:sticky lg:top-28 lg:self-start">
                @include('partials.page-nav-widget', ['compact' => true])
            </aside>
        </div>
    </section>
@endsection
