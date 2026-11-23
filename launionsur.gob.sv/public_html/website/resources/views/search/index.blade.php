@extends('layouts.app')

@section('content')
    @php
        $query = trim((string) ($query ?? request('q')));
        $results = collect($results ?? []);
    @endphp

    <section class="bg-background">
        <div class="mx-auto max-w-screen-2xl px-6 py-12 md:px-8 md:py-16">
            <div class="mx-auto max-w-3xl">
                <p class="text-xs font-bold tracking-[0.24em] uppercase text-secondary">Búsqueda</p>
                <h1 class="mt-3 text-4xl font-extrabold tracking-tight text-primary md:text-5xl">
                    {{ $query !== '' ? 'Resultados para: ' . $query : 'Buscar contenido' }}
                </h1>
                <p class="mt-4 text-lg leading-8 text-on-surface-variant">
                    {{ $query !== ''
                        ? 'Mostramos coincidencias publicadas en servicios, trámites, noticias, distritos y páginas del sitio.'
                        : 'Escribe una palabra clave para buscar servicios, trámites, noticias, distritos y páginas publicadas.' }}
                </p>

                <form method="GET" action="{{ route('search') }}"
                    class="mt-8 flex flex-col gap-3 rounded-2xl bg-surface-container-low p-4 shadow-sm md:flex-row md:items-center">
                    <label class="relative flex-1">
                        <span
                            class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[20px] text-on-surface-variant">search</span>
                        <input type="search" name="q" value="{{ $query }}"
                            placeholder="Buscar servicios, noticias, trámites, distritos o páginas"
                            class="w-full rounded-full bg-surface-container-lowest py-3 pl-12 pr-4 text-sm text-primary outline-none ring-1 ring-transparent transition placeholder:text-on-surface-variant focus:ring-secondary" />
                    </label>
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-full bg-primary px-6 py-3 text-sm font-bold text-on-primary transition hover:bg-primary-container">
                        Buscar
                    </button>
                </form>
            </div>

            <div class="mt-10">
                @if ($query === '')
                    <div
                        class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-8 text-on-surface-variant">
                        Escribe algo en el buscador para ver resultados.
                    </div>
                @elseif ($results->isEmpty())
                    <div
                        class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-8 text-on-surface-variant">
                        No encontramos resultados para "{{ $query }}".
                    </div>
                @else
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-primary">
                            {{ $count }} resultado{{ $count === 1 ? '' : 's' }}
                            encontrado{{ $count === 1 ? '' : 's' }}
                        </p>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($results as $result)
                            <article
                                class="overflow-hidden rounded-2xl bg-surface-container-lowest shadow-sm transition-transform duration-300 hover:-translate-y-0.5">
                                <div class="p-6">
                                    <div
                                        class="flex items-center justify-between gap-3 text-xs font-bold uppercase tracking-[0.18em] text-on-surface-variant">
                                        <span
                                            class="rounded-full bg-secondary-container px-3 py-1 text-[11px] text-on-secondary-container">
                                            {{ $result['type_label'] ?? ucfirst($result['type'] ?? 'resultado') }}
                                        </span>
                                        @if (!empty($result['date']))
                                            <span>{{ \Illuminate\Support\Carbon::parse($result['date'])->format('d M Y') }}</span>
                                        @endif
                                    </div>

                                    <h2 class="mt-4 text-lg font-bold text-primary">
                                        {{ $result['title'] }}
                                    </h2>

                                    <p class="mt-3 text-sm leading-6 text-on-surface-variant">
                                        {{ $result['excerpt'] }}
                                    </p>

                                    <div class="mt-4 flex flex-wrap gap-3 text-sm text-on-surface-variant">
                                        @if (!empty($result['location']))
                                            <span>{{ $result['location'] }}</span>
                                        @endif
                                        @if (!empty($result['organizer']))
                                            <span>{{ $result['organizer'] }}</span>
                                        @endif
                                    </div>

                                    @if (!empty($result['url']))
                                        <a href="{{ $result['url'] }}"
                                            class="mt-5 inline-flex items-center gap-1 font-semibold text-secondary">
                                            Ver resultado
                                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                                        </a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
