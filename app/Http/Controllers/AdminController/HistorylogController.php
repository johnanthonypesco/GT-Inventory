<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HistoryLog;

class HistorylogController extends Controller
{
    public function showhistorylog(Request $request)
    {
        $search = $request->input('search', '');

        $query = HistoryLog::query()->orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        $historyLogs = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.partials._history_table', compact('historyLogs'))->render();
        }

        return view('admin.historylog', compact('historyLogs'));
    }
}
