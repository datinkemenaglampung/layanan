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
        Schema::create('permohonan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')->constrained()->on('users')->onDelete('cascade');
            $table->foreignId('layanan_id')->constrained()->on('layanan')->onDelete('cascade');
            $table->enum('status', ['dibuat', 'diajukan', 'diproses', 'ditolak', 'selesai'])->default('dibuat'); // menunggu, diproses, selesai, ditolak
            $table->string('kode_satker');
            $table->enum('status_kab', ['1', '0'])->default('0');
            $table->enum('status_prov', ['1', '0'])->default('0');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan');
    }
};
