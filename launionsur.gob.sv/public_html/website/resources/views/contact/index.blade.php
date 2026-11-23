@extends('layouts.app')

@section('content')
    <header class="mx-auto max-w-screen-2xl px-6 pb-12 pt-32 md:px-8">
        <span class="mb-4 block text-xs font-bold uppercase tracking-[0.22em] text-secondary">
            Canal de Comunicación Directa
        </span>
        <h1 class="max-w-3xl text-5xl font-black tracking-tighter text-primary md:text-6xl">
            Estamos para servir a nuestra comunidad.
        </h1>
        <p class="mt-6 max-w-xl text-base leading-7 text-on-surface-variant md:text-lg">
            Utilice este formulario para consultas generales, reportes ciudadanos o solicitudes de información.
            Nuestro equipo técnico responderá en un plazo máximo de 24 horas hábiles.
        </p>
    </header>

    <main class="bg-background pb-16">
        <div
            class="mx-auto max-w-screen-2xl px-6 md:px-8 lg:grid lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-8 lg:items-start">
            <div class="min-w-0 space-y-10">
                @if (session('success'))
                    <div
                        class="mb-8 rounded-xl border border-secondary/20 bg-secondary-container/35 px-5 py-4 text-sm font-medium text-on-surface ambient-shadow">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid gap-10 lg:grid-cols-[minmax(0,1.1fr),minmax(320px,0.9fr)] lg:items-start">
                    <section class="rounded-2xl bg-surface-container-lowest p-6 md:p-8 ambient-shadow">
                        <form method="POST" action="{{ route('contact.send') }}" class="space-y-6">
                            @csrf

                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label for="name"
                                        class="mb-2 block text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                                        Nombre
                                    </label>
                                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                                        class="w-full rounded-lg border-none bg-surface-container-low px-4 py-3 text-on-surface transition focus:border-b-2 focus:border-primary focus:bg-surface-container-lowest focus:outline-none @error('name') ring-1 ring-error @enderror">
                                    @error('name')
                                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email"
                                        class="mb-2 block text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                                        Correo electrónico
                                    </label>
                                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                        class="w-full rounded-lg border-none bg-surface-container-low px-4 py-3 text-on-surface transition focus:border-b-2 focus:border-primary focus:bg-surface-container-lowest focus:outline-none @error('email') ring-1 ring-error @enderror">
                                    @error('email')
                                        <p class="mt-2 text-sm text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="subject"
                                    class="mb-2 block text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                                    Asunto
                                </label>
                                <select id="subject" name="subject" required
                                    class="w-full rounded-lg border-none bg-surface-container-low px-4 py-3 text-on-surface transition focus:border-b-2 focus:border-primary focus:bg-surface-container-lowest focus:outline-none @error('subject') ring-1 ring-error @enderror">
                                    <option value="">Seleccione un asunto</option>
                                    <option value="general" @selected(old('subject') === 'general')>Consulta General</option>
                                    <option value="services" @selected(old('subject') === 'services')>Trámites y Servicios</option>
                                    <option value="complaint" @selected(old('subject') === 'complaint')>Denuncia Ciudadana</option>
                                    <option value="info" @selected(old('subject') === 'info')>Información</option>
                                    <option value="other" @selected(old('subject') === 'other')>Otro</option>
                                </select>
                                @error('subject')
                                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="message"
                                    class="mb-2 block text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                                    Mensaje
                                </label>
                                <textarea id="message" name="message" rows="8" required
                                    class="w-full rounded-lg border-none bg-surface-container-low px-4 py-3 text-on-surface transition focus:border-b-2 focus:border-primary focus:bg-surface-container-lowest focus:outline-none @error('message') ring-1 ring-error @enderror">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-2 text-sm text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-wrap items-center gap-4">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-primary-container px-8 py-4 font-bold text-on-primary transition hover:-translate-y-0.5">
                                    Enviar mensaje
                                </button>
                                <p class="text-sm text-on-surface-variant">
                                    Le responderemos al correo indicado.
                                </p>
                            </div>
                        </form>
                    </section>

                    <aside class="space-y-4">
                        <div class="rounded-xl bg-surface-container-lowest p-5 ambient-shadow">
                            <div class="flex items-start gap-4">
                                <span class="rounded-lg bg-secondary px-2.5 py-2 text-white">
                                    <span class="material-symbols-outlined text-[20px]">call</span>
                                </span>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Línea de
                                        Atención</p>
                                    <p class="text-lg font-bold text-primary">+503 2609-7000</p>
                                    <p class="text-sm text-on-surface-variant">Extensión 101: Secretaría</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl bg-surface-container-lowest p-5 ambient-shadow">
                            <div class="flex items-start gap-4">
                                <span class="rounded-lg bg-secondary px-2.5 py-2 text-white">
                                    <span class="material-symbols-outlined text-[20px]">location_on</span>
                                </span>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Dirección
                                        Física</p>
                                    <p class="text-lg font-bold text-primary">Avenida Gral Cabañas, La Unión</p>
                                    <p class="text-sm text-on-surface-variant">Municipalidad de La Unión Sur</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl bg-surface-container-lowest p-5 ambient-shadow">
                            <div class="flex items-start gap-4">
                                <span class="rounded-lg bg-secondary px-2.5 py-2 text-white">
                                    <span class="material-symbols-outlined text-[20px]">schedule</span>
                                </span>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Horario
                                        de
                                        Oficina</p>
                                    <p class="text-lg font-bold text-primary">Lun a Vie, 8:00 a.m. - 4:00 p.m.</p>
                                    <p class="text-sm text-on-surface-variant">Atención continua en días hábiles</p>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden rounded-xl bg-surface-container-lowest ambient-shadow">
                            <div class="h-64 w-full">
                                <iframe title="Mapa de la Municipalidad de La Unión Sur"
                                    src="https://www.google.com/maps?q=Avenida+Gral+Caba%C3%B1as,+La+Uni%C3%B3n,+El+Salvador&output=embed"
                                    class="h-full w-full border-0" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </aside>
                </div>

            </div>

            <aside class="hidden lg:block lg:sticky lg:top-28">
                @include('partials.page-nav-widget', ['compact' => true])
            </aside>
        </div>
    </main>
@endsection
