<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jawaban_essay', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasil_kuis_id')->constrained('hasil_kuis')->onDelete('cascade');
            $table->foreignId('soal_id')->constrained('soal_essay')->onDelete('cascade');
            $table->text('jawaban_siswa');
            $table->integer('poin_maksimal')->default(0);
            $table->text('feedback_ai')->nullable();
            $table->decimal('skor_ai', 5, 2)->nullable();
            $table->integer('poin_diperoleh')->default(0);
            $table->enum('status_penilaian', ['belum_dinilai', 'sedang_proses', 'sudah_dinilai', 'error'])->default('belum_dinilai');
            $table->enum('nilai_oleh', ['AI', 'pengajar', 'sistem'])->default('AI');
            $table->timestamp('dijawab_pada')->useCurrent();
            $table->timestamp('dinilai_pada')->nullable();
            $table->timestamps();

            $table->index('hasil_kuis_id');
            $table->index('soal_id');
            $table->index('status_penilaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_essay');
    }
};
