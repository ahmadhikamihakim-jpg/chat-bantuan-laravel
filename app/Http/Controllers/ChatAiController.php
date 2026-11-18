<?php

namespace App\Http\Controllers;

use App\Models\ChatLog;
use App\Services\ChatAiService;
use Illuminate\Http\Request;

class ChatAiController extends Controller
{
    /** Jawab ketikan bebas pengunjung memakai AI (bila mode AI aktif di Pengaturan). */
    public function ask(Request $request, ChatAiService $ai)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['nullable', 'array', 'max:12'],
            'history.*.role' => ['nullable', 'string', 'in:user,model'],
            'history.*.text' => ['nullable', 'string', 'max:500'],
        ]);

        if (! ChatAiService::enabled()) {
            return response()->json(['ok' => false, 'reason' => 'disabled']);
        }

        $answer = $ai->ask($data['message'], $data['history'] ?? []);

        ChatLog::create([
            'type' => 'text',
            'content' => $data['message'],
            'matched' => $answer !== null,
        ]);

        if ($answer === null) {
            return response()->json(['ok' => false, 'reason' => 'unavailable']);
        }

        return response()->json(['ok' => true, 'answer' => $answer]);
    }
}
