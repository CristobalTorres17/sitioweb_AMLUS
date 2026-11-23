<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuditLogController extends Controller
{
    /**
     * Get paginated list of audit logs with filtering.
     *
     * Query Parameters:
     * - model_type: Filter by model type (e.g., "App\Models\User")
     * - model_id: Filter by specific model ID
     * - user_id: Filter by user who made the change
     * - action: Filter by action type (create, update, delete, restore)
     * - start_date: Filter from date (ISO format)
     * - end_date: Filter to date (ISO format)
     * - is_sensitive: Filter by sensitive flag (1/0)
     * - per_page: Items per page (default: 15)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Authorization via Policy
        $this->authorize('viewAny', AuditLog::class);

        $query = AuditLog::query();

        // Apply filters
        if ($request->filled('model_type')) {
            $query->byModelType($request->input('model_type'));
        }

        if ($request->filled('model_id')) {
            $query->byModelId($request->input('model_id'));
        }

        if ($request->filled('user_id')) {
            $query->byUser($request->input('user_id'));
        }

        if ($request->filled('action')) {
            $query->byAction($request->input('action'));
        }

        if ($request->filled('is_sensitive')) {
            $isSensitive = $request->input('is_sensitive') === '1' || $request->input('is_sensitive') === true;
            if ($isSensitive) {
                $query->sensitive();
            } else {
                $query->where('is_sensitive', false);
            }
        }

        // Date range filtering
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $query->betweenDates($startDate, $endDate);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->input('start_date'));
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->input('end_date'));
        }

        // Pagination
        $perPage = (int) $request->input('per_page', 15);
        $perPage = min($perPage, 100); // Cap at 100 for security

        $auditLogs = $query->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($auditLogs);
    }

    /**
     * Get a specific audit log with computed diff.
     *
     * @param AuditLog $auditLog
     * @return JsonResponse
     */
    public function show(AuditLog $auditLog): JsonResponse
    {
        // Authorization via Policy
        $this->authorize('view', $auditLog);

        $data = $auditLog->toArray();
        $data['diff'] = $auditLog->getDiff();
        $data['user'] = $auditLog->user;
        $data['action_label'] = $auditLog->action ? AuditService::getActionLabel($auditLog->action) : 'Unknown';

        return response()->json($data);
    }

    /**
     * Restore a model to a previous state using an audit log.
     *
     * Only Super Admin can restore. Validates that the audit log
     * is marked as restorable before executing the restore.
     * Creates a new audit log entry for the restoration action.
     *
     * @param AuditLog $auditLog
     * @return JsonResponse
     */
    public function restore(AuditLog $auditLog): JsonResponse
    {
        // Authorization via Policy (checks super_admin role)
        $this->authorize('restore', $auditLog);

        // Business logic validation: check if restorable
        if (!$auditLog->is_restoreable) {
            return response()->json(
                ['error' => 'This audit log is not marked as restorable'],
                422
            );
        }

        try {
            // Execute the restore
            $auditLog->restore();

            // Log the restoration action itself
            AuditService::log(
                model: $auditLog,
                action: 'restore',
                oldValues: [],
                newValues: $auditLog->toArray(),
                isSensitive: false,
                reason: "Restored via audit log restoration endpoint"
            );

            return response()->json([
                'message' => 'Audit log restore executed successfully',
                'audit_log' => $auditLog->fresh(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Restore operation failed', 'details' => $e->getMessage()],
                422
            );
        }
    }
}
