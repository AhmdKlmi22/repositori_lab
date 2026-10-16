<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mk_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->string('judul_tugas', 150);
            $table->enum('tipe', ['Individu', 'Kelompok'])->default('Individu');
            $table->dateTime('deadline');
            $table->enum('status', ['Buka', 'Tutup'])->default('Buka')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};