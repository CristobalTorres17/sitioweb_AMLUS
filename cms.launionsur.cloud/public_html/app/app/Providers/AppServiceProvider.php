<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Services\AuditService;
use App\Models\AuditLog;
use App\Services\SensitiveChangeDetector;
use App\Models\User;
use App\Models\Page;
use App\Models\Media;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model as EloquentModel;

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
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        // Register audit listeners via a dedicated method so tests can
        // re-register the same listeners deterministically.
        $this->registerAuditListeners();
    }

    /**
     * Register global model event listeners used for auditing.
     *
     * Public so test harnesses can call it to ensure deterministic
     * registration for Feature tests.
     */
    public function registerAuditListeners(): void
    {
        $auditable = [User::class, Page::class, Media::class, Service::class];
        // In testing we want deterministic behavior: flush existing listeners
        // before registering so repeated calls result in the same set.
        if ($this->app->environment('testing')) {
            foreach ($auditable as $modelClass) {
                $modelClass::flushEventListeners();
            }
        }

        foreach ($auditable as $modelClass) {
            // Created
            $modelClass::created(function (EloquentModel $model) {
                try {
                    AuditService::log($model, AuditLog::ACTION_CREATE, [], $model->getAttributes());
                } catch (\Throwable $e) {
                    // swallow to avoid breaking app during tests
                }
            });

            // Updated
            $modelClass::updated(function (EloquentModel $model) {
                try {
                    $changes = $model->getChanges();
                    $oldValues = [];
                    $newValues = [];
                    foreach ($changes as $k => $v) {
                        $oldValues[$k] = $model->getOriginal($k);
                        $newValues[$k] = $model->{$k};
                    }

                    $isSensitive = SensitiveChangeDetector::detect(class_basename($model), array_keys($changes));

                    AuditService::log($model, AuditLog::ACTION_UPDATE, $oldValues, $newValues, $isSensitive);
                } catch (\Throwable $e) {
                    // ignore
                }
            });

            // Deleted (soft or hard)
            $modelClass::deleted(function (EloquentModel $model) {
                try {
                    AuditService::log($model, AuditLog::ACTION_DELETE, $model->getAttributes(), []);
                } catch (\Throwable $e) {
                    // ignore
                }
            });
        }
    }
}
