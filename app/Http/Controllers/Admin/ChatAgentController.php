<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChatAgentRequest;
use App\Models\ChatAgent;

class ChatAgentController extends Controller
{
    public function index()
    {
        return view('admin.chat-agents.index', [
            'agents' => ChatAgent::orderBy('sort_order')->orderBy('id')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.chat-agents.create', ['agent' => new ChatAgent(['is_active' => true])]);
    }

    public function store(ChatAgentRequest $request)
    {
        ChatAgent::create($request->validated());

        return redirect()->route('admin.chat-agents.index')->with('success', 'Tim WhatsApp ditambahkan.');
    }

    public function edit(ChatAgent $chatAgent)
    {
        return view('admin.chat-agents.edit', ['agent' => $chatAgent]);
    }

    public function update(ChatAgentRequest $request, ChatAgent $chatAgent)
    {
        $chatAgent->update($request->validated());

        return redirect()->route('admin.chat-agents.index')->with('success', 'Tim WhatsApp diperbarui.');
    }

    public function destroy(ChatAgent $chatAgent)
    {
        $chatAgent->delete();

        return redirect()->route('admin.chat-agents.index')->with('success', 'Tim WhatsApp dihapus.');
    }
}
