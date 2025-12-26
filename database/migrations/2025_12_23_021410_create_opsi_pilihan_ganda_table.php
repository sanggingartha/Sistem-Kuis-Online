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
        Schema::create('opsi_pilihan_ganda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soal_id')->constrained('soal_pilihan_ganda')->onDelete('cascade');
            $table->text('teks_opsi');
            $table->boolean('opsi_benar')->default(false);
            $table->integer('poin')->default(0);
            $table->integer('urutan')->default(1);
            $table->timestamps();

            $table->index(['soal_id', 'urutan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opsi_pilihan_ganda');
    }
};
