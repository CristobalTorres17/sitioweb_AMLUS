@extends('layouts.app')

@section('content')
    @include('partials.page-title', ['title' => 'Mapa'])

    <main class="container py-5">
        <div class="card shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <p class="mb-0">Ubicación institucional y referencias geográficas.</p>
            </div>
        </div>
    </main>
@endsection
