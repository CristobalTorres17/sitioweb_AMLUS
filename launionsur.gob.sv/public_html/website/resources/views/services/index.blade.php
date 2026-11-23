@extends('layouts.app')

@section('content')
    @php
        $activeCategory = request('categoria');
        $heroImage = asset('assets/images/backgrounds/slider-1-1.jpg');
        $categoryList = collect($categories ?? [])
            ->filter()
            ->values();

        $serviceIcon = function ($service): string {
            return data_get($service, 'icon') ?: data_get($service, 'category.icon') ?: 'description';
        };
    @endphp

    <section class="relative overflow-hidden bg-primary text-on-primary pt-32 pb-16">
        <div class="absolute inset-0">
            <img src="{{ $heroImage }}" alt="Servicios Municipales" class="h-full w-full object-cover"
                fetchpriority="high" />
            <div class="absolute inset-0 bg-linear-to-r from-primary via-primary/80 to-primary/30"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-screen-2xl px-6 md:px-8">
            <div class="max-w-3xl">
                <p
                    class="inline-flex rounded-full bg-secondary-container/95 px-4 py-1.5 text-xs font-bold tracking-[0.24em] uppercase text-on-secondary-container">
                    Servicios y atención ciudadana
                </p>

                <h1 class="mt-6 text-5xl font-black tracking-tighter text-white md:text-6xl lg:text-7xl">
                    Servicios Municipales
                </h1>

                <p class="mt-5 max-w-2xl text-base leading-7 text-on-primary/75 md:text-lg">
                    Acceda a los servicios municipales de forma digital. Gestión ágil, clara y centralizada para la
                    comunidad.
                </p>
            </div>
        </div>
    </section>

    <section class="bg-background">
        <div
            class="mx-auto max-w-screen-2xl px-6 py-16 md:px-8 lg:grid lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-8 lg:items-start">
            <div class="min-w-0 space-y-10">
                <div class="mb-12 md:flex md:items-center md:justify-between">
                    <div class="flex flex-wrap gap-3 mb-4 md:mb-0">
                        <a href="{{ route('services.index') }}"
                            class="chip {{ !$activeCategory ? 'chip-active' : 'chip-idle' }}">
                            Todos los Servicios
                        </a>

                        @foreach ($categoryList as $category)
                            <a href="{{ route('services.index', ['categoria' => data_get($category, 'slug')]) }}"
                                class="chip {{ $activeCategory === data_get($category, 'slug') ? 'chip-active' : 'chip-idle' }}">
                                {{ data_get($category, 'name') }}
                            </a>
                        @endforeach
                    </div>

                    <form method="get" action="{{ route('services.index') }}" class="w-full md:w-80 md:shrink-0">
                        <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                        <div class="flex gap-2">
                            <input name="q" value="{{ request('q') }}" type="search"
                                placeholder="Buscar servicios, descripción..."
                                class="w-full rounded-lg border px-4 py-2 text-sm" />
                            <button type="submit"
                                class="rounded-lg bg-secondary-container px-4 py-2 text-on-secondary-container font-bold">Buscar</button>
                        </div>
                    </form>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                    @forelse ($services as $service)
                        @php
                            $iconName = $serviceIcon($service);
                            $serviceDescription = \Illuminate\Support\Str::limit(
                                strip_tags(data_get($service, 'description', 'Sin descripción disponible.')),
                                120,
                            );
                        @endphp

                        <article
                            class="rounded-xl bg-surface-container-lowest p-6 ambient-shadow transition-transform duration-300 hover:-translate-y-1">
                            <span
                                class="mb-4 block text-3xl text-primary-container material-symbols-outlined">{{ $iconName }}</span>
                            <h3 class="mb-2 text-lg font-bold text-primary">{{ data_get($service, 'title') }}</h3>
                            <p class="mb-4 text-sm leading-6 text-on-surface-variant line-clamp-2">
                                {{ $serviceDescription }}
                            </p>
                            <a href="{{ route('services.show', data_get($service, 'slug')) }}"
                                class="inline-flex items-center gap-1 text-sm font-semibold text-secondary">
                                VER REQUISITOS
                                <span class="material-symbols-outlined text-base">arrow_forward</span>
                            </a>
                        </article>
                    @empty
                        <div
                            class="rounded-xl bg-surface-container-lowest p-8 text-on-surface-variant ambient-shadow sm:col-span-2 xl:col-span-4">
                            Todavía no hay servicios publicados.
                        </div>
                    @endforelse
                </div>

                <section class="mt-12 rounded-2xl bg-primary-container p-8 ambient-shadow md:p-10">
                    <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                        <div class="max-w-2xl">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-secondary-container text-on-secondary-container">
                                    <span class="material-symbols-outlined text-[24px]">support_agent</span>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-on-primary">¿No encuentra lo que busca?</h2>
                                    <p class="mt-1 text-sm text-on-primary/70">Nuestro asistente virtual está disponible
                                        24/7
                                        para guiarle en sus servicios.</p>
                                </div>
                            </div>
                        </div>

                        <button type="button"
                            class="inline-flex items-center justify-center rounded-lg bg-secondary-container px-6 py-3 font-bold whitespace-nowrap text-on-secondary-container transition hover:-translate-y-0.5">
                            Iniciar Chat de Soporte
                        </button>
                    </div>
                </section>
            </div>

            <aside class="hidden lg:block lg:sticky lg:top-28">
                @include('partials.page-nav-widget', ['compact' => true])
            </aside>
        </div>
    </section>
@endsection
