<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\PengumpulanTugas; 
use Illuminate\Support\Facades\Auth;

class PengumpulanTugasController extends Controller
{
    /**
     * Menyimpan pengumpulan tugas (Menyatukan logic Individu dan Kelompok)
     */
    public function store(Request $request, $tugas_id)
    {
        // VALIDASI BARU: Sesuai arahan dosen penguji (PDF, DOC/DOCX, XLS/XLSX, PKT/PKA)
        $request->validate([
            'file_tugas' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,pkt,pka|max:5120',
        ], [
            'file_tugas.required' => 'File tugas wajib diunggah.',
            'file_tugas.mimes' => 'Format file ditolak! Hanya boleh mengunggah file PDF, Word, Excel, atau Cisco Packet Tracer (.pkt/.pka).',
            'file_tugas.max' => 'Ukuran file maksimal 5 MB.'
        ]);

        $user = Auth::user();
        $tugas = Tugas::findOrFail($tugas_id);

        // Ambil tipe tugas (individu/kelompok)
        $tipe = ucfirst($tugas->tipe); 
        
        // Format string agar aman untuk nama file
        $judulTugas = str_replace(' ', '_', $tugas->judul_tugas);
        $namaUser = str_replace(' ', '_', $user->nama);
        $nomorInduk = $user->nomor_induk;
        
        $extension = $request->file('file_tugas')->getClientOriginalExtension();
        
        // Format Nama File: Tipe_JudulTugas_NIM_Nama.ekstensi
        $fileName = "{$tipe}_{$judulTugas}_{$nomorInduk}_{$namaUser}.{$extension}";

        // Simpan file ke storage
        $path = $request->file('file_tugas')->storeAs('pengumpulan_tugas', $fileName, 'public');

        // Simpan ke database menggunakan updateOrCreate 
        PengumpulanTugas::updateOrCreate(
            [
                'tugas_id' => $tugas->id,
                'user_id' => $user->id, 
            ],
            [
                'file_name' => $fileName, 
                'file_path' => $path,
                'waktu_kumpul' => now(), 
            ]
        );

        return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan!');
    }
}