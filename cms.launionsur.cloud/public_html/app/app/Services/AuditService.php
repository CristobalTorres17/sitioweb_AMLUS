<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AuditService
{
    /**
     * Log a change for an auditable model.
     *
     * @param Model $model The model being audited
     * @param string $action The action performed (create, update, delete, restore)
     * @param array $oldValues The previous values (nullable for create)
     * @param array $newValues The new values (nullable for delete)
     * @param bool $isSensitive Whether this is a sensitive change
     * @param string|null $reason Optional reason for the change
     * @return AuditLog The created audit log
     */
    public static function log(
        Model $model,
        string $action,
        array $oldValues = [],
        array $newValues = [],
        bool $isSensitive = false,
        ?string $reason = null
    ): AuditLog {
        // Detect changed fields
        $changedFields = self::detectChangedFields($oldValues, $newValues);

        return AuditLog::create([
            'model_type' => class_basename($model),
            'model_id' => $model->getKey(),
            'action' => $action,
            'old_values' => !empty($oldValues) ? $oldValues : null,
            'new_values' => !empty($newValues) ? $newValues : null,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'is_sensitive' => $isSensitive,
            'is_restoreable' => in_array($action, [AuditLog::ACTION_UPDATE, AuditLog::ACTION_DELETE]),
            'reason' => $reason,
        ]);
    }

    /**
     * Detect which fields have changed between old and new values.
     *
     * @param array $oldValues
     * @param array $newValues
     * @return array Array of changed field names
     */
    public static function detectChangedFields(array $oldValues, array $newValues): array
    {
        $changed = [];

        // Check for updated/deleted fields
        foreach (array_keys(array_merge($oldValues, $newValues)) as $field) {
            $oldValue = $oldValues[$field] ?? null;
            $newValue = $newValues[$field] ?? null;

            if ($oldValue !== $newValue) {
                $changed[] = $field;
            }
        }

        return $changed;
    }

    /**
     * Build a filtered query for audit logs.
     *
     * @param string|null $modelType Filter by model type
     * @param int|null $modelId Filter by model ID
     * @param int|null $userId Filter by user ID
     * @param string|null $action Filter by action
     * @param string|null $startDate Filter by start date (Y-m-d H:i:s)
     * @param string|null $endDate Filter by end date (Y-m-d H:i:s)
     * @param bool|null $isSensitive Filter by sensitive flag
     * @return Builder
     */
    public static function filter(
        ?string $modelType = null,
        ?int $modelId = null,
        ?int $userId = null,
        ?string $action = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?bool $isSensitive = null
    ): Builder {
        $query = AuditLog::query();

        if ($modelType) {
            $query->byModelType($modelType);
        }

        if ($modelId) {
            $query->byModelId($modelId);
        }

        if ($userId) {
            $query->byUser($userId);
        }

        if ($action) {
            $query->byAction($action);
        }

        if ($isSensitive !== null) {
            $query->where('is_sensitive', $isSensitive);
        }

        if ($startDate && $endDate) {
            $query->betweenDates($startDate, $endDate);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->orderByDesc('created_at');
    }

    /**
     * Get a human-readable label for an action.
     *
     * @param string $action The action code (create, update, delete, restore)
     * @param string|null $modelType Optional model type to customize the label
     * @return string Human-readable action label
     */
    public static function getActionLabel(string $action, ?string $modelType = null): string
    {
        $labels = [
            AuditLog::ACTION_CREATE => 'Created',
            AuditLog::ACTION_UPDATE => 'Updated',
            AuditLog::ACTION_DELETE => 'Deleted',
            AuditLog::ACTION_RESTORE => 'Restored',
        ];

        $label = $labels[$action] ?? ucfirst($action);

        if ($modelType) {
            $label .= ' ' . $modelType;
        }

        return $label;
    }

    /**
     * Get audit logs for a specific model.
     *
     * @param Model $model
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public static function getModelHistory(Model $model, int $perPage = 15)
    {
        return self::filter(
            modelType: class_basename($model),
            modelId: $model->getKey()
        )->paginate($perPage);
    }

    /**
     * Get recent sensitive changes.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecentSensitiveChanges(int $limit = 20)
    {
        return self::filter(isSensitive: true)
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity summary for a user.
     *
     * @param int $userId
     * @param int $days Number of days to look back
     * @return array Summary data
     */
    public static function getUserActivitySummary(int $userId, int $days = 7): array
    {
        $startDate = now()->subDays($days)->toDateTimeString();

        $logs = self::filter(userId: $userId, startDate: $startDate)->get();

        $actionCounts = $logs->groupBy('action')->mapWithKeys(function ($group, $action) {
            return [$action => $group->count()];
        })->toArray();

        $modelCounts = $logs->groupBy('model_type')->mapWithKeys(function ($group, $type) {
            return [$type => $group->count()];
        })->toArray();

        $sensitiveCount = $logs->where('is_sensitive', true)->count();

        return [
            'total_actions' => $logs->count(),
            'action_breakdown' => $actionCounts,
            'model_breakdown' => $modelCounts,
            'sensitive_changes' => $sensitiveCount,
            'period_days' => $days,
        ];
    }
}
