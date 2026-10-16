<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mk', 50)->unique();
            $table->foreignId('dosen_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_mk', 100);
            $table->tinyInteger('semester')->default(1);
            $table->string('hari', 20)->default('Senin');
            $table->string('waktu', 50)->default('08:00 - 10:00 WIT');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};