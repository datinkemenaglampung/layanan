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
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bidang_id')->nullable()->constrained()->on('bidang')->onUpdate('cascade')->onDelete('cascade');
            $table->string('nama_layanan');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->enum('status', [1, 0])->default(1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};
