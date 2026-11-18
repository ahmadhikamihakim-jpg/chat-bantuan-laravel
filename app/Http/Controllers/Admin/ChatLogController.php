<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatLog;
use Illuminate\Http\Request;

class ChatLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatLog::latest();
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($request->boolean('unmatched')) {
            $query->where('type', 'text')->where('matched', false);
        }

        return view('admin.chat-logs.index', ['logs' => $query->paginate(25)->withQueryString()]);
    }

    public function destroy(ChatLog $chatLog)
    {
        $chatLog->delete();

        return back()->with('success', 'Log dihapus.');
    }

    public function clear()
    {
        ChatLog::query()->delete();

        return back()->with('success', 'Semua log chat dikosongkan.');
    }
}
