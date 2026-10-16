<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = User::with('role');

        if ($search) {
            // Grouping kondisi OR agar tidak membocorkan query lain di masa depan
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        
        return view('laboran.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('laboran.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_induk' => 'required|string|unique:users,nomor_induk',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'nama' => $request->nama,
            'nomor_induk' => $request->nomor_induk,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('laboran.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('laboran.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_induk' => 'required|string|unique:users,nomor_induk,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $data = [
            'nama' => $request->nama,
            'nomor_induk' => $request->nomor_induk,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('laboran.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Mencegah menghapus akun sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('laboran.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        try {
            $user->delete();
            return redirect()->route('laboran.users.index')->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('laboran.users.index')
                ->with('error', 'Gagal menghapus user. Pastikan tidak ada relasi data yang masih terikat (seperti tugas/kelompok).');
        }
    }

    public function template()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=template_user.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['nama', 'nomor_induk', 'password', 'role_id'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ','); // Standar menggunakan koma
            fputcsv($file, ['Contoh Nama', '12345678', 'password123', '4']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id',
        ]);

        $ids = collect($request->ids)
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => $id === (int) auth()->id()) // jangan hapus akun sendiri
            ->values()
            ->all();

        if (empty($ids)) {
            return redirect()->route('laboran.users.index')
                ->with('error', 'Tidak ada user yang bisa dihapus.');
        }

        try {
            $jumlah = User::whereIn('id', $ids)->delete();

            return redirect()->route('laboran.users.index')
                ->with('success', "$jumlah user berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->route('laboran.users.index')
                ->with('error', 'Sebagian user tidak dapat dihapus karena masih memiliki data terkait.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $validRoleIds = Role::pluck('id')->toArray();
        $file = $request->file('file_csv');
        $handle = fopen($file->getPathname(), 'r');
        
        // Deteksi Delimiter otomatis (Mengatasi masalah save as Excel di Windows region Indonesia)
        $baris = fgets($handle);
        $delimiter = substr_count($baris, ';') > substr_count($baris, ',') ? ';' : ',';
        rewind($handle);
        
        // Lewati baris header
        fgetcsv($handle, 1000, $delimiter);

        $berhasil = 0;
        $dilewati = 0;

        while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            $nama = trim($data[0] ?? '');
            $nomorInduk = trim($data[1] ?? '');
            $password = trim($data[2] ?? '');
            $roleId = (int) ($data[3] ?? 4);

            // Validasi data kosong atau Role ID yang tidak valid
            if ($nama === '' || $nomorInduk === '' || !in_array($roleId, $validRoleIds)) {
                $dilewati++;
                continue;
            }

            $user = User::where('nomor_induk', $nomorInduk)->first();

            if ($user) {
                // User sudah ada, perbarui tanpa mengubah password (kecuali jika mau di-reset secara manual)
                $user->update([
                    'nama' => $nama,
                    'role_id' => $roleId
                ]);
            } else {
                // User belum ada, maka Create
                User::create([
                    'nama' => $nama,
                    'nomor_induk' => $nomorInduk,
                    'password' => Hash::make($password !== '' ? $password : 'password123'),
                    'role_id' => $roleId,
                ]);
            }
            $berhasil++;
        }
        fclose($handle);

        return redirect()->route('laboran.users.index')
            ->with('success', "Import selesai: $berhasil user berhasil diproses, $dilewati baris dilewati (kosong/role tidak valid).");
    }
}