<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChatMenuRequest;
use App\Models\ChatMenu;

class ChatMenuController extends Controller
{
    public function index()
    {
        return view('admin.chat-menus.index', [
            'menus' => ChatMenu::orderBy('sort_order')->orderBy('id')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.chat-menus.create', ['menu' => new ChatMenu(['action' => 'answer', 'is_active' => true])]);
    }

    public function store(ChatMenuRequest $request)
    {
        ChatMenu::create($request->validated());

        return redirect()->route('admin.chat-menus.index')->with('success', 'Menu chat ditambahkan.');
    }

    public function edit(ChatMenu $chatMenu)
    {
        return view('admin.chat-menus.edit', ['menu' => $chatMenu]);
    }

    public function update(ChatMenuRequest $request, ChatMenu $chatMenu)
    {
        $chatMenu->update($request->validated());

        return redirect()->route('admin.chat-menus.index')->with('success', 'Menu chat diperbarui.');
    }

    public function destroy(ChatMenu $chatMenu)
    {
        $chatMenu->delete();

        return redirect()->route('admin.chat-menus.index')->with('success', 'Menu chat dihapus.');
    }
}
