<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MediaTagController;
use App\Http\Controllers\MediaCollectionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PageVersionController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // Redirect users to the appropriate entrypoint: dashboard if authenticated, otherwise login
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/admin', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Public Pages Routes (defined after admin routes so they take precedence for public GETs)

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Admin settings and menus (minimal blade-based UI)
    Route::get('admin/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings.index');
    Route::get('admin/settings/{key}/edit', [\App\Http\Controllers\Admin\SettingsController::class, 'edit'])->name('admin.settings.edit');
    Route::put('admin/settings/{key}', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.settings.update');

    Route::get('admin/menus', [\App\Http\Controllers\Admin\MenuController::class, 'index'])->name('admin.menus.index');
    Route::get('admin/menus/create', [\App\Http\Controllers\Admin\MenuController::class, 'create'])->name('admin.menus.create');
    Route::post('admin/menus', [\App\Http\Controllers\Admin\MenuController::class, 'store'])->name('admin.menus.store');
    Route::get('admin/menus/{menu}/edit', [\App\Http\Controllers\Admin\MenuController::class, 'edit'])->name('admin.menus.edit');
    Route::put('admin/menus/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'update'])->name('admin.menus.update');
    Route::delete('admin/menus/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'destroy'])->name('admin.menus.destroy');
    Route::post('admin/menus/{menu}/items', [\App\Http\Controllers\Admin\MenuController::class, 'addItem'])->name('admin.menus.addItem');
    Route::delete('admin/menus/{menu}/items/{item}', [\App\Http\Controllers\Admin\MenuController::class, 'deleteItem'])->name('admin.menus.deleteItem');
    Route::post('admin/menus/{menu}/items/reorder', [\App\Http\Controllers\Admin\MenuController::class, 'reorderItems'])
        ->name('admin.menus.items.reorder');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin content routes
    Route::resource('articles', ArticleController::class);
    Route::post('articles/{article}/restore', [ArticleController::class, 'restore'])->name('articles.restore');
    Route::post('articles/draft', [ArticleController::class, 'draft'])->name('articles.draft.store');
    Route::post('articles/{article}/draft', [ArticleController::class, 'draft'])->name('articles.draft.update');
    Route::get('articles/{article}/revisions', [ArticleController::class, 'revisions'])->name('articles.revisions.index');
    Route::post('articles/{article}/revisions/{revision}/restore', [ArticleController::class, 'restoreRevision'])->name('articles.revisions.restore');
    Route::resource('categories', \App\Http\Controllers\CategoryController::class)->except(['show']);
    Route::post('categories/{category}/restore', [\App\Http\Controllers\CategoryController::class, 'restore'])->name('categories.restore');
    Route::post('categories/{category}/move-up', [\App\Http\Controllers\CategoryController::class, 'moveUp'])->name('categories.move-up');
    Route::post('categories/{category}/move-down', [\App\Http\Controllers\CategoryController::class, 'moveDown'])->name('categories.move-down');
    Route::resource('events', EventController::class)->except(['show']);
    Route::post('events/{event}/gallery', [EventController::class, 'syncGallery'])->name('events.gallery.sync');
    Route::post('events/{event}/restore', [EventController::class, 'restore'])->name('events.restore');
    Route::resource('banners', BannerController::class)->except(['show']);
    Route::post('banners/{banner}/move-up', [BannerController::class, 'moveUp'])->name('banners.move-up');
    Route::post('banners/{banner}/move-down', [BannerController::class, 'moveDown'])->name('banners.move-down');
    Route::resource('services', \App\Http\Controllers\ServiceController::class)->except(['show']);
    Route::resource('service-categories', \App\Http\Controllers\ServiceCategoryController::class)->except(['show']);
    Route::post('service-categories/{serviceCategory}/move-up', [\App\Http\Controllers\ServiceCategoryController::class, 'moveUp'])->name('service-categories.move-up');
    Route::post('service-categories/{serviceCategory}/move-down', [\App\Http\Controllers\ServiceCategoryController::class, 'moveDown'])->name('service-categories.move-down');
    // Steps & requirements reorder endpoints
    Route::post('services/{service}/steps/{step}/move-up', [\App\Http\Controllers\ServiceController::class, 'moveStepUp'])->name('services.steps.move-up');
    Route::post('services/{service}/steps/{step}/move-down', [\App\Http\Controllers\ServiceController::class, 'moveStepDown'])->name('services.steps.move-down');
    Route::post('services/{service}/requirements/{requirement}/move-up', [\App\Http\Controllers\ServiceController::class, 'moveRequirementUp'])->name('services.requirements.move-up');
    Route::post('services/{service}/requirements/{requirement}/move-down', [\App\Http\Controllers\ServiceController::class, 'moveRequirementDown'])->name('services.requirements.move-down');
    // Batch reorder endpoints
    Route::post('services/{service}/steps/reorder', [\App\Http\Controllers\ServiceController::class, 'reorderSteps'])->name('services.steps.reorder');
    Route::post('services/{service}/requirements/reorder', [\App\Http\Controllers\ServiceController::class, 'reorderRequirements'])->name('services.requirements.reorder');
    // CMS Blocks admin
    Route::resource('cms-blocks', \App\Http\Controllers\CmsBlockController::class)->except(['show']);
    Route::post('cms-blocks/{cmsBlock}/restore', [\App\Http\Controllers\CmsBlockController::class, 'restore'])
        ->name('cms-blocks.restore');
    Route::get('cms-blocks/{cmsBlock}/revisions', [\App\Http\Controllers\CmsBlockController::class, 'revisions'])
        ->name('cms-blocks.revisions.index');
    Route::post('cms-blocks/{cmsBlock}/revisions/{revision}/restore', [\App\Http\Controllers\CmsBlockController::class, 'restoreRevision'])
        ->name('cms-blocks.revisions.restore');
    Route::post('cms-blocks/{cmsBlock}/move-up', [\App\Http\Controllers\CmsBlockController::class, 'moveUp'])
        ->name('cms-blocks.move-up');
    Route::post('cms-blocks/{cmsBlock}/move-down', [\App\Http\Controllers\CmsBlockController::class, 'moveDown'])
        ->name('cms-blocks.move-down');
    // Districts
    Route::resource('districts', \App\Http\Controllers\DistrictController::class)->except(['show']);
    Route::post('districts/{district}/move-up', [\App\Http\Controllers\DistrictController::class, 'moveUp'])->name('districts.move-up');
    Route::post('districts/{district}/move-down', [\App\Http\Controllers\DistrictController::class, 'moveDown'])->name('districts.move-down');
    // Static Pages admin
    // Use an admin-prefixed index so it doesn't collide with public page routes
    Route::get('admin/pages', [PageController::class, 'index'])->name('pages.index');
    Route::resource('pages', PageController::class)->except(['show', 'index']);
    Route::post('pages/{page}/restore', [PageController::class, 'restore'])->name('pages.restore');
    Route::post('pages/{page}/publish', [PageController::class, 'publish'])->name('pages.publish');
    Route::post('pages/{page}/unpublish', [PageController::class, 'unpublish'])->name('pages.unpublish');
    Route::get('pages/{page}/versions', [PageVersionController::class, 'index'])->name('pages.versions.index');
    Route::get('pages/{page}/versions/{version}', [PageVersionController::class, 'show'])->name('pages.versions.show');
    Route::post('pages/{page}/versions/{version}/restore', [PageVersionController::class, 'restore'])->name('pages.versions.restore');
    // Media admin (Gallery view with upload, filters, tags)
    Route::resource('media', MediaController::class)->except(['show'])->middleware('permission:edit');
    // Blade-based fallback gallery for simple admin access
    Route::get('admin/media/gallery', [\App\Http\Controllers\MediaController::class, 'galleryBlade'])
        ->name('admin.media.gallery')
        ->middleware('permission:edit');
    Route::post('media', [\App\Http\Controllers\MediaController::class, 'store'])
        ->middleware([\App\Http\Middleware\ThrottleMediaUpload::class, 'permission:edit'])
        ->name('media.store');
    Route::resource('media-tags', MediaTagController::class)->except(['show']);

    // Media collections (folder management) — JSON responses
    Route::get('media-collections', [MediaCollectionController::class, 'index'])->name('media-collections.index');
    Route::post('media-collections', [MediaCollectionController::class, 'store'])->name('media-collections.store');
    Route::patch('media-collections/{mediaCollection}', [MediaCollectionController::class, 'update'])->name('media-collections.update');
    Route::delete('media-collections/{mediaCollection}', [MediaCollectionController::class, 'destroy'])->name('media-collections.destroy');

    // Article media management
    Route::post('articles/{article}/media', [\App\Http\Controllers\ArticleMediaController::class, 'attach'])->name('articles.media.attach');
    Route::delete('articles/{article}/media/{media}', [\App\Http\Controllers\ArticleMediaController::class, 'detach'])->name('articles.media.detach');
    Route::post('articles/{article}/media/{media}/primary', [\App\Http\Controllers\ArticleMediaController::class, 'primary'])->name('articles.media.primary');
    Route::post('articles/{article}/media/reorder', [\App\Http\Controllers\ArticleMediaController::class, 'reorder'])->name('articles.media.reorder');
    Route::patch('articles/{article}/media/{media}', [\App\Http\Controllers\ArticleMediaController::class, 'updateCaption'])->name('articles.media.update');

    // Users admin UI (Inertia pages)
    Route::get('admin/users', [\App\Http\Controllers\UserController::class, 'adminIndex'])->name('admin.users.index');

    Route::get('admin/users/create', [\App\Http\Controllers\UserController::class, 'adminCreate'])->name('admin.users.create');

    Route::get('admin/users/{user}/edit', [\App\Http\Controllers\UserController::class, 'adminEdit'])->name('admin.users.edit');

    Route::get('admin/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])
        ->name('admin.audit-logs');

    // Provide roles for admin UI (used by Create/Edit pages)
    Route::get('admin/roles', function () {
        return response()->json(\App\Models\Role::select('id', 'name')->orderBy('name')->get());
    })->middleware('auth');

    // Roles & Permissions admin (blade-based quick UI)
    Route::get('admin/roles/list', [\App\Http\Controllers\Admin\RoleController::class, 'index'])
        ->name('admin.roles.index')->middleware('permission:edit');
    Route::get('admin/roles/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])
        ->name('admin.roles.create')->middleware('permission:edit');
    Route::post('admin/roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])
        ->name('admin.roles.store')->middleware('permission:edit');
    Route::get('admin/roles/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])
        ->name('admin.roles.edit')->middleware('permission:edit');
    Route::put('admin/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])
        ->name('admin.roles.update')->middleware('permission:edit');
    Route::delete('admin/roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])
        ->name('admin.roles.destroy')->middleware('permission:edit');

    Route::get('admin/permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])
        ->name('admin.permissions.index')->middleware('permission:edit');
    Route::get('admin/permissions/create', [\App\Http\Controllers\Admin\PermissionController::class, 'create'])
        ->name('admin.permissions.create')->middleware('permission:edit');
    Route::post('admin/permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'store'])
        ->name('admin.permissions.store')->middleware('permission:edit');
    Route::get('admin/permissions/{permission}/edit', [\App\Http\Controllers\Admin\PermissionController::class, 'edit'])
        ->name('admin.permissions.edit')->middleware('permission:edit');
    Route::put('admin/permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'update'])
        ->name('admin.permissions.update')->middleware('permission:edit');
    Route::delete('admin/permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'destroy'])
        ->name('admin.permissions.destroy')->middleware('permission:edit');
});

require __DIR__ . '/auth.php';

// Public Pages Routes
Route::get('/pages', [PublicPageController::class, 'index'])->name('pages.public.index');
Route::get('/pages/{page:slug}', [PublicPageController::class, 'show'])->name('pages.public.show');
