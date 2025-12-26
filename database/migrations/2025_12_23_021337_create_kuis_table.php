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
        Schema::create('kuis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_kuis');
            $table->text('deskripsi')->nullable();
            $table->string('kode_kuis', 10)->unique();
            $table->string('barcode_path', 500)->nullable();
            $table->integer('waktu_pengerjaan')->default(15);
            $table->boolean('acak_soal')->default(false);
            $table->boolean('acak_opsi')->default(false);
            $table->integer('total_poin')->default(0);
            $table->integer('poin_pilgan')->default(0);
            $table->integer('poin_essay')->default(0);
            $table->enum('status', ['draft', 'aktif', 'selesai', 'arsip'])->default('draft');
            $table->timestamp('mulai_dari')->nullable();
            $table->timestamp('berakhir_pada')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('kode_kuis');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuis');
    }
};
