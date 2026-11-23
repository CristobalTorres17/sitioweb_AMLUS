<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;
    // Constants for audit actions
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    public const ACTION_RESTORE = 'restore';

    /**
     * The name of the "updated at" column.
     * Setting to null means we don't use updated_at (only created_at).
     *
     * @var string|null
     */
    const UPDATED_AT = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old_values' => 'json',
            'new_values' => 'json',
            'is_sensitive' => 'boolean',
            'is_restoreable' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the user who made this change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the audited model instance.
     * This retrieves the actual model that was changed.
     *
     * @return Model|null
     */
    public function getModelInstance(): ?Model
    {
        if (!$this->model_type || !$this->model_id) {
            return null;
        }

        try {
            $modelClass = 'App\\Models\\' . $this->model_type;
            if (!class_exists($modelClass)) {
                return null;
            }

            return $modelClass::find($this->model_id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the difference between old and new values.
     * Returns an array with only the changed fields.
     *
     * @return array
     */
    public function getDiff(): array
    {
        $diff = [];

        if (!$this->old_values && !$this->new_values) {
            return $diff;
        }

        $oldValues = $this->old_values ?? [];
        $newValues = $this->new_values ?? [];

        // Find all keys from both arrays
        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

        foreach ($allKeys as $key) {
            $oldValue = $oldValues[$key] ?? null;
            $newValue = $newValues[$key] ?? null;

            // Only include if values are different
            if ($oldValue !== $newValue) {
                $diff[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $diff;
    }

    /**
     * Restore the audited model to its previous state.
     * This method retrieves the old values and updates the model.
     *
     * @return bool True if restoration was successful
     * @throws \Exception If restoration is not allowed or model not found
     */
    public function restore(): bool
    {
        // Check if this audit log is restorable
        if (!$this->is_restoreable) {
            throw new \Exception('This audit log is not restorable.');
        }

        // Get the model instance
        $model = $this->getModelInstance();
        if (!$model) {
            throw new \Exception('The audited model does not exist.');
        }

        // Only update and delete actions can be restored
        if (!in_array($this->action, [self::ACTION_UPDATE, self::ACTION_DELETE])) {
            throw new \Exception('Only update and delete actions can be restored.');
        }

        // Update the model with old values
        if ($this->old_values && count($this->old_values) > 0) {
            $model->update($this->old_values);
        }

        // If this was a delete, restore the soft-deleted model
        if ($this->action === self::ACTION_DELETE && method_exists($model, 'restore')) {
            $model->restore();
        }

        // Log the restoration as a new audit entry
        AuditLog::create([
            'model_type' => $this->model_type,
            'model_id' => $this->model_id,
            'action' => self::ACTION_RESTORE,
            'old_values' => $this->new_values, // What we're restoring FROM
            'new_values' => $this->old_values, // What we're restoring TO
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'is_sensitive' => $this->is_sensitive,
            'is_restoreable' => false, // Prevent infinite restore loops
            'reason' => "Restored from audit log #{$this->id}",
        ]);

        return true;
    }

    /**
     * Scope to filter by model type.
     */
    public function scopeByModelType($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope to filter by model ID.
     */
    public function scopeByModelId($query, int $modelId)
    {
        return $query->where('model_id', $modelId);
    }

    /**
     * Scope to filter by action.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by sensitive changes.
     */
    public function scopeSensitive($query)
    {
        return $query->where('is_sensitive', true);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
