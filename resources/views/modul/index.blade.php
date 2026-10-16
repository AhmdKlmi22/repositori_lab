@extends('layouts.app')
@section('title', 'Modul Praktikum')
@section('page_title', 'Repositori / Modul Praktikum')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Modul Praktikum</h2>
            <p class="text-sm text-slate-500">Akses dokumen panduan dan materi praktikum laboratorium.</p>
        </div>
    </div>
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm flex items-center">
            <i class="ri-checkbox-circle-fill text-lg mr-2"></i>
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- Form Upload Modul (Khusus Dosen, Aslab, dan Laboran) -->
    @if(in_array($role, ['Dosen', 'Aslab', 'Laboran']))
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-md font-bold text-slate-800 mb-4 flex items-center">
                <i class="ri-upload-cloud-line text-blue-600 mr-2 text-lg"></i> Upload Modul Baru
            </h3>
            
            <form action="{{ route('modul.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Mata Kuliah</label>
                        <select name="mk_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                            <option value="" disabled selected>-- Pilih Mata Kuliah --</option>
                            @foreach($mataKuliah as $mk)
                                <option value="{{ $mk->id }}">{{ $mk->kode_mk }} - {{ $mk->nama_mk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Judul Modul</label>
                        <input type="text" name="judul_modul" required placeholder="Contoh: Pertemuan 1 - Pengenalan Sintaks"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">File Modul (PDF/DOCX/ZIP, Maks 10MB)</label>
                    <input type="file" name="file" required
                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition flex items-center">
                        <i class="ri-upload-2-line mr-2"></i> Simpan Modul
                    </button>
                </div>
            </form>
        </div>
    @endif
    <!-- Tabel Daftar Modul -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3.5">Mata Kuliah</th>
                    <th class="px-6 py-3.5">Judul Modul</th>
                    <th class="px-6 py-3.5">Nama Berkas</th>
                    <th class="px-6 py-3.5">Tanggal Unggah</th>
                    <th class="px-6 py-3.5 text-center">Berkas & Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($modul as $m)
                    <tr class="hover:bg-slate-50/75 transition">
                        <td class="px-6 py-4">
                            <span class="font-semibold text-slate-800 block">{{ $m->mataKuliah->nama_mk }}</span>
                            <span class="text-xs font-mono text-slate-400">{{ $m->mataKuliah->kode_mk }}</span>
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $m->judul_modul }}</td>
                        <td class="px-6 py-4 text-xs font-mono text-slate-500 truncate max-w-xs">{{ $m->file_name }}</td>
                        <td class="px-6 py-4 text-xs text-slate-400 whitespace-nowrap">{{ $m->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-center whitespace-nowrap space-x-3">
                            <a href="{{ asset('storage/' . $m->file_path) }}" target="_blank" download="{{ $m->file_name }}"
                                class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-medium transition">
                                <i class="ri-download-2-line mr-1"></i> Unduh
                            </a>
                            @if(in_array($role, ['Dosen', 'Aslab', 'Laboran']))
                                <form action="{{ route('modul.destroy', $m->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus berkas modul ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-800 text-xs font-medium">
                                        <i class="ri-delete-bin-line mr-1"></i> Hapus
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada modul praktikum yang diunggah.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $modul->links() }}
        </div>
    </div>
</div>
@endsection