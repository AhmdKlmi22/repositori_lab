@extends('layouts.app')
@section('title', 'Berkas Masuk')
@section('page_title', 'Tugas / Berkas Masuk')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('tugas.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 mb-2">
                <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar Tugas
            </a>
            <h2 class="text-xl font-bold text-slate-800">Berkas Masuk: {{ $tugas->judul_tugas }}</h2>
            <p class="text-sm text-slate-500">
                Mata Kuliah: <span class="font-semibold text-slate-700">{{ $tugas->mataKuliah->nama_mk }}</span> |
                Batas: <span class="font-mono text-slate-700">{{ \Carbon\Carbon::parse($tugas->deadline)->format('d M Y H:i') }}</span> |
                Tipe: <span class="font-semibold text-slate-700">{{ $tugas->tipe }}</span>
            </p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <tr>
                    @if ($tugas->tipe === 'Kelompok')
                        <th class="px-6 py-3.5">Nama Kelompok</th>
                        <th class="px-6 py-3.5">Perwakilan (Pengunggah)</th>
                    @else
                        <th class="px-6 py-3.5">NIM</th>
                        <th class="px-6 py-3.5">Nama Mahasiswa</th>
                    @endif
                    <th class="px-6 py-3.5">Waktu Kumpul</th>
                    <th class="px-6 py-3.5">Nama Berkas</th>
                    <th class="px-6 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($pengumpulan as $p)
                    <tr class="hover:bg-slate-50/75 transition">
                        @if ($tugas->tipe === 'Kelompok')
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $p->kelompok->nama_kelompok ?? 'Kelompok ' . $p->kelompok_id }}
                            </td>
                            <td class="px-6 py-4 text-slate-800">
                                {{ $p->mahasiswa->nama ?? '-' }}
                                <span class="text-xs text-slate-500 block">({{ $p->mahasiswa->nomor_induk ?? '-' }})</span>
                            </td>
                        @else
                            <td class="px-6 py-4 font-mono font-medium text-slate-800">{{ $p->mahasiswa->nomor_induk ?? '-' }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $p->mahasiswa->nama ?? '-' }}</td>
                        @endif

                        <td class="px-6 py-4 font-mono text-xs text-slate-600">
                            {{ \Carbon\Carbon::parse($p->waktu_kumpul)->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4 text-xs font-mono text-slate-500 truncate max-w-xs">{{ $p->file_name }}</td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('pengumpulan.download', $p->id) }}"
                                class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-medium transition">
                                <i class="ri-download-2-line mr-1"></i> Unduh Berkas
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada mahasiswa/kelompok yang mengumpulkan tugas ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $pengumpulan->links() }}
        </div>
    </div>
</div>
@endsection