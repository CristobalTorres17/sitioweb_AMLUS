@extends('layouts.app')

@section('content')
    @php
        $resolveMediaUrl = fn($media, string $fallback): string => \App\Support\MediaUrlResolver::resolve(
            $media,
            $fallback,
        ) ?? $fallback;

        $heroFallback = asset('assets/images/resources/main-slider-img-1-1.png');

        // Determine content type and resolve hero image
        if ($isArticle) {
            $heroImageUrl = $resolveMediaUrl($event->media->first(), $heroFallback);
            $categoryDisplay = 'Noticia';
            $dateToDisplay = $event->published_at;
            $authorDisplay = 'Comunicaciones Municipales';
            $description = $event->excerpt;
        } else {
            $heroImageUrl = $resolveMediaUrl(data_get($event, 'featuredImage'), $heroFallback);
            $categoryLabel =
                data_get($event, 'metadata.legacy_event_type') ?:
                data_get($event, 'metadata.category') ?:
                data_get($event, 'metadata.tipo') ?:
                'Evento';
            $categoryDisplay = \Illuminate\Support\Str::headline($categoryLabel);
            $dateToDisplay = $event->event_date;
            $authorDisplay = $event->organizer ?? 'Comunicaciones Municipales';
            $description = data_get($event, 'description', 'Sin descripción disponible.');
        }

        $afterHeroBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'after_hero', []));
        $midContentBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'mid_content', []));
        $sidebarBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'sidebar', []));
        $beforeFooterBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'before_footer', []));

        $lead = \Illuminate\Support\Str::limit(strip_tags($description), 280);
        $content = data_get($event, 'content');
        $tags = collect(data_get($event, 'metadata.tags', []));

        if ($tags->isEmpty()) {
            $tags = collect(data_get($event, 'metadata.keywords', []));
        }

        if ($tags->isEmpty() && $categoryDisplay !== 'Noticias') {
            $tags = collect([$categoryDisplay]);
        }

        if ($tags->isNotEmpty() && is_string($tags->first())) {
            $tags = $tags->flatMap(
                fn($tag) => is_string($tag) ? preg_split('/\s*,\s*/', $tag, -1, PREG_SPLIT_NO_EMPTY) : [$tag],
            );
        }
    @endphp

    <section class="relative min-h-140 overflow-hidden">
        <img src="{{ $heroImageUrl }}" alt="{{ data_get($event, 'title') }}"
            class="absolute inset-0 h-full w-full object-cover object-center" fetchpriority="high" />
        <div class="absolute inset-0 bg-linear-to-t from-primary via-primary/40 to-transparent"></div>

        <div class="relative mx-auto flex min-h-140 max-w-screen-2xl items-end px-12 pb-12 pt-24 md:px-12 md:pb-16">
            <div class="max-w-4xl">
                <p
                    class="inline-flex rounded-full bg-secondary-container/95 px-4 py-1.5 text-xs font-bold tracking-[0.24em] uppercase text-on-secondary-container">
                    {{ $categoryDisplay }}
                </p>

                <h1 class="mt-6 text-5xl font-black tracking-tighter text-white md:text-7xl lg:text-7xl">
                    {{ $event->title }}
                </h1>

                <div class="mt-6 flex flex-wrap items-center gap-5 text-xs font-medium text-on-primary/85 md:text-sm">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                        <span>{{ optional($dateToDisplay)->format('d \d\e F, Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        <span>Por: {{ $authorDisplay }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">schedule</span>
                        <span>Lectura: {{ $readingTime }} min</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.page-nav-widget')

    @if ($afterHeroBlocks->isNotEmpty())
        @foreach ($afterHeroBlocks as $cmsBlock)
            {!! $cmsBlock->content !!}
        @endforeach
    @endif

    <section class="bg-background">
        <div class="mx-auto max-w-screen-2xl px-8 py-20 md:px-8 lg:py-20">
            <div class="grid grid-cols-1 gap-8 lg:items-start grid-article-layout">
                <aside class="hidden lg:block lg:space-y-4 lg:sticky lg:top-24">
                    <a href="{{ route('events.index') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-surface-container-lowest px-4 py-3 text-sm font-semibold text-primary ambient-shadow transition hover:bg-surface-container-low">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                        Volver a noticias
                    </a>

                    <div class="rounded-2xl bg-surface-container-lowest p-5 ambient-shadow">
                        <p class="text-xs font-bold tracking-[0.22em] uppercase text-secondary">Compartir</p>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <button type="button" data-share-native data-share-title="{{ $event->title }}"
                                data-share-url="{{ url()->current() }}"
                                class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-primary text-on-primary transition hover:bg-primary-container md:hidden"
                                aria-label="Compartir noticia">
                                <span class="material-symbols-outlined text-[20px]">share</span>
                            </button>

                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                target="_blank" rel="noreferrer"
                                class="hidden h-11 w-11 items-center justify-center rounded-full bg-surface-container-low text-primary transition hover:bg-surface-container md:inline-flex"
                                aria-label="Compartir en Facebook">
                                <span class="material-symbols-outlined text-[20px]">public</span>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($event->title . ' ' . url()->current()) }}"
                                target="_blank" rel="noreferrer"
                                class="hidden h-11 w-11 items-center justify-center rounded-full bg-surface-container-low text-primary transition hover:bg-surface-container md:inline-flex"
                                aria-label="Compartir por WhatsApp">
                                <span class="material-symbols-outlined text-[20px]">chat</span>
                            </a>
                            <a href="mailto:?subject={{ urlencode($event->title) }}&body={{ urlencode(url()->current()) }}"
                                class="hidden h-11 w-11 items-center justify-center rounded-full bg-surface-container-low text-primary transition hover:bg-surface-container md:inline-flex"
                                aria-label="Compartir por correo">
                                <span class="material-symbols-outlined text-[20px]">mail</span>
                            </a>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-primary-container p-6 text-on-primary ambient-shadow">
                        <p class="text-xs font-bold tracking-[0.22em] uppercase text-secondary-container">Suscribirse</p>
                        <h2 class="mt-3 text-xl font-black tracking-tight text-white">Reciba más noticias institucionales
                        </h2>
                        <p class="mt-3 text-sm leading-6 text-on-primary/75">Manténgase informado sobre obras, servicios y
                            avisos de interés ciudadano.</p>
                        <a href="{{ route('contact.show') }}"
                            class="mt-5 inline-flex w-full items-center justify-center rounded-lg bg-secondary-container px-5 py-3 text-sm font-bold text-on-secondary-container transition hover:-translate-y-0.5">
                            Suscribirme
                        </a>
                    </div>
                </aside>
                <article class="space-y-10">
                    <div class="space-y-6 rounded-3xl bg-surface-container-lowest p-6 ambient-shadow md:p-8">
                        @if ($lead)
                            <p
                                class="text-xl leading-relaxed text-on-surface/80 mb-12 font-medium italic border-l-4 border-secondary pl-8">
                                {{ $lead }}</p>
                        @endif

                        @if ($content)
                            <div
                                class="prose prose-slate prose-lg max-w-none [&_img]:my-6 [&_img]:w-full [&_img]:rounded-2xl [&_img]:object-cover [&_blockquote]:border-l-4 [&_blockquote]:border-secondary [&_blockquote]:pl-6 [&_blockquote]:text-xl [&_blockquote]:italic [&_blockquote]:text-primary [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:list-decimal [&_ol]:pl-6">
                                {!! $content !!}
                            </div>
                        @else
                            <p class="text-base leading-8 text-on-surface-variant">Sin contenido disponible.</p>
                        @endif

                        @if ($tags->isNotEmpty())
                            <div class="flex flex-wrap gap-3 pt-4">
                                @foreach ($tags as $tag)
                                    <span
                                        class="rounded-full bg-secondary-container px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-on-secondary-container">
                                        {{ \Illuminate\Support\Str::headline($tag) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @foreach ($midContentBlocks as $cmsBlock)
                        {!! $cmsBlock->content !!}
                    @endforeach
                </article>

                <aside class="hidden lg:block lg:space-y-4 lg:sticky lg:top-24">
                    <div class="rounded-2xl bg-surface-container-lowest p-6 ambient-shadow">
                        <p class="text-xs font-bold tracking-[0.22em] uppercase text-secondary">Detalles</p>

                        @if ($event->location)
                            <div class="mt-4 rounded-xl bg-surface-container-low p-4">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-on-surface-variant">Ubicación
                                </p>
                                <p class="mt-2 text-sm leading-6 text-primary">{{ $event->location }}</p>
                            </div>
                        @endif

                        <div class="mt-4 grid gap-3 text-sm text-on-surface-variant">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[20px] text-secondary">event</span>
                                <span>{{ optional($event->event_date)->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[20px] text-secondary">person</span>
                                <span>{{ $event->organizer ?? 'Comunicaciones Municipales' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-primary-container p-6 text-on-primary ambient-shadow">
                        <p class="text-xs font-bold tracking-[0.22em] uppercase text-secondary-container">Aviso</p>
                        <p class="mt-3 text-sm leading-6 text-on-primary/75">Comparta esta publicación para mantener a más
                            vecinos informados.</p>
                    </div>

                    @if ($sidebarBlocks->isNotEmpty())
                        @foreach ($sidebarBlocks as $cmsBlock)
                            <div class="rounded-2xl bg-surface-container-lowest p-6 ambient-shadow">
                                {!! $cmsBlock->content !!}
                            </div>
                        @endforeach
                    @endif
                </aside>
            </div>

            <section class="mt-16 lg:mt-20">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-xs font-bold tracking-[0.24em] uppercase text-secondary">También te puede interesar
                        </p>
                        <h2 class="mt-3 text-3xl font-black tracking-tight text-primary md:text-4xl">Noticias relacionadas
                        </h2>
                    </div>

                    <a href="{{ route('events.index') }}"
                        class="inline-flex items-center gap-2 font-semibold text-secondary transition hover:text-primary">
                        Ver todas las noticias
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($relatedEvents as $relatedEvent)
                        @php
                            $relatedImageUrl = $resolveMediaUrl(
                                data_get($relatedEvent, 'featuredImage') ?? data_get($relatedEvent, 'media.0'),
                                $heroFallback,
                            );
                            $relatedCategory = data_get($relatedEvent, 'excerpt')
                                ? 'Noticia'
                                : (data_get($relatedEvent, 'metadata.legacy_event_type') ?:
                                data_get($relatedEvent, 'metadata.category') ?:
                                data_get($relatedEvent, 'metadata.tipo') ?:
                                'Noticias');
                            $relatedCategoryDisplay = \Illuminate\Support\Str::headline($relatedCategory);
                            $relatedDate =
                                data_get($relatedEvent, 'event_date') ?? data_get($relatedEvent, 'published_at');
                            $relatedExcerpt = \Illuminate\Support\Str::limit(
                                strip_tags(
                                    data_get($relatedEvent, 'description') ?:
                                    data_get($relatedEvent, 'excerpt') ?:
                                    'Sin descripción disponible.',
                                ),
                                120,
                            );
                        @endphp

                        <article
                            class="overflow-hidden rounded-2xl bg-surface-container-lowest ambient-shadow transition-transform duration-300 hover:-translate-y-0.5">
                            <img src="{{ $relatedImageUrl }}" alt="{{ data_get($relatedEvent, 'title') }}"
                                class="h-44 w-full object-cover" loading="lazy" />

                            <div class="p-6">
                                <span
                                    class="inline-flex rounded-full bg-secondary-container px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-on-secondary-container">
                                    {{ $relatedCategoryDisplay }}
                                </span>
                                <h3 class="mt-4 text-lg font-bold text-primary">{{ data_get($relatedEvent, 'title') }}</h3>
                                <p class="mt-2 text-xs font-medium uppercase tracking-[0.16em] text-on-surface-variant">
                                    {{ optional($relatedDate)->format('d M Y') }}
                                </p>
                                <p class="mt-3 text-sm leading-6 text-on-surface-variant">{{ $relatedExcerpt }}</p>
                                <a href="{{ route('events.show', data_get($relatedEvent, 'slug')) }}"
                                    class="mt-5 inline-flex items-center gap-1 font-semibold text-secondary">
                                    Leer más
                                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                                </a>
                            </div>
                        </article>
                    @empty
                        <div
                            class="rounded-2xl bg-surface-container-lowest p-8 text-on-surface-variant ambient-shadow md:col-span-2 xl:col-span-3">
                            Todavía no hay noticias relacionadas.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </section>

    @if ($beforeFooterBlocks->isNotEmpty())
        @foreach ($beforeFooterBlocks as $cmsBlock)
            {!! $cmsBlock->content !!}
        @endforeach
    @endif
@endsection
