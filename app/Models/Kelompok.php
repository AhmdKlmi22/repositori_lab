<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    use HasFactory;

    protected $table = 'kelompok';
    protected $guarded = [];

    public function anggota()
    {
        // Harus identik pivotnya dengan yang ada di User::kelompok()
        return $this->belongsToMany(User::class, 'kelompok_mhs', 'kelompok_id', 'mahasiswa_id');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }
}