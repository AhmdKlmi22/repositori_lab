<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Menutup celah Mass Assignment
    protected $fillable = [
        'nama',
        'nomor_induk',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function arsipPengumpulan()
    {
        return $this->hasMany(PengumpulanTugas::class, 'user_id');
    }

    public function kelompok()
    {
        return $this->belongsToMany(Kelompok::class, 'kelompok_mhs', 'mahasiswa_id', 'kelompok_id');
    }

    public function mataKuliahDiambil()
    {
        return $this->belongsToMany(MataKuliah::class, 'peserta_mk', 'user_id', 'mk_id');
    }

    public function asistenMataKuliah()
    {
        return $this->belongsToMany(MataKuliah::class, 'asisten_mk', 'aslab_id', 'mk_id');
    }
}