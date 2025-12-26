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
        Schema::create('verifikasi_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('kode', 6);
            $table->timestamp('kadaluwarsa_pada');
            $table->boolean('terverifikasi')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index(['kode', 'terverifikasi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi_emails');
    }
};
