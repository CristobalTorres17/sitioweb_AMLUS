@php
    $pageNavItems = collect($pages ?? ($cmsNavigationPages ?? []))
        ->filter()
        ->values();
    $compact = (bool) ($compact ?? false);
    $pageCount = $pageNavItems->count();
    $pageLabel = $pageCount === 1 ? 'página' : 'páginas';
    $singlePageItem = $pageNavItems->first();
    $pageSlideSize = $compact ? 3 : 4;
    $pageSlides =
        $pageCount > $pageSlideSize ? $pageNavItems->chunk($pageSlideSize)->values() : collect([$pageNavItems]);
    $showSlider = $pageSlides->count() > 1;

    $cardClasses = function (bool $active): string {
        return $active
            ? 'group relative flex items-center gap-4 overflow-hidden rounded-2xl border border-secondary bg-linear-to-br from-primary via-primary to-primary/90 px-5 py-4.5 text-left text-on-primary shadow-lg shadow-primary/30 transition duration-300 ease-out'
            : 'group relative flex items-center gap-4 overflow-hidden rounded-2xl border border-primary/30 bg-primary/15 px-5 py-4.5 text-left text-on-primary shadow-md transition duration-300 ease-out hover:-translate-y-1 hover:border-primary/50 hover:bg-primary/20 hover:shadow-lg hover:shadow-primary/20';
    };

    $iconClasses = function (bool $active): string {
        return $active
            ? 'inline-flex h-12 w-12 flex-none items-center justify-center rounded-xl bg-white/15 text-on-primary shadow-sm transition duration-300 scale-100'
            : 'inline-flex h-12 w-12 flex-none items-center justify-center rounded-xl bg-primary/25 text-on-primary transition duration-300 group-hover:scale-110 group-hover:bg-primary/40';
    };

    $arrowClasses = function (bool $active): string {
        return $active
            ? 'inline-flex h-8 w-8 flex-none items-center justify-center rounded-lg bg-white/15 text-on-primary transition duration-300'
            : 'inline-flex h-8 w-8 flex-none items-center justify-center rounded-lg bg-primary/20 text-on-primary transition duration-300 group-hover:translate-x-1 group-hover:bg-primary/40';
    };

    $eyebrowClasses = function (bool $active): string {
        return $active
            ? 'block text-xs font-extrabold uppercase tracking-widest text-on-primary/75'
            : 'block text-xs font-extrabold uppercase tracking-widest text-on-primary/60';
    };

    $titleClasses = function (bool $active): string {
        return $active
            ? 'mt-1.5 block text-base font-bold leading-snug text-on-primary'
            : 'mt-1.5 block text-base font-bold leading-snug text-on-primary group-hover:text-on-primary';
    };

    $descriptionClasses = function (bool $active): string {
        return $active
            ? 'mt-1 block text-sm leading-5 text-on-primary/70'
            : 'mt-1 block text-sm leading-5 text-on-primary/60 group-hover:text-on-primary/70';
    };
@endphp

