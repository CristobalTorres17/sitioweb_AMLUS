<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditLogController extends Controller
{
    /**
     * Render the Audit Logs admin index with filters and pagination.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $query = AuditLog::query();

        if ($request->filled('action')) {
            $query->byAction($request->input('action'));
        }

        if ($request->filled('resource_type')) {
            $query->byModelType($request->input('resource_type'));
        }

        if ($request->filled('user_id')) {
            $query->byUser($request->input('user_id'));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->betweenDates($request->input('start_date'), $request->input('end_date'));
        }

        $perPage = min((int) $request->input('per_page', 50), 200);

        $logs = $query->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('AuditLogs/Index', [
            'logs' => $logs,
            'actions' => [
                'create' => 'Created',
                'update' => 'Updated',
                'delete' => 'Deleted',
                'restore' => 'Restored',
            ],
            'resourceTypes' => AuditLog::query()->select('model_type')->distinct()->pluck('model_type')->toArray(),
        ]);
    }
}
