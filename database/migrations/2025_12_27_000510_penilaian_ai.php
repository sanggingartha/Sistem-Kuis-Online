<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_ai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jawaban_essay_id')->constrained('jawaban_essay')->onDelete('cascade');
            $table->text('prompt_dikirim'); // Prompt yang dikirim ke AI
            $table->longText('respon_ai'); // Respon mentah dari AI
            $table->integer('waktu_proses')->nullable(); // Waktu proses dalam milidetik
            $table->enum('status_request', ['sukses', 'gagal', 'timeout'])->default('sukses');
            $table->text('error_message')->nullable(); // Jika ada error
            $table->string('model_versi', 50)->default('gemini-2.5-flash'); // Versi model AI
            $table->integer('token_digunakan')->nullable(); // Jumlah token yang digunakan
            $table->timestamps();

            $table->index('jawaban_essay_id');
            $table->index('status_request');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_ai');
    }
};