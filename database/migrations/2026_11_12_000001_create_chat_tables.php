<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_menus', function (Blueprint $table) {
            $table->id();
            $table->string('label', 100);
            // answer = bot menjawab teks; agents = tampilkan daftar tim WhatsApp; link = buka URL
            $table->string('action', 20)->default('answer');
            $table->text('answer')->nullable();
            $table->text('keywords')->nullable(); // kata kunci untuk ketikan bebas, pisahkan koma
            $table->string('url', 255)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('chat_agents', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('label', 100)->nullable(); // mis. Sales, Customer Service
            $table->string('wa_number', 20); // format internasional tanpa +, mis. 62812xxxxxxx
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('chat_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20); // menu, text, agent
            $table->string('content', 500)->nullable(); // label menu / teks ketikan / nama agen
            $table->boolean('matched')->default(false); // khusus type=text: ketemu jawaban atau fallback
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_logs');
        Schema::dropIfExists('chat_agents');
        Schema::dropIfExists('chat_menus');
        Schema::dropIfExists('settings');
    }
};
