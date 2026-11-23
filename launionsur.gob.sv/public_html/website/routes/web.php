<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TramiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('noticias')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');
});

Route::prefix('servicios')->name('services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/{slug}', [ServiceController::class, 'show'])->name('show');
});

Route::prefix('tramites')->name('tramites.')->group(function () {
    Route::get('/', [TramiteController::class, 'index'])->name('index');
    Route::get('/{slug}', [TramiteController::class, 'show'])->name('show');
});

Route::prefix('distritos')->name('districts.')->group(function () {
    Route::get('/', [DistrictController::class, 'index'])->name('index');
    Route::get('/{slug}', [DistrictController::class, 'show'])->name('show');
});

Route::get('/contacto', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contacto', [ContactController::class, 'send'])
    ->middleware('throttle:10,1')
    ->name('contact.send');

Route::get('/buscar', [SearchController::class, 'index'])->name('search');
Route::get('/api/buscar', [SearchController::class, 'searchApi'])->name('search.api');

Route::get('/nosotros', [PageController::class, 'about'])->name('about');
Route::get('/equipo', [PageController::class, 'team'])->name('team');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/precios', [PageController::class, 'pricing'])->name('pricing');
Route::get('/mapa', [PageController::class, 'mapa'])->name('mapa');
Route::get('/integridad-transparencia', [PageController::class, 'integridadTransparencia'])->name('integridad');
Route::get('/paginas/{slug}', [PageController::class, 'cms'])->name('pages.show');
Route::get('/proyectos', [PageController::class, 'projects'])->name('projects');
Route::get('/proyectos/{slug}', [PageController::class, 'projectDetails'])->name('projects.show');
Route::get('/testimonios', [PageController::class, 'testimonials'])->name('testimonials');

Route::redirect('/index.php', '/', 301);
Route::redirect('/blog.php', '/noticias', 301);
Route::redirect('/blog-details.php', '/noticias', 301);
Route::redirect('/search-event.php', '/buscar', 301);
Route::redirect('/services.php', '/servicios', 301);
Route::redirect('/contact.php', '/contacto', 301);
Route::redirect('/about.php', '/nosotros', 301);
Route::redirect('/team.php', '/equipo', 301);
Route::redirect('/faq.php', '/faq', 301);
Route::redirect('/pricing.php', '/precios', 301);
Route::redirect('/mapa.php', '/mapa', 301);
Route::redirect('/projects.php', '/proyectos', 301);
Route::redirect('/project-details.php', '/proyectos', 301);
Route::redirect('/testimonials.php', '/testimonios', 301);

Route::redirect('/electric-panel-repair', '/servicios/electric-panel-repair', 301);
Route::redirect('/lighting-fixtures', '/servicios/lighting-fixtures', 301);
Route::redirect('/short-circuit-repair', '/servicios/short-circuit-repair', 301);
Route::redirect('/installing-ceiling-fan', '/servicios/installing-ceiling-fan', 301);
Route::redirect('/maintenance-service', '/servicios/maintenance-service', 301);
Route::redirect('/commercial-services', '/servicios/commercial-services', 301);
