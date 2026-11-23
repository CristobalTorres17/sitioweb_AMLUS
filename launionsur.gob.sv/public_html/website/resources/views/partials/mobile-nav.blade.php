@php
    $navItems = [
        ['label' => 'Ciudadanos', 'route' => 'services.index'],
        ['label' => 'Trámites', 'route' => 'tramites.index'],
        ['label' => 'Distritos', 'route' => 'districts.index'],
        ['label' => 'Noticias', 'route' => 'events.index'],
        ['label' => 'Integridad', 'route' => 'integridad'],
        ['label' => 'Contacto', 'route' => 'contact.show'],
    ];

    $pageNavItems = collect($cmsNavigationPages ?? []);
@endphp

<header class="glass-nav fixed top-0 z-50 flex h-16 w-full items-center justify-between px-6 md:hidden">
    <button type="button" data-toggle-panel="mobile-drawer" aria-label="Abrir menú"
        class="inline-flex h-11 w-11 items-center justify-center rounded-full text-on-surface transition hover:bg-surface-container-low">
        <span class="material-symbols-outlined text-[28px]">menu</span>
    </button>

    <a href="{{ route('home') }}" class="text-xl font-black tracking-tighter text-primary">
        La Unión Sur
    </a>

    <button type="button" data-toggle-panel="mobile-search" aria-label="Buscar"
        class="inline-flex h-11 w-11 items-center justify-center rounded-full text-on-surface transition hover:bg-surface-container-low">
        <span class="material-symbols-outlined text-[24px]">search</span>
    </button>
</header>

<div id="mobile-drawer" data-open="false" class="fixed inset-0 z-40 hidden md:hidden">
    <div class="absolute inset-0 bg-primary/60" data-toggle-panel="mobile-drawer"></div>
    <nav class="absolute left-0 top-0 h-full w-72 bg-surface-container-lowest p-6 shadow-xl">
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-2xl font-black tracking-tighter text-primary">La Unión Sur</a>
            <button type="button" data-toggle-panel="mobile-drawer" aria-label="Cerrar menú"
                class="inline-flex h-10 w-10 items-center justify-center rounded-full text-on-surface transition hover:bg-surface-container-low">
                <span class="material-symbols-outlined text-[24px]">close</span>
            </button>
        </div>

        <div class="space-y-2">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}" @class([
                    'flex items-center justify-between rounded-lg px-3 py-3 text-sm font-semibold transition',
                    'bg-surface-container text-primary' => Route::is($item['route']),
                    'text-on-surface hover:bg-surface-container-low' => !Route::is(
                        $item['route']),
                ])>
                    <span>{{ $item['label'] }}</span>
                    <span class="material-symbols-outlined text-[20px] text-secondary">chevron_right</span>
                </a>
            @endforeach
        </div>

        @if ($pageNavItems->isNotEmpty())
            <div class="mt-8 border-t border-outline-variant pt-6">
                <p class="mb-3 text-xs font-black uppercase tracking-[0.22em] text-secondary">Páginas</p>

                <div class="flex flex-wrap gap-2">
                    @foreach ($pageNavItems as $item)
                        <a href="{{ $item['href'] }}" @class([
                            'rounded-full border px-3 py-2 text-sm font-semibold transition',
                            'border-primary bg-primary text-on-primary' => $item['active'] ?? false,
                            'border-outline-variant bg-surface-container-low text-on-surface hover:border-secondary hover:bg-secondary-container hover:text-on-secondary-container' => !(
                                $item['active'] ?? false
                            ),
                        ])>
                            {{ $item['title'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-8 space-y-3 border-t border-outline-variant pt-6 text-sm text-on-surface-variant">
            <p class="font-semibold text-on-surface">info@launionsur.gob.sv</p>
            <p>Avenida Gral Cabañas, La Unión</p>
        </div>
    </nav>
</div>

<div id="mobile-search" data-open="false" class="fixed inset-0 z-40 hidden md:hidden">
    <div class="absolute inset-0 bg-primary/60" data-toggle-panel="mobile-search"></div>
    <div class="absolute inset-x-0 top-0 rounded-b-2xl bg-surface-container-lowest p-6 shadow-xl">
        <div class="mb-4 flex items-center justify-between">
            <p class="text-lg font-bold text-primary">Buscar</p>
            <button type="button" data-toggle-panel="mobile-search" aria-label="Cerrar búsqueda"
                class="inline-flex h-10 w-10 items-center justify-center rounded-full text-on-surface transition hover:bg-surface-container-low">
                <span class="material-symbols-outlined text-[24px]">close</span>
            </button>
        </div>

        <form action="{{ route('search') }}" method="get" class="relative">
            <span
                class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-on-surface-variant">search</span>
            <input type="search" name="q" placeholder="Buscar noticias, servicios o páginas"
                class="w-full rounded-full bg-surface-container-low py-3 pl-10 pr-4 text-sm text-on-surface outline-none focus:bg-surface-container" />
        </form>
    </div>
</div>
