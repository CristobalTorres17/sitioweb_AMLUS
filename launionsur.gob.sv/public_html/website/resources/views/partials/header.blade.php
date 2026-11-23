@php
    $navItems = [
        ['label' => 'Inicio', 'route' => 'home'],
        ['label' => 'Servicios', 'route' => 'services.index'],
        ['label' => 'Trámites', 'route' => 'tramites.index'],
        ['label' => 'Distritos', 'route' => 'districts.index'],
        ['label' => 'Noticias', 'route' => 'events.index'],
        ['label' => 'Integridad', 'route' => 'integridad'],
        ['label' => 'Contacto', 'route' => 'contact.show'],
    ];
@endphp

<header class="glass-nav fixed top-0 z-50 hidden w-full md:block">
    <nav class="mx-auto flex max-w-screen-2xl items-center justify-between gap-8 px-8 py-2">
        <a href="{{ route('home') }}" class="text-2xl font-black tracking-tighter text-primary">
            <span class="inline-flex items-center gap-2">
                <img src="{{ asset('assets/images/launionsur.png') }}" alt="Logo" class="h-14">
            </span>
        </a>

        <div class="flex min-w-0 flex-1 items-center justify-center gap-6 text-sm font-semibold text-on-surface-variant">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}" @class([
                    'whitespace-nowrap pb-1 transition-colors hover:text-secondary',
                    'text-secondary border-b-2 border-secondary' => Route::is($item['route']),
                ])>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-4">
            <form action="{{ route('search') }}" method="get" class="relative">
                <span
                    class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-on-surface-variant">
                    search
                </span>
                <input type="search" name="q" placeholder="Buscar"
                    class="w-56 rounded-full bg-surface-container-low py-2 pl-10 pr-4 text-sm text-on-surface outline-none transition focus:bg-surface-container" />
            </form>

            {{-- <a href="#"
                class="rounded-lg bg-primary-container px-6 py-2.5 text-sm font-bold text-on-primary transition-all duration-300 hover:bg-secondary">
                Portal Tributario
            </a> --}}
        </div>
    </nav>
</header>

@include('partials.mobile-nav')
