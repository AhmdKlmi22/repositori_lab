<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\MataKuliah;
use App\Models\PengumpulanTugas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KelompokController extends Controller
{
    // ================= HELPER OTORISASI ================= //
    private function roleName(): string
    {
        return Auth::user()->role?->nama_role ?? '';
    }

    // Laboran: semua mata kuliah. Dosen: pengampu. Aslab: asisten. Lainnya: tidak.
    private function canManageMk(MataKuliah $mk): bool
    {
        $user = Auth::user();

        return match ($this->roleName()) {
            'Laboran' => true,
            'Dosen'   => (int) $mk->dosen_id === (int) $user->id,
            'Aslab'   => $mk->asisten()->where('users.id', $user->id)->exists(),
            default   => false,
        };
    }

    private function authorizeMk(MataKuliah $mk): void
    {
        abort_unless($this->canManageMk($mk), 403, 'Anda tidak berhak mengelola kelompok mata kuliah ini.');
    }

    // Memeriksa hak akses lewat mata kuliah induk, lalu mengembalikannya
    private function authorizeKelompok(Kelompok $kelompok): MataKuliah
    {
        $kelompok->loadMissing('mataKuliah');
        $this->authorizeMk($kelompok->mataKuliah);

        return $kelompok->mataKuliah;
    }

    private function isMahasiswa(User $user): bool
    {
        return ($user->role?->nama_role ?? '') === 'Mahasiswa';
    }

    // Satu mahasiswa hanya boleh berada di satu kelompok per mata kuliah
    private function pindahkanKeKelompok(User $mhs, Kelompok $tujuan, MataKuliah $mk): void
    {
        $idKelompokMk = Kelompok::where('mk_id', $mk->id)->pluck('id')->all();

        $mhs->kelompok()->detach($idKelompokMk);
        $mhs->kelompok()->attach($tujuan->id);
    }
    // ==================================================== //

    public function template()
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=template_kelompok.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['nama_kelompok', 'nomor_induk'], ',');
            fputcsv($file, ['Kelompok 1', '202255202001'], ',');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // POST /kelas/{id}/kelompok  (id = mata kuliah)
    public function store(Request $request, $id)
    {
        $mk = MataKuliah::findOrFail($id);
        $this->authorizeMk($mk);

        $validated = $request->validate([
            'nama_kelompok' => 'required|string|max:100',
        ]);

        $nama = trim($validated['nama_kelompok']);

        if (Kelompok::where('mk_id', $mk->id)->where('nama_kelompok', $nama)->exists()) {
            return back()->with('error', "Kelompok \"$nama\" sudah ada di mata kuliah ini.");
        }

        Kelompok::create([
            'mk_id' => $mk->id,
            'nama_kelompok' => $nama,
        ]);

        return back()->with('success', "Kelompok $nama berhasil dibuat.");
    }

    // POST /kelas/{id}/kelompok/import  (id = mata kuliah)
    public function import(Request $request, $id)
    {
        $mk = MataKuliah::findOrFail($id);
        $this->authorizeMk($mk);

        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $handle = fopen($request->file('file_csv')->getPathname(), 'r');

        $baris = fgets($handle);
        if ($baris === false) {
            fclose($handle);
            return back()->with('error', 'File CSV kosong.');
        }

        // Deteksi delimiter, untuk CSV yang disimpan Excel dengan locale Indonesia
        $delimiter = substr_count($baris, ';') > substr_count($baris, ',') ? ';' : ',';
        rewind($handle);
        fgetcsv($handle, 1000, $delimiter); // lewati header

        $pesertaIds = array_map('intval', $mk->peserta()->pluck('users.id')->all());

        $berhasil = 0;
        $masalah = [];
        $nomorBaris = 1;

        DB::transaction(function () use ($handle, $delimiter, $mk, $pesertaIds, &$berhasil, &$masalah, &$nomorBaris) {
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $nomorBaris++;

                $namaKelompok = trim($data[0] ?? '');
                $nomorInduk = trim($data[1] ?? '');

                if ($namaKelompok === '' || $nomorInduk === '') {
                    continue; // baris kosong dilewati
                }

                $mhs = User::where('nomor_induk', $nomorInduk)->first();

                if (!$mhs || !$this->isMahasiswa($mhs)) {
                    $masalah[] = "Baris $nomorBaris: NIM $nomorInduk tidak ditemukan sebagai mahasiswa.";
                    continue;
                }

                if (!in_array((int) $mhs->id, $pesertaIds, true)) {
                    $masalah[] = "Baris $nomorBaris: NIM $nomorInduk belum terdaftar di mata kuliah ini.";
                    continue;
                }

                $kelompok = Kelompok::firstOrCreate([
                    'mk_id' => $mk->id,
                    'nama_kelompok' => $namaKelompok,
                ]);

                $this->pindahkanKeKelompok($mhs, $kelompok, $mk);
                $berhasil++;
            }
        });

        fclose($handle);

        $pesan = "Import selesai: $berhasil anggota diproses.";

        if ($masalah) {
            $contoh = implode(' ', array_slice($masalah, 0, 5));
            return back()->with('error', $pesan . ' ' . count($masalah) . ' baris dilewati. ' . $contoh);
        }

        return back()->with('success', $pesan);
    }

    // POST /kelompok/{id}/add-member  (id = kelompok)
    public function addMember(Request $request, $id)
    {
        $kelompok = Kelompok::findOrFail($id);
        $mk = $this->authorizeKelompok($kelompok);

        $validated = $request->validate([
            'mahasiswa_id' => 'required|integer|exists:users,id',
        ]);

        $mhs = User::findOrFail($validated['mahasiswa_id']);

        if (!$this->isMahasiswa($mhs)) {
            return back()->with('error', 'Hanya akun mahasiswa yang dapat dimasukkan ke kelompok.');
        }

        if (!$mk->peserta()->where('users.id', $mhs->id)->exists()) {
            return back()->with('error', 'Mahasiswa tersebut belum terdaftar di mata kuliah ini.');
        }

        $this->pindahkanKeKelompok($mhs, $kelompok, $mk);

        return back()->with('success', "{$mhs->nama} berhasil dimasukkan ke {$kelompok->nama_kelompok}.");
    }

    // DELETE /kelompok/{id}/remove-member/{mhs_id}  (id = kelompok)
    public function removeMember($id, $mhs_id)
    {
        $kelompok = Kelompok::findOrFail($id);
        $this->authorizeKelompok($kelompok);

        $kelompok->anggota()->detach((int) $mhs_id);

        return back()->with('success', 'Anggota berhasil dikeluarkan dari kelompok.');
    }

    // DELETE /kelompok/{id}  (id = kelompok)
    public function destroy($id)
    {
        $kelompok = Kelompok::findOrFail($id);
        $this->authorizeKelompok($kelompok);

        // Kelompok yang sudah punya berkas tidak dihapus, agar arsip pengumpulan tidak kehilangan induknya
        if (PengumpulanTugas::where('kelompok_id', $kelompok->id)->exists()) {
            return back()->with('error', 'Kelompok ini sudah memiliki berkas pengumpulan, sehingga tidak dapat dihapus.');
        }

        DB::transaction(function () use ($kelompok) {
            $kelompok->anggota()->detach();
            $kelompok->delete();
        });

        return back()->with('success', 'Kelompok berhasil dihapus.');
    }
}