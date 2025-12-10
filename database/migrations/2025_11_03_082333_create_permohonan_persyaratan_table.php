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
        Schema::create('permohonan_persyaratan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->nullable()->constrained()->on('permohonan')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('persyaratan_id')->nullable()->constrained()->on('persyaratan')->onUpdate('cascade')->onDelete('cascade');
            $table->string('value')->nullable();
            $table->enum('status', ['0', '1'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_persyaratan');
    }
};
