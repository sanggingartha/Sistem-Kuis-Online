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
        Schema::create('hasil_kuis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuis_id')->constrained('kuis')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('waktu_mulai')->useCurrent();
            $table->timestamp('waktu_selesai')->nullable();
            $table->integer('durasi_pengerjaan')->nullable();
            $table->integer('total_poin')->default(0);
            $table->integer('poin_diperoleh')->default(0);
            $table->integer('poin_pilgan')->default(0);
            $table->integer('poin_essay')->default(0);
            $table->decimal('persentase', 5, 2)->default(0.00);
            $table->enum('status', ['sedang_mengerjakan', 'selesai', 'waktu_habis'])->default('sedang_mengerjakan');
            $table->integer('percobaan_ke')->default(1);
            $table->timestamps();

            $table->index(['kuis_id', 'user_id']);
            $table->index('status');
            $table->unique(['kuis_id', 'user_id', 'percobaan_ke'], 'unique_attempt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_kuis');
    }
};
