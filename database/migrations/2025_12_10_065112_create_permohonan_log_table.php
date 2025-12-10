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
        Schema::create('permohonan_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->nullable()->constrained()->on('permohonan')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('users_id')->constrained()->on('users')->onDelete('cascade');
            $table->text('catatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_log');
    }
};
