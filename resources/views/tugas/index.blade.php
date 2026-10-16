@extends('layouts.app')
@section('title', 'Tugas Praktikum')
@section('page_title', 'Praktikum / Daftar Tugas')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Tugas Praktikum</h2>
            <p class="text-sm text-slate-500">Daftar penugasan praktikum, status batas waktu, dan pengumpulan berkas.</p>
        </div>
        @if (in_array($role, ['Dosen', 'Aslab', 'Laboran']))
            <a href="{{ route('tugas.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <i class="ri-add-line mr-2"></i> Buat Tugas Baru
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm flex items-center shadow-sm">
            <i class="ri-checkbox-circle-fill text-lg mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-start shadow-sm">
            <i class="ri-error-warning-fill text-xl mr-2 mt-0.5"></i>
            <div>
                <strong class="block mb-1">Pengumpulan Gagal:</strong>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm shadow-sm">
            <div class="font-bold mb-2 flex items-center">
                <i class="ri-error-warning-fill text-xl mr-2"></i> Berkas Ditolak Sistem!
            </div>
            <ul class="list-disc list-inside space-y-1 ml-6">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3.5">Mata Kuliah</th>
                    <th class="px-6 py-3.5">Judul Tugas</th>
                    <th class="px-6 py-3.5">Batas Pengumpulan</th>
                    <th class="px-6 py-3.5">Status</th>
                    @if ($role === 'Mahasiswa')
                        <th class="px-6 py-3.5">Status Pengumpulan Anda</th>
                    @endif
                    <th class="px-6 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @if (count($tugas) > 0)
                    @foreach ($tugas as $t)
                        @php
                            $isExpired = \Carbon\Carbon::parse($t->deadline)->isPast();
                            $isClosed = ($t->status === 'Tutup' || $isExpired);
                            
                            $mySubmission = null;
                            if ($role === 'Mahasiswa') {
                                if ($t->tipe === 'Kelompok') {
                                    $myKelompok = $t->mataKuliah->kelompok->first(function($k) {
                                        return $k->anggota->contains('id', Auth::id());
                                    });
                                    $mySubmission = $myKelompok 
                                        ? $t->arsipPengumpulan->firstWhere('kelompok_id', $myKelompok->id) 
                                        : null;
                                } else {
                                    $mySubmission = $t->arsipPengumpulan->firstWhere('user_id', Auth::id());
                                }
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-800 block">{{ $t->mataKuliah->nama_mk }}</span>
                                <span class="text-xs font-mono text-slate-400">{{ $t->mataKuliah->kode_mk }}</span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ $t->judul_tugas }}
                                @if ($t->tipe === 'Kelompok')
                                    <span class="ml-2 inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700">Kelompok</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-slate-600">
                                {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($isClosed)
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Ditutup</span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Buka</span>
                                @endif
                            </td>
                            @if ($role === 'Mahasiswa')
                                <td class="px-6 py-4">
                                    @if ($mySubmission)
                                        <span class="inline-flex items-center text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200">
                                            <i class="ri-checkbox-circle-line mr-1"></i> Sudah Dikumpul
                                        </span>
                                        <span class="block text-[11px] text-slate-400 font-mono mt-0.5">
                                            {{ \Carbon\Carbon::parse($mySubmission->waktu_kumpul)->format('d/m/y H:i') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200">
                                            <i class="ri-time-line mr-1"></i> Belum Mengumpul
                                        </span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-6 py-4 text-center whitespace-nowrap space-x-2">
                                @if ($role === 'Mahasiswa')
                                    <a href="{{ route('kelas.show', $t->mataKuliah->id) }}?tab=tugas" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition" title="Buka Kelas untuk Kumpul">
                                        <i class="ri-external-link-line mr-1"></i> Buka Ruang Kelas
                                    </a>
                                @endif

                                @if (in_array($role, ['Dosen', 'Aslab', 'Laboran']))
                                    <a href="{{ route('pengumpulan.index', $t->id) }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-800 text-xs font-medium">
                                        <i class="ri-folder-user-line mr-1"></i> Terkumpul ({{ $t->arsipPengumpulan->count() }})
                                    </a>
                                    <span class="text-slate-300">|</span>
                                    <a href="{{ route('tugas.edit', $t->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-xs font-medium">
                                        <i class="ri-edit-line mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('tugas.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah yakin ingin menghapus tugas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-800 text-xs font-medium">
                                            <i class="ri-delete-bin-line mr-1"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ $role === 'Mahasiswa' ? 6 : 5 }}" class="px-6 py-8 text-center text-slate-400">Belum ada tugas praktikum.</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $tugas->links() }}
        </div>
    </div>
</div>
@endsection