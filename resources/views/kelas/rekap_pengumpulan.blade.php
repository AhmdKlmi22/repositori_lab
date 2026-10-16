@extends('layouts.app')
@section('title', 'Rekap Pengumpulan - ' . $mataKuliah->nama_mk)
@section('page_title', 'Akademik / Rekap Pengumpulan Tugas')
@section('content')
<div class="space-y-6">
    <!-- Header Informasi Mata Kuliah -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('kelas.show', $mataKuliah->id) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2">
                <i class="ri-arrow-left-line mr-1"></i> Kembali ke Ruang Kelas
            </a>
            <h2 class="text-xl font-bold text-slate-800">Rekap Pengumpulan Tugas: {{ $mataKuliah->nama_mk }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Dosen: <strong class="text-slate-700">{{ $mataKuliah->dosen ? $mataKuliah->dosen->nama : '-' }}</strong> |
                Jadwal: <span class="font-medium text-slate-700">{{ $mataKuliah->hari }}, {{ $mataKuliah->waktu }}</span> |
                Total Peserta: <span class="font-bold text-blue-600">{{ $mataKuliah->peserta->count() }} Mahasiswa</span>
            </p>
        </div>
    </div>

    <!-- Filter Pilihan Tugas -->
    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
        <form method="GET" action="{{ route('kelas.rekap-pengumpulan', $mataKuliah->id) }}" class="flex flex-col md:flex-row md:items-center gap-3">
            <label class="text-xs font-bold text-slate-700 whitespace-nowrap">Pilih Tugas Praktikum:</label>
            <select name="tugas_id" onchange="this.form.submit()" class="w-full md:w-auto flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500">
                @forelse ($mataKuliah->tugas as $t)
                    <option value="{{ $t->id }}" {{ (optional($selectedTugas)->id == $t->id) ? 'selected' : '' }}>
                        {{ $t->judul_tugas }} (Batas: {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y H:i') }})
                    </option>
                @empty
                    <option value="">Belum ada tugas pada mata kuliah ini</option>
                @endforelse
            </select>
            <noscript>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs rounded-lg">Pilih</button>
            </noscript>
        </form>
    </div>

    <!-- Ringkasan Angka Pengumpulan -->
    @if ($selectedTugas)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">
                        Total {{ $selectedTugas->tipe === 'Kelompok' ? 'Kelompok' : 'Mahasiswa' }}
                    </p>
                    <h3 class="text-lg font-bold text-slate-800">{{ $totalTarget }} Target</h3>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg"><i class="ri-team-line text-xl"></i></div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Sudah Mengumpulkan</p>
                    <h3 class="text-lg font-bold text-emerald-600">{{ $sudahMengumpulkan }} Berkas</h3>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg"><i class="ri-checkbox-circle-line text-xl"></i></div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Belum Mengumpulkan</p>
                    <h3 class="text-lg font-bold text-red-500">{{ $belumMengumpulkan }}</h3>
                </div>
                <div class="p-3 bg-red-50 text-red-500 rounded-lg"><i class="ri-time-line text-xl"></i></div>
            </div>
        </div>

        <!-- Tabel Daftar Mahasiswa dan Status File -->
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">NIM</th>
                        <th class="px-6 py-3.5">Nama Mahasiswa</th>
                        <th class="px-6 py-3.5">Status Pengumpulan</th>
                        <th class="px-6 py-3.5">Waktu Kumpul</th>
                        <th class="px-6 py-3.5">Nama File Otomatis</th>
                        <th class="px-6 py-3.5 text-center">Berkas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($mataKuliah->peserta as $mhs)
                        @php
                            $submission = $pengumpulan->get($mhs->id);
                        @endphp
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-6 py-4 font-mono font-medium text-slate-800">{{ $mhs->nomor_induk }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $mhs->nama }}</td>
                            <td class="px-6 py-4">
                                @if ($submission)
                                    @if ($selectedTugas->tipe === 'Kelompok' && $submission->user_id !== $mhs->id)
                                        <span class="inline-flex items-center text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200" title="Diunggah oleh: {{ $submission->mahasiswa->nama ?? 'Perwakilan' }}">
                                            <i class="ri-checkbox-circle-line mr-1"></i> Terkumpul via {{ strtok($submission->mahasiswa->nama ?? 'Perwakilan', ' ') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200">
                                            <i class="ri-checkbox-circle-line mr-1"></i> Terkumpul
                                            @if ($selectedTugas->tipe === 'Kelompok') (Perwakilan) @endif
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center text-xs font-semibold text-red-600 bg-red-50 px-2.5 py-1 rounded-md border border-red-200">
                                        <i class="ri-close-circle-line mr-1"></i> Belum Mengumpul
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                {{ $submission ? \Carbon\Carbon::parse($submission->waktu_kumpul)->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500 truncate max-w-xs">
                                {{ $submission ? $submission->file_name : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if ($submission)
                                    <a href="{{ route('pengumpulan.download', $submission->id) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-semibold transition">
                                        <i class="ri-download-2-line mr-1"></i> Unduh File
                                    </a>
                                @else
                                    <span class="text-xs text-slate-300 italic">Tidak ada berkas</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada mahasiswa yang terdaftar di mata kuliah ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-400">
            Belum ada tugas praktikum yang diterbitkan untuk mata kuliah ini.
        </div>
    @endif
</div>
@endsection