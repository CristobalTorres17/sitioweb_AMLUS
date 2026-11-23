@extends('layouts.app')

@section('content')
    @include('partials.page-title', ['title' => 'Preguntas Frecuentes'])

    <main class="container py-5">
        <div class="card shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <p class="mb-0">Listado de preguntas y respuestas frecuentes.</p>
            </div>
        </div>
    </main>
@endsection
