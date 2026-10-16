<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Tugas;
use App\Models\MataKuliah;
use App\Models\PengumpulanTugas;

class TugasController extends Controller
{
    // Berkas disimpan di storage/app/private, tidak bisa diakses langsung lewat URL
    private const DISK = 'local';
    private const STAFF_ROLES = ['Dosen', 'Aslab', 'Laboran'];

    // ================= HELPER ================= //
    private function roleName(): string
    {
        return Auth::user()->role->nama_role ?? '';
    }

    private function authorizeStaff(): void
    {
        abort_unless(in_array($this->roleName(), self::STAFF_ROLES, true), 403, 'Anda tidak berhak mengelola tugas.');
    }

    private function canManageTugas(Tugas $tugas): bool
    {
        $user = Auth::user();
        $role = $this->roleName();

        if ($role === 'Laboran') return true;
        if ($role === 'Dosen') return (int) $tugas->mataKuliah->dosen_id === (int) $user->id;
        if ($role === 'Aslab') return $tugas->mataKuliah->asisten()->where('users.id', $user->id)->exists();

        return false;
    }

    private function authorizeTugas(Tugas $tugas): void
    {
        abort_unless($this->canManageTugas($tugas), 403, 'Anda tidak berhak mengelola tugas ini.');
    }

    private function getMataKuliahDikelola()
    {
        $user = Auth::user();
        $role = $this->roleName();

        if ($role === 'Dosen') return MataKuliah::where('dosen_id', $user->id)->get();
        if ($role === 'Aslab') return $user->asistenMataKuliah()->get();
        if ($role === 'Laboran') return MataKuliah::all();

        return collect(); // Mahasiswa tidak mengelola mata kuliah apa pun
    }
    // ========================================== //

    public function index()
    {
        $user = Auth::user();
        $role = $this->roleName();

        $query = Tugas::with(['mataKuliah.kelompok.anggota', 'arsipPengumpulan'])->latest();

        if ($role === 'Mahasiswa') {
            $query->whereIn('mk_id', $user->mataKuliahDiambil()->pluck('mata_kuliah.id'));
        } elseif ($role === 'Dosen') {
            $query->whereIn('mk_id', MataKuliah::where('dosen_id', $user->id)->pluck('id'));
        } elseif ($role === 'Aslab') {
            $query->whereIn('mk_id', $user->asistenMataKuliah()->pluck('mata_kuliah.id'));
        }

        $tugas = $query->paginate(15);
        return view('tugas.index', compact('tugas', 'role'));
    }

    public function create()
    {
        $this->authorizeStaff();

        $mataKuliah = $this->getMataKuliahDikelola();
        $role = $this->roleName();

        return view('tugas.create', compact('mataKuliah', 'role'));
    }

