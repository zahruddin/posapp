<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class logActivityController extends Controller
{
    //
    public function index(Request $request)
    {
        $logs = ActivityLog::query();

        if ($request->has('search')) {
            $search = $request->search;
            $logs = $logs->where('action', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%");
        }

        // Mengambil data activity log dengan relasi user untuk nama kasir
        $logs = $logs->latest()->with('user')->paginate(10); // 'user' adalah relasi pada ActivityLog

        return view('admin.logActivity', compact('logs'));
    }
}
