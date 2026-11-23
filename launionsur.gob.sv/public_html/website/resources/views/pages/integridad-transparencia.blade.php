@extends('layouts.app')

@section('meta_description',
    'Información oficial sobre política antisoborno, canales de denuncia y reglas de regalos y
    hospitalidad de la Alcaldía Municipal de La Unión Sur.')

@section('content')
    @php
        $denunciaEmail = 'antisoborno@launionsur.gob.sv';

        $afterHeroBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'after_hero', []));
        $midContentBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'mid_content', []))->concat(
            collect(data_get($cmsPageBlocksByZone ?? [], 'content', [])),
        );
        $beforeFooterBlocks = collect(data_get($cmsPageBlocksByZone ?? [], 'before_footer', []));
    @endphp

    <section class="relative overflow-hidden bg-primary text-on-primary pt-32 pb-20">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-linear-to-br from-primary via-primary/95 to-primary-container/80"></div>
            <div class="absolute -top-24 right-0 h-80 w-80 rounded-full bg-secondary/15 blur-3xl"></div>
            <div class="absolute -bottom-28 left-0 h-96 w-96 rounded-full bg-secondary-container/15 blur-3xl"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-screen-2xl px-6 md:px-8">
            <div class="max-w-4xl">
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-secondary-container/95 px-4 py-1.5 text-xs font-bold tracking-[0.24em] uppercase text-on-secondary-container shadow-sm">
                    Integridad institucional
                </div>

                <h1 class="mt-6 text-5xl font-black tracking-tighter text-white md:text-6xl lg:text-7xl">
                    Sistema de Gestión Antisoborno y Transparencia
                </h1>

                <p class="mt-6 max-w-3xl text-base leading-7 text-on-primary/80 md:text-lg">
                    En la Alcaldía Municipal de La Unión Sur mantenemos una política de cero tolerancia al soborno,
                    promovemos canales de denuncia seguros y aplicamos reglas claras para regalos y hospitalidad.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#canales-denuncia"
                        class="inline-flex items-center justify-center rounded-lg bg-secondary-container px-6 py-3 font-bold text-on-secondary-container transition hover:-translate-y-0.5">
                        Ver canales de denuncia
                    </a>
                    <a href="mailto:{{ $denunciaEmail }}"
                        class="inline-flex items-center justify-center rounded-lg border border-on-primary/20 px-6 py-3 font-bold text-on-primary transition hover:bg-on-primary/10">
                        Denunciar ahora
                    </a>
                </div>
            </div>
        </div>
    </section>



    @if ($afterHeroBlocks->isNotEmpty())
        @foreach ($afterHeroBlocks as $cmsBlock)
            {!! $cmsBlock->content !!}
        @endforeach
    @endif

    <section class="bg-background">
        <div class="mx-auto max-w-screen-2xl px-6 py-16 md:px-8 lg:py-20 space-y-16">
            <section>
                <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.24em] text-secondary">Nuestros 3 pilares</p>
                        <h2 class="mt-2 text-3xl font-black tracking-tighter text-primary md:text-4xl">
                            Integridad al servicio de la ciudadanía
                        </h2>
                    </div>
                    <p class="max-w-2xl text-sm leading-6 text-on-surface-variant md:text-base">
                        La política institucional se apoya en tres compromisos visibles, medibles y fáciles de consultar.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <article class="rounded-2xl bg-surface-container-lowest p-6 ambient-shadow">
                        <span class="material-symbols-outlined mb-4 block text-3xl text-secondary">verified</span>
                        <h3 class="text-xl font-bold text-primary">Política SGAS</h3>
                        <p class="mt-3 text-sm leading-6 text-on-surface-variant">
                            Cumplimos el Sistema de Gestión Antisoborno bajo principios de legalidad, transparencia y
                            servicio público.
                        </p>
                    </article>

                    <article class="rounded-2xl bg-surface-container-lowest p-6 ambient-shadow">
                        <span class="material-symbols-outlined mb-4 block text-3xl text-secondary">report</span>
                        <h3 class="text-xl font-bold text-primary">Canales de denuncia</h3>
                        <p class="mt-3 text-sm leading-6 text-on-surface-variant">
                            Toda persona puede denunciar sospechas de soborno, faltas éticas o incumplimientos legales con
                            confidencialidad.
                        </p>
                    </article>

                    <article class="rounded-2xl bg-surface-container-lowest p-6 ambient-shadow">
                        <span class="material-symbols-outlined mb-4 block text-3xl text-secondary">gavel</span>
                        <h3 class="text-xl font-bold text-primary">Regalos y hospitalidad</h3>
                        <p class="mt-3 text-sm leading-6 text-on-surface-variant">
                            Aplicamos reglas claras para prevenir conflictos de interés y proteger la integridad
                            institucional.
                        </p>
                    </article>
                </div>
            </section>

            <div class="grid gap-4 md:grid-cols-12 md:items-start">
                <div class="space-y-6 md:col-span-8">
                    @if ($midContentBlocks->isNotEmpty())
                        <div class="space-y-8">
                            @foreach ($midContentBlocks as $cmsBlock)
                                <section class="rounded-3xl bg-surface-container-lowest p-6 ambient-shadow md:p-8">
                                    {!! $cmsBlock->content !!}
                                </section>
                            @endforeach
                        </div>
                    @endif

                    <section id="politica-sgas" class="rounded-3xl bg-surface-container-lowest p-6 ambient-shadow md:p-8">
                        <p class="text-xs font-bold uppercase tracking-[0.24em] text-secondary">Política SGAS</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tighter text-primary md:text-4xl">
                            Política del Sistema de Gestión Antisoborno
                        </h2>

                        <div class="mt-6 space-y-5 text-base leading-7 text-on-surface-variant">
                            <p>
                                Somos una Municipalidad que administra eficientemente los recursos, con una gestión
                                transparente,
                                proporcionando servicios de calidad y calidez, comprometidos con el desarrollo social,
                                económico,
                                turístico, ambiental, cultural y deportivo del municipio, para garantizar mejor calidad de
                                vida
                                a la población.
                            </p>
                            <p>
                                El Concejo Municipal de la Alcaldía Municipal de La Unión Sur, Departamento de La Unión,
                                asume
                                el compromiso de cumplir
                                y fortalecer su Sistema de Gestión Antisoborno mediante las siguientes acciones:
                            </p>
                            <ol class="list-decimal space-y-3 pl-5">
                                <li>Prohibir y no tolerar ninguna práctica de soborno, directa o indirecta, financiera o no
                                    financiera, incluyendo promesas, entregas, aceptaciones o solicitudes de ventajas
                                    indebidas
                                    por parte de colaboradores o terceros.</li>
                                <li>Cumplir estrictamente leyes, reglamentos y normativa institucional aplicable al Sistema
                                    de
                                    Gestión Antisoborno.</li>
                                <li>Promover y proteger canales de denuncia para reportar sospechas de soborno, conductas
                                    éticas
                                    impropias o incumplimientos legales, sin temor a represalias.</li>
                            </ol>
                        </div>
                    </section>

                    <section id="regalos-hospitalidad"
                        class="rounded-3xl bg-surface-container-lowest p-6 ambient-shadow md:p-8">
                        <p class="text-xs font-bold uppercase tracking-[0.24em] text-secondary">Regalos y hospitalidad</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tighter text-primary md:text-4xl">
                            Reglas claras para proteger la imparcialidad institucional
                        </h2>
                        <p class="mt-4 text-sm leading-6 text-on-surface-variant">
                            Aplicamos criterios transparentes para prevenir conflictos de interés y evitar cualquier
                            apariencia
                            de impropiedad.
                        </p>

                        <div class="mt-6 space-y-4 text-base leading-7 text-on-surface-variant">
                            <p>1. Las y los miembros de la Municipalidad no podrán aceptar regalos o muestras de cortesía de
                                ninguna índole cuando comprometan la objetividad de sus funciones.</p>
                            <p>2. En actividades administrativas, no se podrán aceptar atenciones operativas o regalos de
                                proveedores cuando su valor supere el umbral institucional permitido o se reciban con
                                expectativa de influencia.</p>
                            <p>3. Quedan excluidos los obsequios entre miembros de la Municipalidad que sean transparentes,
                                de
                                valor mínimo y sin conflicto de interés.</p>
                            <p>4. Solo podrán aceptarse muestras de hospitalidad institucional cuando:</p>
                            <ul class="list-disc space-y-2 pl-5">
                                <li>Sean parte de procesos de capacitación o cooperación institucional.</li>
                                <li>No busquen influir decisiones, obtener beneficios o intercambiar tratos de favor.</li>
                                <li>Se reciban de manera pública, transparente y documentada.</li>
                                <li>Su frecuencia no genere apariencia de impropiedad.</li>
                            </ul>
                            <p>5. La Municipalidad no recibe ni otorga donaciones a socios de negocio o personal
                                institucional
                                sin autorización expresa del Concejo Municipal.</p>

                            <div class="mt-6 rounded-2xl border border-border bg-white p-5 text-sm text-text-secondary">
                                <div class="font-medium text-primary">Nota</div>
                                Ajusta este texto a la versión jurídica oficial antes de publicar en producción.
                            </div>
                        </div>
                    </section>
                </div>

                <div class="space-y-6 md:col-span-4 md:sticky md:top-24">
                    <div class="rounded-3xl bg-primary-container p-6 text-on-primary ambient-shadow">
                        <p class="text-xs font-bold tracking-[0.22em] uppercase text-secondary-container">Denuncia
                            confidencial</p>
                        <h3 class="mt-3 text-2xl font-black tracking-tight text-white">Tu denuncia puede ser anónima</h3>
                        <p class="mt-3 text-sm leading-6 text-on-primary/75">
                            Escríbenos al correo oficial y ayúdanos a detectar conductas impropias o incumplimientos
                            legales.
                        </p>
                        <a href="mailto:{{ $denunciaEmail }}"
                            class="mt-5 inline-flex items-center justify-center rounded-lg bg-secondary-container px-5 py-3 text-sm font-bold text-on-secondary-container transition hover:-translate-y-0.5 w-full">
                            {{ $denunciaEmail }}
                        </a>
                    </div>

                    <div class="rounded-3xl bg-surface-container-lowest p-6 ambient-shadow">
                        <p class="text-xs font-bold tracking-[0.22em] uppercase text-secondary">Correo oficial</p>
                        <p class="mt-3 text-lg font-bold text-primary">antisoborno@launionsur.gob.sv</p>
                        <p class="mt-3 text-sm leading-6 text-on-surface-variant">
                            Guarda este contacto como canal oficial para observaciones, reportes o consultas relacionadas
                            con integridad.
                        </p>
                    </div>

                    @include('partials.page-nav-widget')
                </div>
            </div>

            <section id="canales-denuncia"
                class="rounded-3xl bg-primary px-6 py-8 text-on-primary ambient-shadow md:px-8 md:py-10">
                <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.24em] text-secondary-container">Canales de
                            denuncia</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tighter text-white md:text-4xl">
                            Denuncia segura, libre y confidencial
                        </h2>
                        <p class="mt-4 text-sm leading-6 text-on-primary/80 md:text-base">
                            Ante cualquier sospecha de actos de soborno, cuestionamientos éticos o incumplimientos legales,
                            promovemos el uso libre y confidencial de nuestros canales de denuncia.
                        </p>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="mailto:{{ $denunciaEmail }}"
                                class="inline-flex items-center justify-center rounded-lg bg-secondary-container px-5 py-3 text-sm font-bold text-on-secondary-container transition hover:-translate-y-0.5">
                                Enviar denuncia por correo
                            </a>
                            <a href="#politica-sgas"
                                class="inline-flex items-center justify-center rounded-lg border border-on-primary/20 px-5 py-3 text-sm font-bold text-on-primary transition hover:bg-on-primary/10">
                                Ver política completa
                            </a>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-white/10 p-5 backdrop-blur">
                            <p class="text-xs font-bold uppercase tracking-[0.22em] text-secondary-container">Correo visible
                            </p>
                            <p class="mt-3 text-lg font-bold text-white">{{ $denunciaEmail }}</p>
                            <p class="mt-2 text-sm leading-6 text-on-primary/70">
                                Puedes usarlo desde cualquier dispositivo o cliente de correo.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white/10 p-5 backdrop-blur">
                            <div
                                class="flex aspect-square flex-col items-center justify-center rounded-xl border border-dashed border-white/30 bg-white/5 text-center">
                                <span class="material-symbols-outlined text-5xl text-secondary-container">qr_code_2</span>
                                <p class="mt-3 text-sm font-bold uppercase tracking-[0.18em] text-white">Escanéame</p>
                                <p class="mt-2 max-w-48 text-xs leading-5 text-on-primary/75">
                                    Accede rápidamente al canal oficial de denuncia.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @foreach ($beforeFooterBlocks as $cmsBlock)
                {!! $cmsBlock->content !!}
            @endforeach

            <section class="rounded-3xl bg-primary px-8 py-10 text-on-primary ambient-shadow md:px-10 md:py-12">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold uppercase tracking-[0.24em] text-secondary-container">Cero tolerancia al
                            soborno</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tighter text-white md:text-4xl">
                            Reporta cualquier situación irregular por el canal oficial
                        </h2>
                        <p class="mt-4 text-sm leading-6 text-on-primary/75 md:text-base">
                            Tu participación fortalece la transparencia, protege la confianza ciudadana y nos ayuda a actuar
                            con rapidez.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="mailto:{{ $denunciaEmail }}"
                            class="inline-flex items-center justify-center rounded-lg bg-secondary-container px-6 py-3 font-bold text-on-secondary-container transition hover:-translate-y-0.5">
                            Denunciar ahora
                        </a>
                        <a href="#politica-sgas"
                            class="inline-flex items-center justify-center rounded-lg border border-on-primary/20 px-6 py-3 font-bold text-on-primary transition hover:bg-on-primary/10">
                            Ver política completa
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </section>
@endsection
