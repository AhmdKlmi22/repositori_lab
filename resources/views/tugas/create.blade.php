@extends('layouts.app')
@section('title', 'Buat Tugas Praktikum')
@section('page_title', 'Tugas Praktikum / Buat Baru')
@section('content')
<div class="max-w-2xl bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-800">Form Buat Tugas Praktikum</h2>
        <p class="text-sm text-slate-500">Tentukan mata kuliah, tipe penugasan (individu/kelompok), dan tenggat waktu pengumpulan.</p>
    </div>
    
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tugas.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Mata Kuliah Praktikum</label>
            <select name="mk_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                <option value="" disabled selected>-- Pilih Mata Kuliah --</option>
                @foreach ($mataKuliah as $mk)
                    <option value="{{ $mk->id }}" {{ old('mk_id') == $mk->id ? 'selected' : '' }}>
                        {{ $mk->kode_mk }} - {{ $mk->nama_mk }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Judul Tugas</label>
            <input type="text" name="judul_tugas" value="{{ old('judul_tugas') }}" required placeholder="Contoh: Tugas 1 - Desain Database"
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Penugasan</label>
            <div class="grid grid-cols-2 gap-4">
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-slate-50 transition border-slate-200">
                    <input type="radio" name="tipe" value="Individu" {{ old('tipe', 'Individu') === 'Individu' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                    <div class="ml-3">
                        <span class="block text-sm font-semibold text-slate-800">Tugas Individu</span>
                        <span class="block text-xs text-slate-400">Setiap mahasiswa mengunggah berkas mandiri.</span>
                    </div>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-slate-50 transition border-slate-200">
                    <input type="radio" name="tipe" value="Kelompok" {{ old('tipe') === 'Kelompok' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                    <div class="ml-3">
                        <span class="block text-sm font-semibold text-slate-800">Tugas Kelompok</span>
                        <span class="block text-xs text-slate-400">Diunggah oleh 1 perwakilan kelompok.</span>
                    </div>
                </label>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Batas Waktu (Deadline)</label>
            <input type="datetime-local" name="deadline" value="{{ old('deadline') }}" required
                   class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>
        
        <div class="pt-4 flex items-center justify-end space-x-3">
            <a href="{{ route('tugas.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                Simpan Tugas
            </button>
        </div>
    </form>
</div>
@endsection