@extends('layouts.app')
@section('title', 'Kelola Mata Kuliah')
@section('page_title', 'Master Data / Mata Kuliah')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Mata Kuliah Praktikum</h2>
            <p class="text-sm text-slate-500">Kelola jadwal, dosen pengampu, asisten laboratorium, dan mahasiswa terdaftar.</p>
        </div>
        <a href="{{ route('laboran.mata-kuliah.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            <i class="ri-add-line mr-2"></i> Tambah Mata Kuliah
        </a>
    </div>
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm flex items-center">
            <i class="ri-checkbox-circle-fill text-lg mr-2"></i>
            {{ session('success') }}
        </div>
    @endif
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3.5">Kode MK</th>
                    <th class="px-6 py-3.5">Mata Kuliah & Jadwal</th>
                    <th class="px-6 py-3.5">Dosen Pengampu</th>
                    <th class="px-6 py-3.5">Asisten Lab</th>
                    <th class="px-6 py-3.5 text-center">Peserta</th>
                    <th class="px-6 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($mataKuliah as $mk)
                    <tr class="hover:bg-slate-50/75 transition">
                        <td class="px-6 py-4 font-mono font-bold text-slate-800">{{ $mk->kode_mk }}</td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800 block">{{ $mk->nama_mk }}</span>
                            <span class="text-xs text-blue-600 font-semibold flex items-center mt-0.5">
                                <i class="ri-calendar-event-line mr-1"></i> {{ $mk->hari }}, {{ $mk->waktu }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-700">
                            {{ $mk->dosen ? $mk->dosen->nama : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($mk->asisten->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($mk->asisten as $aslab)
                                        <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                            {{ $aslab->nama }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-slate-400 italic">Belum ada</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                <i class="ri-user-follow-line mr-1"></i> {{ $mk->peserta->count() }} Mhs
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center space-x-3 whitespace-nowrap">
                            <a href="{{ route('kelas.rekap-pengumpulan', $mk->id) }}" class="inline-flex items-center text-emerald-600 hover:text-emerald-800 text-sm font-semibold" title="Rekap Pengumpulan">
                                <i class="ri-file-chart-line mr-1"></i> Rekap Tugas
                            </a>
                            <a href="{{ route('kelas.show', $mk->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium" title="Masuk Kelas">
                                <i class="ri-eye-line mr-1"></i> Kelas
                            </a>
                            <a href="{{ route('laboran.mata-kuliah.edit', $mk->id) }}" class="inline-flex items-center text-slate-600 hover:text-slate-800 text-sm font-medium">
                                <i class="ri-edit-line mr-1"></i> Edit
                            </a>
                            <form action="{{ route('laboran.mata-kuliah.destroy', $mk->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-800 text-sm font-medium">
                                    <i class="ri-delete-bin-line mr-1"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada data mata kuliah.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $mataKuliah->links() }}
        </div>
    </div>
</div>
@endsection