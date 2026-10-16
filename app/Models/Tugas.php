<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PengumpulanTugas; // UBAH DISINI: Import model yang benar

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'mk_id', 
        'judul_tugas', 
        'tipe', 
        'deadline', 
        'status'
    ];

    // Otomatis mengenali kolom deadline sebagai objek Waktu/Tanggal (Carbon)
    protected $casts = [
        'deadline' => 'datetime',
    ];

    // Relasi: Tugas menempel pada satu Mata Kuliah (Cukup 1 fungsi saja)
    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }

    // Relasi: Satu Tugas memiliki banyak Arsip Pengumpulan dari mahasiswa (Cukup 1 fungsi saja)
    public function arsipPengumpulan()
    {
        // UBAH DISINI: Ganti ArsipPengumpulan::class menjadi PengumpulanTugas::class
        return $this->hasMany(PengumpulanTugas::class, 'tugas_id');
    }
}