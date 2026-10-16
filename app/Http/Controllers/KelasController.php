<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MataKuliah;
use App\Models\PengumpulanTugas;

class KelasController extends Controller
{
    private function roleName(): string
    {
        return Auth::user()->role->nama_role ?? '';
    }

    // Satu sumber kebenaran untuk akses ruang kelas
    private function canAccessKelas(MataKuliah $mk): bool
    {
        $user = Auth::user();
        $role = $this->roleName();

        if ($role === 'Laboran') return true;
        if ($role === 'Dosen') return (int) $mk->dosen_id === (int) $user->id;
        if ($role === 'Aslab') return $mk->asisten()->where('users.id', $user->id)->exists();

        // Mahasiswa: hanya jika terdaftar sebagai peserta
        return $mk->peserta()->where('users.id', $user->id)->exists();
    }

    public function index()
    {
        $user = Auth::user();
        $role = $this->roleName();

        if ($role === 'Mahasiswa') {
            $kelasSaya = $user->mataKuliahDiambil()->with('dosen')->get();

            $kelasTersedia = MataKuliah::with('dosen')
                ->whereDoesntHave('peserta', fn ($q) => $q->where('users.id', $user->id))
                ->get();

            return view('kelas.mahasiswa_index', compact('kelasSaya', 'kelasTersedia'));
        }

        $query = MataKuliah::with(['peserta', 'asisten']);

        if ($role !== 'Laboran') {
            $query->where(function ($q) use ($user) {
                $q->where('dosen_id', $user->id)
                  ->orWhereHas('asisten', fn ($a) => $a->where('users.id', $user->id));
            });
        }

        $mataKuliah = $query->get();

        return view('kelas.dosen_index', compact('mataKuliah'));
    }

    public function show($id)
    {
        $mataKuliah = MataKuliah::with([
            'dosen',
            'asisten',
            'peserta',
            'modul',
            'tugas.arsipPengumpulan',
            'kelompok.anggota',
        ])->findOrFail($id);

        abort_unless($this->canAccessKelas($mataKuliah), 403, 'Anda tidak terdaftar pada mata kuliah ini.');

        $role = $this->roleName() ?: 'Mahasiswa';

        return view('kelas.show', compact('mataKuliah', 'role'));
    }

    public function enroll(Request $request, $id)
    {
        $mk = MataKuliah::findOrFail($id);

        // syncWithoutDetaching mencegah duplikat jika tombol ditekan dua kali
        $mk->peserta()->syncWithoutDetaching([Auth::id()]);

        return back()->with('success', 'Berhasil bergabung ke mata kuliah ' . $mk->nama_mk);
    }

    public function rekapPengumpulan($id, Request $request)
    {
        $mataKuliah = MataKuliah::with(['dosen', 'peserta', 'kelompok.anggota', 'tugas'])
            ->findOrFail($id);

        abort_unless(
            in_array($this->roleName(), ['Dosen', 'Aslab', 'Laboran'], true) && $this->canAccessKelas($mataKuliah),
            403,
            'Anda tidak berhak melihat rekap mata kuliah ini.'
        );

        $selectedTugas = $request->filled('tugas_id')
            ? $mataKuliah->tugas->firstWhere('id', (int) $request->tugas_id)
            : $mataKuliah->tugas->first();

        $pengumpulan = collect();
        $totalTarget = 0;
        $sudahMengumpulkan = 0;
        $belumMengumpulkan = 0;

        if ($selectedTugas) {
            // Terbaru di depan. unique() mempertahankan item pertama, yaitu yang terbaru
            $submissions = PengumpulanTugas::where('tugas_id', $selectedTugas->id)
                ->with('mahasiswa')
                ->orderBy('waktu_kumpul', 'desc')
                ->get();

            if ($selectedTugas->tipe === 'Kelompok') {
                $totalTarget = $mataKuliah->kelompok->count();

                $perKelompok = $submissions->unique('kelompok_id')->keyBy('kelompok_id');
                $sudahMengumpulkan = $perKelompok->count();
                $belumMengumpulkan = max(0, $totalTarget - $sudahMengumpulkan);

                // Peta per mahasiswa, untuk kebutuhan tabel
                $pengumpulan = $mataKuliah->peserta->mapWithKeys(function ($mhs) use ($mataKuliah, $perKelompok) {
                    $kelompok = $mataKuliah->kelompok->first(
                        fn ($k) => $k->anggota->contains('id', $mhs->id)
                    );

                    return [$mhs->id => $kelompok ? $perKelompok->get($kelompok->id) : null];
                })->filter();
            } else {
                $totalTarget = $mataKuliah->peserta->count();

                $perMahasiswa = $submissions->unique('user_id')->keyBy('user_id');
                $sudahMengumpulkan = $perMahasiswa->count();
                $belumMengumpulkan = max(0, $totalTarget - $sudahMengumpulkan);

                $pengumpulan = $perMahasiswa;
            }
        }

        return view('kelas.rekap_pengumpulan', compact(
            'mataKuliah',
            'selectedTugas',
            'pengumpulan',
            'totalTarget',
            'sudahMengumpulkan',
            'belumMengumpulkan'
        ));
    }
}