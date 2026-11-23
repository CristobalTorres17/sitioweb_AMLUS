<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\CmsBlockDisplayService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(CmsBlockDisplayService $cmsBlocks): void
    {
        Paginator::defaultView('vendor.pagination.tailwind-custom');

        View::composer('*', function ($view) use ($cmsBlocks) {
            if (!request()) {
                return;
            }

            $view->with($cmsBlocks->sharedViewData(request()));
        });

        // Prevent accidental write queries in local when DB_READONLY_GUARD is enabled.
        if ($this->app->environment('local') && env('DB_READONLY_GUARD', true) && !$this->isSchemaCommand()) {
            DB::listen(function ($query) {
                // $query is an instance of Illuminate\Database\Events\QueryExecuted
                if (preg_match('/^\s*(insert|update|delete|create|drop|alter|truncate|replace)\b/i', $query->sql)) {
                    throw new \RuntimeException('Write query blocked by READONLY guard: ' . $query->sql);
                }
            });
        }
    }

    private function isSchemaCommand(): bool
    {
        if (!$this->app->runningInConsole()) {
            return false;
        }

        $argv = $_SERVER['argv'] ?? [];
        $command = $argv[1] ?? '';

        return in_array($command, [
            'migrate',
            'migrate:fresh',
            'migrate:refresh',
            'migrate:reset',
            'migrate:rollback',
            'db:seed',
            'db:wipe',
            'schema:dump',
        ], true);
    }
}
