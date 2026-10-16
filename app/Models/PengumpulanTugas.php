<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumpulanTugas extends Model
{
    use HasFactory;

    // Pastikan ini sesuai dengan nama tabel di database kamu
    protected $table = 'arsip_pengumpulan'; 

    protected $fillable = [
        'tugas_id',
        'user_id',
        'kelompok_id',
        'file_name',
        'file_path',
        'waktu_kumpul',
        'nilai',
        'catatan'
    ];

    // Relasi ke User (Mahasiswa yang mengumpulkan)
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Kelompok
    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id');
    }
    
    // Relasi ke Tugas
    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }
}