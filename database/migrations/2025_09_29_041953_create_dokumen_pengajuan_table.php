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
        Schema::create('dokumen_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->nullable()->constrained()->on('pengajuan')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('persyaratan_id')->nullable()->constrained()->on('persyaratan')->onUpdate('cascade')->onDelete('cascade');
            $table->string('nilai_input');
            $table->enum('status', ['sesuai', 'tidak sesuai'])->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_pengajuan');
    }
};
