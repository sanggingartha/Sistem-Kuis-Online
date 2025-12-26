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
        Schema::create('soal_essay', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuis_id')->constrained('kuis')->onDelete('cascade');
            $table->text('pertanyaan');
            $table->string('gambar_url', 500)->nullable();
            $table->text('jawaban_acuan')->nullable();
            $table->text('rubrik_penilaian')->nullable();
            $table->integer('urutan')->default(1);
            $table->integer('poin_maksimal')->default(20);
            $table->timestamps();

            $table->index(['kuis_id', 'urutan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_essay');
    }
};
