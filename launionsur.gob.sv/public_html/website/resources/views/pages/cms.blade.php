@extends('layouts.app')

@section('meta_description', $page->meta_description ?: $page->description ?: 'Página informativa de La Unión Sur.')
@section('canonical_url', route('pages.show', ['slug' => $page->slug]))

@section('content')
    @php
        $afterHeroBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'after_hero', []));
        $midContentBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'mid_content', []))->concat(
            collect(data_get($cmsPageBlocksByZone ?? [], 'content', [])),
        );
        $beforeFooterBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'before_footer', []));
    @endphp

    <section class="relative overflow-hidden bg-primary text-on-primary pt-32 pb-20">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-linear-to-br from-primary via-primary/95 to-primary-container/75"></div>
            <div class="absolute -top-24 right-0 h-80 w-80 rounded-full bg-secondary/15 blur-3xl"></div>
            <div class="absolute -bottom-28 left-0 h-96 w-96 rounded-full bg-secondary-container/15 blur-3xl"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-screen-2xl px-6 md:px-8">
            <div class="max-w-4xl">
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-secondary-container/95 px-4 py-1.5 text-xs font-bold tracking-[0.24em] uppercase text-on-secondary-container shadow-sm">
                    Página institucional
                </div>

                <h1 class="mt-6 text-5xl font-black tracking-tighter text-white md:text-6xl lg:text-7xl">
                    {{ $page->title }}
                </h1>

                @if ($page->description)
                    <p class="mt-6 max-w-3xl text-base leading-7 text-on-primary/80 md:text-lg">
                        {{ $page->description }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    <section class="bg-background">
        <div
            class="mx-auto max-w-screen-2xl px-6 py-12 md:px-8 lg:grid lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-8 lg:items-start lg:py-16">
            <div class="min-w-0 space-y-10">
                @if ($afterHeroBlocks->isNotEmpty())
                    <div class="space-y-6">
                        @foreach ($afterHeroBlocks as $cmsBlock)
                            {!! $cmsBlock->content !!}
                        @endforeach
                    </div>
                @endif

                @if (filled($page->content))
                    <article class="rounded-3xl bg-surface-container-lowest p-6 ambient-shadow md:p-8">
                        {!! $page->content !!}
                    </article>
                @endif

                @if ($midContentBlocks->isNotEmpty())
                    <div class="space-y-6">
                        @foreach ($midContentBlocks as $cmsBlock)
                            <article class="rounded-3xl bg-surface-container-lowest p-6 ambient-shadow md:p-8">
                                {!! $cmsBlock->content !!}
                            </article>
                        @endforeach
                    </div>
                @endif

                @if ($beforeFooterBlocks->isNotEmpty())
                    <div class="space-y-6">
                        @foreach ($beforeFooterBlocks as $cmsBlock)
                            {!! $cmsBlock->content !!}
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="hidden lg:block lg:sticky lg:top-28">
                @include('partials.page-nav-widget', ['compact' => true])
            </aside>
        </div>
    </section>
@endsection
