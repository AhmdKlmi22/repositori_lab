<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    
    // Matuhkan timestamps jika tabel role tidak memiliki created_at/updated_at
    public $timestamps = false; 

    protected $fillable = [
        'nama_role',
    ];

    // Relasi: Satu Role memiliki banyak User
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}