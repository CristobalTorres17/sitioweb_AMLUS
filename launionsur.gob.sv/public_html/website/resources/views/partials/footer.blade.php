@php
    $footerBlocks = collect($cmsFooterBlocks ?? []);
@endphp

@if ($footerBlocks->isNotEmpty())
    <div class="border-b border-on-primary/10 bg-primary/95">
        <div class="mx-auto max-w-screen-2xl px-6 py-16 md:px-8">
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($footerBlocks as $cmsBlock)
                    <div class="rounded-2xl border border-on-primary/10 bg-on-primary/5 p-6">
                        {!! $cmsBlock->content !!}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<footer class="bg-primary text-on-primary">
    <div class="mx-auto max-w-screen-2xl px-6 py-16 md:px-8">
        <div class="grid gap-10 md:grid-cols-2 xl:grid-cols-4">
            <div class="md:col-span-2 xl:col-span-1">
                <a href="{{ route('home') }}" class="text-3xl font-black tracking-tighter text-secondary-container">
                    La Unión Sur
                </a>
                <p class="mt-4 max-w-sm text-sm leading-6 text-on-primary/80">
                    Municipalidad de La Unión Sur. Gestión cercana, servicios digitales y atención transparente para
                    nuestra comunidad.
                </p>

                <div class="mt-6 flex items-center gap-3">
                    <a href="{{ route('home') }}"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-on-primary/10 text-on-primary/80 transition hover:bg-on-primary/10 hover:text-on-primary">
                        <span class="material-symbols-outlined text-[20px]">language</span>
                    </a>
                    <a href="#"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-on-primary/10 text-on-primary/80 transition hover:bg-on-primary/10 hover:text-on-primary">
                        <span class="material-symbols-outlined text-[20px]">rss_feed</span>
                    </a>
                    <a href="mailto:info@launionsur.gob.sv"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-on-primary/10 text-on-primary/80 transition hover:bg-on-primary/10 hover:text-on-primary">
                        <span class="material-symbols-outlined text-[20px]">mail</span>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-widest text-on-primary/50">Servicios</h3>
                <ul class="mt-4 space-y-3 text-sm text-on-primary/80">
                    <li><a href="{{ route('services.index') }}" class="transition hover:text-on-primary">Servicios
                            Municipales</a></li>
                    <li><a href="{{ route('tramites.index') }}" class="transition hover:text-on-primary">Trámites en
                            Línea</a></li>
                    <li><a href="{{ route('tramites.index') }}"
                            class="transition hover:text-on-primary">Certificaciones</a></li>
                    <li><a href="{{ route('tramites.index') }}" class="transition hover:text-on-primary">Permisos</a>
                    </li>
                    <li><a href="{{ route('events.index') }}" class="transition hover:text-on-primary">Noticias</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-widest text-on-primary/50">Institucional</h3>
                <ul class="mt-4 space-y-3 text-sm text-on-primary/80">
                    <li><a href="{{ route('about') }}" class="transition hover:text-on-primary">Quiénes Somos</a></li>
                    <li><a href="{{ route('projects') }}" class="transition hover:text-on-primary">Proyectos</a></li>
                    <li><a href="{{ route('faq') }}" class="transition hover:text-on-primary">Preguntas Frecuentes</a>
                    </li>
                    <li><a href="{{ route('mapa') }}" class="transition hover:text-on-primary">Mapa del Sitio</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-widest text-on-primary/50">Contacto</h3>
                <ul class="mt-4 space-y-3 text-sm text-on-primary/80">
                    <li>Avenida Gral Cabañas, La Unión</li>
                    <li><a href="tel:+50326097000" class="transition hover:text-on-primary">+503 26097000</a></li>
                    <li><a href="mailto:info@launionsur.gob.sv"
                            class="transition hover:text-on-primary">info@launionsur.gob.sv</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-12 border-t border-on-primary/10 pt-6 text-sm text-on-primary/50">
            © 2024 Municipalidad de La Unión Sur. Compromiso y Excelencia.
        </div>
    </div>
</footer>
