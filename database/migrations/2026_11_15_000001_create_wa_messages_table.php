<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Riwayat percakapan WhatsApp (Cloud API): pesan masuk & balasan bot. */
    public function up(): void
    {
        Schema::create('wa_messages', function (Blueprint $table) {
            $table->id();
            $table->string('wa_message_id')->nullable()->unique(); // id pesan dari Meta (cegah dobel)
            $table->string('direction', 3); // in = dari pelanggan, out = balasan bot
            $table->string('wa_id', 32); // nomor pelanggan (format internasional, tanpa +)
            $table->string('contact_name')->nullable();
            $table->text('body');
            $table->string('status', 16)->default('received'); // received|answered|sent|failed|ignored
            $table->boolean('is_ai')->default(false);
            $table->timestamps();
            $table->index(['wa_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_messages');
    }
};
