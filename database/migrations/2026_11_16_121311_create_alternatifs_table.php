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
        Schema::create('alternatifs', function (Blueprint $table) {
            $table->id();
             $table->string('kode')->unique();
             $table->string('NIK')->unique();
            $table->string('name');
            $table->string('alamat')->nullable();
            $table->string('nomor')->nullable();
            $table->enum('status', ['Verifikasi', 'Belum Verifikasi'])->default('Belum Verifikasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alternatifs');
    }
};
