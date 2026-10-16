<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modul_praktikum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mk_id')->constrained('mata_kuliah')->onDelete('cascade');
            $table->string('judul_modul', 150);
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modul_praktikum');
    }
};