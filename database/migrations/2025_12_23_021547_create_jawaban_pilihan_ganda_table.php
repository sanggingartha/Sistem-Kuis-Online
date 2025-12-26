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
        Schema::create('jawaban_pilihan_ganda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasil_kuis_id')->constrained('hasil_kuis')->onDelete('cascade');
            $table->foreignId('soal_id')->constrained('soal_pilihan_ganda')->onDelete('cascade');
            $table->foreignId('opsi_id')->nullable()->constrained('opsi_pilihan_ganda')->onDelete('set null');
            $table->integer('poin_diperoleh')->default(0);
            $table->boolean('benar')->default(false);
            $table->timestamp('dijawab_pada')->useCurrent();
            $table->timestamps();

            $table->index('hasil_kuis_id');
            $table->index('soal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_pilihan_ganda');
    }
};
