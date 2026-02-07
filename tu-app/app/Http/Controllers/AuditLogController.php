<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display audit logs with filtering
     */
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')
            ->latest();

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        // Get distinct values for filters
        $actions = AuditLog::distinct()->pluck('action');
        $modelTypes = AuditLog::distinct()->pluck('model_type')
            ->map(fn($type) => class_basename($type));
        $users = \App\Models\User::select('id', 'name')->get();

        return view('admin.audit-logs.index', compact('logs', 'actions', 'modelTypes', 'users'));
    }
}
