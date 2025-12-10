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
        Schema::create('layanan_persyaratan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_id')->nullable()->constrained()->on('layanan')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('persyaratan_id')->nullable()->constrained()->on('persyaratan')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('wajib')->default(true);
            $table->enum('uploaded_level', ['0', '1'])->default('0');
            $table->integer('urut');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanan_persyaratan');
    }
};
