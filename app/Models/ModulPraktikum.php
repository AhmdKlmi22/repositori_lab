<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulPraktikum extends Model
{
    use HasFactory;

    protected $table = 'modul_praktikum'; // Menyesuaikan dengan nama tabel di database SQL

    protected $fillable = [
        'mk_id',
        'judul_modul',
        'file_name',
        'file_path',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }
}