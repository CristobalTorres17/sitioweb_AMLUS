<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\AuditLog;
use App\Policies\AuditLogPolicy;
use App\Policies\UserPolicy;
use App\Policies\PagePolicy;
use App\Policies\MediaPolicy;
use App\Policies\MediaCollectionPolicy;
use App\Policies\ServicePolicy;
use App\Policies\CmsBlockPolicy;
use App\Policies\ArticlePolicy;
use App\Policies\EventPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        AuditLog::class => AuditLogPolicy::class,

        // Add policies for core resources
        \App\Models\User::class => UserPolicy::class,
        \App\Models\Page::class => PagePolicy::class,
        \App\Models\Media::class => MediaPolicy::class,
        \App\Models\MediaCollection::class => MediaCollectionPolicy::class,
        \App\Models\Service::class => ServicePolicy::class,
        \App\Models\CmsBlock::class => CmsBlockPolicy::class,
        \App\Models\Article::class => ArticlePolicy::class,
        \App\Models\Event::class => EventPolicy::class,
        \App\Models\Setting::class => \App\Policies\SettingPolicy::class,
        \App\Models\Menu::class => \App\Policies\MenuPolicy::class,
        \App\Models\Permission::class => PermissionPolicy::class,
        \App\Models\Role::class => RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
