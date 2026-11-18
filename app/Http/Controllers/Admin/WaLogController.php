<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaMessage;
use Illuminate\Http\Request;

class WaLogController extends Controller
{
    /** Riwayat percakapan WhatsApp: pesan pelanggan & balasan bot. */
    public function index(Request $request)
    {
        $query = WaMessage::latest();
        if ($request->filled('direction')) {
            $query->where('direction', $request->string('direction'));
        }
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(fn ($w) => $w->where('wa_id', 'like', "%{$q}%")->orWhere('body', 'like', "%{$q}%"));
        }

        return view('admin.wa-logs.index', ['logs' => $query->paginate(30)->withQueryString()]);
    }

    public function clear()
    {
        WaMessage::query()->delete();

        return back()->with('success', 'Semua log WhatsApp dikosongkan.');
    }
}