    public function store(Request $request)
    {
        $this->authorizeStaff();

        $validMkIds = $this->getMataKuliahDikelola()->pluck('id')->all();

        $request->validate([
            'mk_id' => ['required', Rule::in($validMkIds)],
            'judul_tugas' => 'required|string|max:255',
            'tipe' => 'required|in:Individu,Kelompok',
            'deadline' => 'required|date',
        ]);

        Tugas::create([
            'mk_id' => $request->mk_id,
            'judul_tugas' => $request->judul_tugas,
            'tipe' => $request->tipe,
            'deadline' => $request->deadline,
            'status' => 'Buka',
        ]);

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function edit($id)
    {
        $this->authorizeStaff();

        $tugas = Tugas::with('mataKuliah')->findOrFail($id);
        $this->authorizeTugas($tugas);

        $mataKuliah = $this->getMataKuliahDikelola();
        $role = $this->roleName();

        return view('tugas.edit', compact('tugas', 'mataKuliah', 'role'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeStaff();

        $tugas = Tugas::with('mataKuliah')->findOrFail($id);
        $this->authorizeTugas($tugas);

        $validMkIds = $this->getMataKuliahDikelola()->pluck('id')->all();

        $request->validate([
            'mk_id' => ['required', Rule::in($validMkIds)],
            'judul_tugas' => 'required|string|max:255',
            'tipe' => 'required|in:Individu,Kelompok',
            'deadline' => 'required|date',
            'status' => 'required|in:Buka,Tutup',
        ]);

        $tugas->update([
            'mk_id' => $request->mk_id,
            'judul_tugas' => $request->judul_tugas,
            'tipe' => $request->tipe,
            'deadline' => $request->deadline,
            'status' => $request->status,
        ]);

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tugas = Tugas::with(['mataKuliah', 'arsipPengumpulan'])->findOrFail($id);
        $this->authorizeTugas($tugas);

        $paths = $tugas->arsipPengumpulan->pluck('file_path')->filter()->all();

        DB::transaction(function () use ($tugas) {
            $tugas->arsipPengumpulan()->delete();
            $tugas->delete();
        });

        // File dihapus hanya setelah transaksi database berhasil
        foreach ($paths as $path) {
            Storage::disk(self::DISK)->delete($path);
        }

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil dihapus.');
    }

    public function pengumpulan($id)
    {
        $tugas = Tugas::with('mataKuliah')->findOrFail($id);
        $this->authorizeTugas($tugas);

        $role = $this->roleName();

        $pengumpulan = PengumpulanTugas::with(['mahasiswa', 'kelompok'])
            ->where('tugas_id', $tugas->id)
            ->orderBy('waktu_kumpul', 'desc')
            ->paginate(15);

        return view('tugas.pengumpulan', compact('tugas', 'pengumpulan', 'role'));
    }

    public function kumpul(Request $request, $id)
    {
        $request->validate([
            'file_tugas' => 'required|file|max:5120|extensions:pdf,doc,docx,xls,xlsx,pkt,pka,zip,rar',
        ], [
            'file_tugas.required'   => 'File tugas wajib diunggah.',
            'file_tugas.max'        => 'Ukuran file maksimal 5 MB.',
            'file_tugas.extensions' => 'Format file ditolak! Hanya boleh mengunggah file PDF, Word, Excel, Packet Tracer (.pkt/.pka), atau ZIP/RAR.',
        ]);

        $tugas = Tugas::with(['mataKuliah.kelompok.anggota'])->findOrFail($id);
        $user = Auth::user();

        $terdaftar = $tugas->mataKuliah->peserta()->where('users.id', $user->id)->exists();
        if (!$terdaftar) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar di mata kuliah ini.');
        }

        if ($tugas->status === 'Tutup' || ($tugas->deadline && now()->greaterThan($tugas->deadline))) {
            return redirect()->back()->with('error', 'Gagal mengumpulkan: Waktu pengumpulan tugas sudah ditutup atau melewati deadline.');
        }

        $tipe = ucfirst($tugas->tipe);
        $judulTugas = Str::slug($tugas->judul_tugas, '_');
        $extension = $request->file('file_tugas')->getClientOriginalExtension();

        if ($tipe === 'Kelompok') {
            $kelompok = $tugas->mataKuliah->kelompok
                ->first(fn ($k) => $k->anggota->contains('id', $user->id));

            if (!$kelompok) {
                return redirect()->back()->with('error', 'Anda belum tergabung dalam kelompok untuk mata kuliah ini.');
            }

            $namaKelompok = Str::slug($kelompok->nama_kelompok, '_');
            $fileName = "{$tipe}_{$judulTugas}_{$namaKelompok}.{$extension}";
            $matchThese = ['tugas_id' => $tugas->id, 'kelompok_id' => $kelompok->id];
            $kelompokId = $kelompok->id;
        } else {
            $namaUser = Str::slug($user->nama ?? 'Mahasiswa', '_');
            $nomorInduk = Str::slug($user->nomor_induk ?? 'NIM', '_');
            $fileName = "{$tipe}_{$judulTugas}_{$nomorInduk}_{$namaUser}.{$extension}";
            $matchThese = ['tugas_id' => $tugas->id, 'user_id' => $user->id];
            $kelompokId = null;
        }

        // Simpan berkas baru dulu, baru hapus berkas lama. Jika penyimpanan gagal, berkas lama tetap ada.
        $oldPath = PengumpulanTugas::where($matchThese)->value('file_path');

        $filePath = $request->file('file_tugas')->storeAs('pengumpulan_tugas', $fileName, self::DISK);

        PengumpulanTugas::updateOrCreate(
            $matchThese,
            [
                'user_id'      => $user->id,
                'kelompok_id'  => $kelompokId,
                'file_name'    => $fileName,
                'file_path'    => $filePath,
                'waktu_kumpul' => now(),
            ]
        );

        if ($oldPath && $oldPath !== $filePath) {
            Storage::disk(self::DISK)->delete($oldPath);
        }

        return redirect()->back()->with('success', "File tugas $tipe berhasil dikumpulkan!");
    }

    public function download($pengumpulan_id)
    {
        $pengumpulan = PengumpulanTugas::with(['kelompok.anggota', 'tugas.mataKuliah'])
            ->findOrFail($pengumpulan_id);
        $user = Auth::user();

        $isManager = $this->canManageTugas($pengumpulan->tugas);
        $isOwner = (int) $pengumpulan->user_id === (int) $user->id;
        $isKelompokMember = $pengumpulan->kelompok
            && $pengumpulan->kelompok->anggota->contains('id', $user->id);

        abort_unless($isManager || $isOwner || $isKelompokMember, 403, 'Akses ditolak. Anda tidak berhak mengunduh berkas ini.');

        if (Storage::disk(self::DISK)->exists($pengumpulan->file_path)) {
            return Storage::disk(self::DISK)->download($pengumpulan->file_path, $pengumpulan->file_name);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan di server.');
    }
}