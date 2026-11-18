<?php

use App\Http\Controllers\Admin\ChatAgentController as AdminChatAgentController;
use App\Http\Controllers\Admin\ChatLogController as AdminChatLogController;
use App\Http\Controllers\Admin\ChatMenuController as AdminChatMenuController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WaLogController as AdminWaLogController;
use App\Http\Controllers\ChatAiController;
use App\Http\Controllers\ChatWidgetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ---------- Publik ----------
Route::get('/', [HomeController::class, 'index'])->name('home');

// ---------- Widget chat (dipakai JavaScript di browser pengunjung) ----------
Route::get('/chat/config', [ChatWidgetController::class, 'config'])->name('chat.config');
Route::post('/chat/log', [ChatWidgetController::class, 'log'])->middleware('throttle:60,1')->name('chat.log');
Route::post('/chat/ask', [ChatAiController::class, 'ask'])->middleware('throttle:20,1')->name('chat.ask');

// ---------- WhatsApp Cloud API (webhook dari Meta) ----------
Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify'])->name('whatsapp.verify');
Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'receive'])->name('whatsapp.webhook');

// ---------- Akun (Breeze) ----------
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ---------- Admin ----------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('chat-menus', AdminChatMenuController::class)->except('show');
    Route::resource('chat-agents', AdminChatAgentController::class)->except('show');
    Route::get('chat-logs', [AdminChatLogController::class, 'index'])->name('chat-logs.index');
    Route::delete('chat-logs/clear', [AdminChatLogController::class, 'clear'])->name('chat-logs.clear');
    Route::delete('chat-logs/{chatLog}', [AdminChatLogController::class, 'destroy'])->name('chat-logs.destroy');
    Route::get('wa-logs', [AdminWaLogController::class, 'index'])->name('wa-logs.index');
    Route::delete('wa-logs/clear', [AdminWaLogController::class, 'clear'])->name('wa-logs.clear');
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('settings', [AdminSettingController::class, 'index'])->name('settings');
    Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::get('profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
});

require __DIR__.'/auth.php';
