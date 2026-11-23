<?php

namespace App\Services;

class SensitiveChangeDetector
{
    /**
     * Map of model types to their sensitive fields.
     * These fields trigger is_sensitive = true when changed.
     *
     * @var array<string, array<string>>
     */
    private static array $sensitiveFieldsMap = [
        'User' => [
            'email',
            'password',
            'role_id',
            'is_active',
            'locked_until',
        ],
        'Role' => [
            'name',
            'permissions',
            'description',
        ],
        'ApiToken' => [
            'token',
            'last_used_at',
        ],
    ];

    /**
     * Check if a change is considered sensitive.
     *
     * @param string $modelType The type of model being changed (e.g., 'User', 'Page')
     * @param array $changedFields Array of field names that were changed
     * @return bool True if any changed field is sensitive, false otherwise
     */
    public static function detect(string $modelType, array $changedFields): bool
    {
        // Get sensitive fields for this model type
        $sensitiveFields = self::getSensitiveFields($modelType);

        // Check if any changed field is in the sensitive fields list
        foreach ($changedFields as $field) {
            if (in_array($field, $sensitiveFields)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a specific field change is sensitive.
     *
     * @param string $modelType The type of model
     * @param string $field The field name
     * @return bool True if the field is sensitive, false otherwise
     */
    public static function isFieldSensitive(string $modelType, string $field): bool
    {
        $sensitiveFields = self::getSensitiveFields($modelType);
        return in_array($field, $sensitiveFields);
    }

    /**
     * Get all sensitive fields for a model type.
     *
     * @param string $modelType
     * @return array
     */
    public static function getSensitiveFields(string $modelType): array
    {
        return self::$sensitiveFieldsMap[$modelType] ?? [];
    }

    /**
     * Add sensitive fields for a model type.
     * Useful for extending the detector at runtime.
     *
     * @param string $modelType
     * @param array $fields
     * @return void
     */
    public static function addSensitiveFields(string $modelType, array $fields): void
    {
        if (!isset(self::$sensitiveFieldsMap[$modelType])) {
            self::$sensitiveFieldsMap[$modelType] = [];
        }

        self::$sensitiveFieldsMap[$modelType] = array_unique(
            array_merge(self::$sensitiveFieldsMap[$modelType], $fields)
        );
    }

    /**
     * Set sensitive fields for a model type (replaces existing).
     *
     * @param string $modelType
     * @param array $fields
     * @return void
     */
    public static function setSensitiveFields(string $modelType, array $fields): void
    {
        self::$sensitiveFieldsMap[$modelType] = $fields;
    }

    /**
     * Check if any of the old/new values indicate a sensitive change.
     * This is a convenience method that extracts changed fields and detects.
     *
     * @param string $modelType
     * @param array $oldValues
     * @param array $newValues
     * @return bool
     */
    public static function detectFromValues(string $modelType, array $oldValues, array $newValues): bool
    {
        $changedFields = AuditService::detectChangedFields($oldValues, $newValues);
        return self::detect($modelType, $changedFields);
    }

    /**
     * Get a descriptive message about why a change is sensitive.
     *
     * @param string $modelType
     * @param array $changedFields
     * @return string|null Null if not sensitive, message if sensitive
     */
    public static function getSensitiveReason(string $modelType, array $changedFields): ?string
    {
        $sensitiveFields = self::getSensitiveFields($modelType);
        $sensitiveFielsChanged = array_intersect($changedFields, $sensitiveFields);

        if (empty($sensitiveFielsChanged)) {
            return null;
        }

        $fields = implode(', ', $sensitiveFielsChanged);
        return "Sensitive fields changed: {$fields}";
    }

    /**
     * Get all configured model types and their sensitive fields.
     *
     * @return array
     */
    public static function getAllSensitiveFields(): array
    {
        return self::$sensitiveFieldsMap;
    }
}
