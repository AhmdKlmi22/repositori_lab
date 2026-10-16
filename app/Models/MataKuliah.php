<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $fillable = [
        'kode_mk',
        'nama_mk',
        'hari',
        'waktu',
        'dosen_id',
    ];

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function aslab(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'asisten_mk', 'mk_id', 'aslab_id');
    }

    public function asisten(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'asisten_mk', 'mk_id', 'aslab_id');
    }

    public function mahasiswa(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'peserta_mk', 'mk_id', 'user_id');
    }

    public function peserta(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'peserta_mk', 'mk_id', 'user_id');
    }

    // Bagian ini sudah diubah dari Modul::class menjadi ModulPraktikum::class
    public function modul(): HasMany
    {
        return $this->hasMany(ModulPraktikum::class, 'mk_id');
    }

    // Bagian ini juga sudah diubah dari Modul::class menjadi ModulPraktikum::class
    public function modulPraktikum(): HasMany
    {
        return $this->hasMany(ModulPraktikum::class, 'mk_id');
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class, 'mk_id');
    }

    public function kelompok(): HasMany
    {
        return $this->hasMany(Kelompok::class, 'mk_id');
    }
}