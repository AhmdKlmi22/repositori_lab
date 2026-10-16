<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Role
        $roles = [
            ['id' => 1, 'nama_role' => 'Laboran'],
            ['id' => 2, 'nama_role' => 'Dosen'],
            ['id' => 3, 'nama_role' => 'Aslab'],
            ['id' => 4, 'nama_role' => 'Mahasiswa'],
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(['id' => $role['id']], $role);
        }

       
        User::firstOrCreate(
            ['nomor_induk' => '1419019601'],
            ['nama' => 'Muhammad Jundullah, S.Kom., M.Kom.', 'password' => Hash::make('password123'), 'role_id' => 1]
        );
    }
}