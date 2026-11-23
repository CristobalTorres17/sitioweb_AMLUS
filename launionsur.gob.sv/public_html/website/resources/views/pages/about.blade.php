@extends('layouts.app')

@section('content')
    @php
        $heroImage = asset('assets/images/backgrounds/slider-1-1.jpg');
        $mayorImage = asset('assets/images/resources/main-slider-img-1-1.png');

        $history = [
            [
                'year' => '1998',
                'title' => 'Consolidación institucional',
                'description' => 'Se fortalecen los servicios municipales y la atención cercana a la ciudadanía.',
                'image' => $heroImage,
            ],
            [
                'year' => '2008',
                'title' => 'Modernización administrativa',
                'description' => 'Se amplían los canales de atención y se reorganizan procesos para mayor eficiencia.',
                'image' => $mayorImage,
            ],
            [
                'year' => '2016',
                'title' => 'Transformación digital',
                'description' =>
                    'La municipalidad impulsa trámites más accesibles y comunicación digital con la comunidad.',
                'image' => $heroImage,
            ],
            [
                'year' => '2024',
                'title' => 'Gobierno abierto',
                'description' =>
                    'La Unión Sur refuerza la transparencia, la participación y la excelencia en el servicio.',
                'image' => $mayorImage,
            ],
        ];

        $projects = [
            [
                'title' => 'Mejoramiento vial urbano',
                'description' => 'Obras de pavimentación, drenaje y señalización para mejorar la movilidad local.',
                'image' => $heroImage,
            ],
            [
                'title' => 'Centro de atención ciudadana',
                'description' => 'Espacio modernizado para facilitar trámites, asesorías y acompañamiento.',
                'image' => $mayorImage,
            ],
            [
                'title' => 'Programa de espacios públicos',
                'description' => 'Intervenciones para fortalecer convivencia, seguridad y uso comunitario.',
                'image' => $heroImage,
            ],
        ];
    @endphp

    <section class="relative overflow-hidden bg-primary text-on-primary pt-32 pb-20">
        <div class="absolute inset-0">
            <img src="{{ $heroImage }}" alt="Institución La Unión Sur" class="h-full w-full object-cover"
                fetchpriority="high" />
            <div class="absolute inset-0 bg-linear-to-r from-primary via-primary/80 to-primary/20"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-screen-2xl px-6 md:px-8">
            <div class="max-w-4xl">
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-secondary-container/95 px-4 py-1.5 text-on-secondary-container shadow-sm">
                    <img src="{{ asset('assets/images/lusur.png') }}" alt="Logo" class="h-10">
                    <span class="text-xs font-bold tracking-[0.24em] uppercase">ALCALDÍA LA UNIÓN SUR</span>
                </div>

                <h1 class="mt-6 text-5xl font-black tracking-tighter text-white md:text-6xl lg:text-7xl">
                    Quiénes Somos y nuestra gestión institucional
                </h1>

                <p class="mt-6 max-w-2xl text-base leading-7 text-on-primary/80 md:text-lg">
                    La Municipalidad de La Unión Sur trabaja con cercanía, transparencia y una visión de servicio público
                    orientada al bienestar de cada comunidad.
                </p>
            </div>
        </div>
    </section>

    @include('partials.page-nav-widget')

    @php
        $afterHeroBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'after_hero', []));
    @endphp
    @if ($afterHeroBlocks->isNotEmpty())
        @foreach ($afterHeroBlocks as $cmsBlock)
            {!! $cmsBlock->content !!}
        @endforeach
    @endif

    @php
        $midContentBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'mid_content', []))->concat(
            collect(data_get($cmsPageBlocksByZone ?? [], 'content', [])),
        );
    @endphp
    @if ($midContentBlocks->isNotEmpty())
        @foreach ($midContentBlocks as $cmsBlock)
            <section class="bg-background">
                <div class="mx-auto max-w-screen-2xl px-6 py-16 md:px-8 lg:py-20">
                    {!! $cmsBlock->content !!}
                </div>
            </section>
        @endforeach
    @endif


    <section class="bg-background">
        <div class="mx-auto max-w-screen-2xl px-6 py-16 md:px-8 lg:py-20">
            {{-- <div class="grid gap-10 lg:grid-cols-[minmax(0,360px),minmax(0,1fr)] lg:items-start">
                <div class="relative overflow-hidden rounded-2xl bg-surface-container-lowest ambient-shadow">
                    <img src="{{ $mayorImage }}" alt="Alcaldía Municipal" class="h-105 w-full object-cover"
                        loading="lazy" />
                    <div
                        class="absolute inset-x-0 bottom-0 bg-linear-to-t from-primary via-primary/80 to-transparent p-6 text-on-primary">
                        <span
                            class="inline-flex rounded-full bg-secondary-container px-4 py-1.5 text-xs font-bold uppercase tracking-[0.22em] text-on-secondary-container">
                            Alcaldía Municipal
                        </span>
                        <h2 class="mt-4 text-2xl font-black tracking-tight">Ing. Carlos Molina</h2>
                        <p class="mt-1 text-sm text-on-primary/75">Alcalde Municipal de La Unión Sur</p>
                    </div>
                </div>

                <div class="rounded-2xl bg-surface-container-lowest p-6 md:p-8 ambient-shadow">
                    <p class="text-xs font-bold uppercase tracking-[0.24em] text-secondary">Mensaje del Alcalde</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tighter text-primary md:text-4xl">
                        Una municipalidad cercana, moderna y comprometida con su gente.
                    </h2>
                    <div class="mt-6 space-y-5 text-base leading-7 text-on-surface-variant">
                        <p>
                            Nuestro trabajo se guía por una convicción simple: cada trámite debe ser más claro, más ágil y
                            más humano. La transformación institucional no es únicamente tecnológica, también es un
                            compromiso permanente con la confianza ciudadana.
                        </p>
                        <p>
                            La Unión Sur avanza con una visión de servicio que prioriza la transparencia, el orden,
                            la participación y el respeto por el tiempo de cada vecino. Ese enfoque nos impulsa a mejorar
                            la atención, fortalecer la infraestructura y abrir más espacios de comunicación directa.
                        </p>
                        <p>
                            Trabajamos para que la municipalidad sea una aliada de la comunidad y no solo una entidad de
                            gestión. Ese es el estándar que queremos sostener en cada decisión pública.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-16 grid gap-8 md:grid-cols-2">
                <div class="rounded-2xl bg-surface-container-lowest p-8 ambient-shadow">
                    <span class="material-symbols-outlined mb-4 block text-3xl text-secondary">my_location</span>
                    <h3 class="mb-4 text-xl font-bold text-primary">Misión</h3>
                    <p class="leading-7 text-on-surface-variant">
                        Brindar servicios municipales eficientes, transparentes y cercanos, promoviendo el desarrollo
                        integral de La Unión Sur mediante una gestión responsable, participativa y orientada al bienestar
                        de toda la ciudadanía.
                    </p>
                </div>

                <div class="rounded-2xl bg-surface-container-lowest p-8 ambient-shadow">
                    <span class="material-symbols-outlined mb-4 block text-3xl text-secondary">visibility</span>
                    <h3 class="mb-4 text-xl font-bold text-primary">Visión</h3>
                    <p class="leading-7 text-on-surface-variant">
                        Ser una municipalidad moderna, innovadora y abierta, reconocida por su capacidad de servir con
                        excelencia, impulsar el progreso local y construir confianza pública a través de una gestión
                        transparente y humana.
                    </p>
                </div>
            </div>


            <section class="mt-16">
                <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.24em] text-secondary">Nuestra Historia</p>
                        <h2 class="mt-2 text-3xl font-black tracking-tighter text-primary md:text-4xl">
                            Hitos que han marcado nuestro crecimiento institucional
                        </h2>
                    </div>
                    <p class="max-w-2xl text-sm leading-6 text-on-surface-variant md:text-base">
                        Una cronología breve de avances administrativos, tecnológicos y de atención ciudadana que han
                        impulsado la evolución de la municipalidad.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($history as $item)
                        <article
                            class="relative overflow-hidden rounded-xl bg-primary-container text-on-primary ambient-shadow">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}"
                                class="absolute inset-0 h-full w-full object-cover opacity-20" loading="lazy" />
                            <div class="relative z-10 p-6">
                                <span class="text-3xl font-bold text-secondary-container">{{ $item['year'] }}</span>
                                <h3 class="mt-2 text-xl font-bold">{{ $item['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-on-primary/75">{{ $item['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="mt-16">
                <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.24em] text-secondary">Proyectos Emblemáticos</p>
                        <h2 class="mt-2 text-3xl font-black tracking-tighter text-primary md:text-4xl">
                            Obras y programas que reflejan nuestra gestión
                        </h2>
                    </div>
                    <a href="{{ route('projects') }}"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-secondary">
                        Ver todos los proyectos
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <article
                            class="overflow-hidden rounded-xl bg-surface-container-lowest ambient-shadow transition-transform duration-300 hover:-translate-y-1">
                            <img src="{{ $project['image'] }}" alt="{{ $project['title'] }}"
                                class="h-52 w-full object-cover" loading="lazy" />
                            <div class="p-6">
                                <p class="text-xs font-bold uppercase tracking-[0.22em] text-secondary">Proyecto
                                    institucional</p>
                                <h3 class="mt-3 text-xl font-bold text-primary">{{ $project['title'] }}</h3>
                                <p class="mt-3 text-sm leading-6 text-on-surface-variant">{{ $project['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section> --}}

            @php
                $beforeFooterBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'before_footer', []));
            @endphp
            @if ($beforeFooterBlocks->isNotEmpty())
                @foreach ($beforeFooterBlocks as $cmsBlock)
                    {!! $cmsBlock->content !!}
                @endforeach
            @endif

            <section class="mt-16 rounded-2xl bg-primary px-8 py-10 text-on-primary ambient-shadow md:px-10 md:py-12">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold uppercase tracking-[0.24em] text-secondary-container">Gestión y
                            transparencia</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tighter text-white md:text-4xl">
                            ¿Deseas saber más sobre nuestra gestión?
                        </h2>
                        <p class="mt-4 text-sm leading-6 text-on-primary/75 md:text-base">
                            Conozca nuestros proyectos, iniciativas y documentos institucionales para entender cómo
                            construimos un servicio público más abierto y confiable.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-secondary-container px-6 py-3 font-bold text-on-secondary-container transition hover:-translate-y-0.5">
                            Ver Transparencia
                        </a>
                        <a href="{{ route('contact.show') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-on-primary/20 px-6 py-3 font-bold text-on-primary transition hover:bg-on-primary/10">
                            Contactar Alcaldía
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </section>
@endsection