@if ($pageNavItems->isNotEmpty())
    <div @class([
        'w-full space-y-5 rounded-3xl bg-linear-to-br from-primary via-primary to-primary/95 p-6',
        'hidden lg:block' => !$compact,
    ]) aria-label="Navegación de páginas del CMS">
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 text-on-primary">
                    <span class="material-symbols-outlined text-base">pages</span>
                </span>
                <h2 class="text-xl font-bold tracking-tight text-on-primary">Páginas institucionales</h2>
            </div>
            <p class="text-sm leading-6 text-on-primary/80">
                Explora el contenido institucional disponible en el portal.
            </p>
            <div class="flex items-center gap-3 pt-1">
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wider text-on-primary">
                    <span class="inline-block h-2 w-2 rounded-full bg-secondary animate-pulse"></span>
                    {{ $pageCount }} {{ $pageLabel }}
                </div>
            </div>
        </div>

        @if ($showSlider)
            <div class="grid grid-cols-[auto_minmax(0,1fr)_auto] grid-rows-[auto_auto] items-center gap-x-3.5 gap-y-3">
                <button type="button" data-page-slider-prev
                    class="row-span-2 inline-flex h-9 w-9 items-center justify-center self-center rounded-lg border border-white/20 bg-white/10 text-on-primary shadow-md transition duration-300 hover:-translate-y-1 hover:border-white/40 hover:bg-white/20 hover:shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white disabled:pointer-events-none disabled:opacity-40"
                    aria-label="Ver grupo anterior">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </button>

                <div data-page-slider class="col-start-2 row-start-1 min-w-0">
                    <div data-page-slider-track
                        class="nav-carousel flex snap-x snap-mandatory overflow-x-auto scroll-smooth">
                        @foreach ($pageSlides as $slideIndex => $slide)
                            <section data-page-slider-slide class="min-w-full shrink-0 snap-start">
                                <div class="grid gap-3.5">
                                    @foreach ($slide as $item)
                                        @php
                                            $isActive = (bool) data_get($item, 'active', false);
                                            $itemHref = data_get($item, 'href');
                                            $itemTitle = data_get($item, 'title');
                                        @endphp

                                        <a href="{{ $itemHref }}" class="{{ $cardClasses($isActive) }}"
                                            @if ($isActive) aria-current="page" @endif>
                                            <span
                                                class="absolute inset-y-0 left-0 w-1 rounded-r-lg {{ $isActive ? 'bg-secondary' : 'bg-transparent group-hover:bg-secondary/40' }}"></span>

                                            <span class="relative flex min-w-0 flex-1 items-center gap-3.5">
                                                <span class="{{ $iconClasses($isActive) }}">
                                                    <span
                                                        class="material-symbols-outlined text-[20px]">description</span>
                                                </span>

                                                <span class="min-w-0 flex-1">
                                                    <span class="{{ $eyebrowClasses($isActive) }}">
                                                        Página
                                                    </span>
                                                    <span class="{{ $titleClasses($isActive) }}">
                                                        {{ $itemTitle }}
                                                    </span>
                                                </span>
                                            </span>

                                            <span class="{{ $arrowClasses($isActive) }}">
                                                <span class="material-symbols-outlined text-[16px]">arrow_outward</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>
                </div>

                <button type="button" data-page-slider-next
                    class="row-span-2 inline-flex h-9 w-9 items-center justify-center self-center rounded-lg border border-white/20 bg-white/10 text-on-primary shadow-md transition duration-300 hover:-translate-y-1 hover:border-white/40 hover:bg-white/20 hover:shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white disabled:pointer-events-none disabled:opacity-40"
                    aria-label="Ver grupo siguiente">
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>

                    <div class="col-start-2 row-start-2 flex items-center justify-start gap-1.5">
                        @foreach ($pageSlides as $slideIndex => $slide)
                            <button type="button" data-page-slider-dot data-page-slider-target="{{ $slideIndex }}"
                                class="h-2 w-5 rounded-full bg-white/30 transition duration-300 first:bg-white"
                                aria-label="Ir al grupo {{ $slideIndex + 1 }}"></button>
                        @endforeach
                    </div>
            </div>
        @elseif ($singlePageItem)
            @php
                $singlePageHref = data_get($singlePageItem, 'href');
                $singlePageTitle = data_get($singlePageItem, 'title');
            @endphp

            <a href="{{ $singlePageHref }}"
                class="group block overflow-hidden rounded-2xl border border-white/20 bg-linear-to-br from-primary/90 to-primary/70 shadow-lg shadow-primary/30 transition duration-300 hover:-translate-y-1 hover:border-white/40 hover:shadow-xl hover:shadow-primary/40">
                <div class="flex items-center gap-4 px-5 py-5">
                    <span
                        class="inline-flex h-14 w-14 flex-none items-center justify-center rounded-xl bg-white/20 text-on-primary shadow-lg shadow-primary/20 transition duration-300 group-hover:scale-110">
                        <span class="material-symbols-outlined text-[24px]">description</span>
                    </span>

                    <span class="min-w-0 flex-1">
                        <span class="block text-xs font-extrabold uppercase tracking-widest text-on-primary/70">
                            Página disponible
                        </span>
                        <span class="mt-1.5 block text-lg font-bold leading-6 text-on-primary">
                            {{ $singlePageTitle }}
                        </span>
                        <span class="mt-1 block text-sm leading-5 text-on-primary/60">
                            Accede al contenido institucional.
                        </span>
                    </span>

                    <span
                        class="inline-flex h-9 w-9 flex-none items-center justify-center rounded-lg bg-white/15 text-on-primary transition duration-300 group-hover:translate-x-1 group-hover:bg-white/25">
                        <span class="material-symbols-outlined text-[18px]">arrow_outward</span>
                    </span>
                </div>

                <div class="h-1 bg-linear-to-r from-transparent via-white/40 to-transparent"></div>
            </a>
        @else
            <div class="grid gap-3.5">
                @foreach ($pageNavItems as $item)
                    @php
                        $isActive = (bool) data_get($item, 'active', false);
                        $itemHref = data_get($item, 'href');
                        $itemTitle = data_get($item, 'title');
                    @endphp

                    <a href="{{ $itemHref }}" class="{{ $cardClasses($isActive) }}"
                        @if ($isActive) aria-current="page" @endif>
                        <span
                            class="absolute inset-y-0 left-0 w-1 rounded-r-lg {{ $isActive ? 'bg-secondary' : 'bg-transparent group-hover:bg-secondary/40' }}"></span>

                        <span class="relative flex min-w-0 flex-1 items-center gap-3.5">
                            <span class="{{ $iconClasses($isActive) }}">
                                <span class="material-symbols-outlined text-[20px]">description</span>
                            </span>

                            <span class="min-w-0 flex-1">
                                <span class="{{ $eyebrowClasses($isActive) }}">
                                    Página
                                </span>
                                <span class="{{ $titleClasses($isActive) }}">
                                    {{ $itemTitle }}
                                </span>
                            </span>
                        </span>

                        <span class="{{ $arrowClasses($isActive) }}">
                            <span class="material-symbols-outlined text-[16px]">arrow_outward</span>
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endif
