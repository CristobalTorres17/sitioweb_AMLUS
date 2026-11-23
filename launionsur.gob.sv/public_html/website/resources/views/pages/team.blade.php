@extends('layouts.app')

@section('content')
    @include('partials.page-title', ['title' => 'Equipo'])

    <main class="container py-5">
        <div class="card shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <p class="mb-0">Contenido institucional del equipo de trabajo.</p>
            </div>
        </div>
    </main>
@endsection
