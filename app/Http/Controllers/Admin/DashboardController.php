<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatAgent;
use App\Models\ChatLog;
use App\Models\ChatMenu;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalMenu' => ChatMenu::count(),
            'totalAgen' => ChatAgent::count(),
            'totalLog' => ChatLog::count(),
            'logHariIni' => ChatLog::whereDate('created_at', today())->count(),
            'tidakTerjawab' => ChatLog::where('type', 'text')->where('matched', false)->count(),
            'totalPengguna' => User::count(),
            'latestLogs' => ChatLog::latest()->take(8)->get(),
            'topFallback' => ChatLog::where('type', 'text')->where('matched', false)
                ->selectRaw('content, COUNT(*) as total')->groupBy('content')
                ->orderByDesc('total')->take(5)->get(),
        ]);
    }
}
