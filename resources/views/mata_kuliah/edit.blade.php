@extends('layouts.app')
@section('title', 'Edit Mata Kuliah')
@section('page_title', 'Master Data / Edit Mata Kuliah')
@section('content')
<div class="max-w-2xl bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-800">Perbarui Mata Kuliah</h2>
        <p class="text-sm text-slate-500">Edit data mata kuliah: <strong class="text-slate-700">{{ $mataKuliah->nama_mk }}</strong></p>
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
    <form action="{{ route('laboran.mata-kuliah.update', $mataKuliah->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kode Mata Kuliah</label>
            <input type="text" name="kode_mk" value="{{ old('kode_mk', $mataKuliah->kode_mk) }}" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono uppercase">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Mata Kuliah</label>
            <input type="text" name="nama_mk" value="{{ old('nama_mk', $mataKuliah->nama_mk) }}" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Hari Praktikum</label>
                <select name="hari" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                        <option value="{{ $hari }}" {{ old('hari', $mataKuliah->hari) === $hari ? 'selected' : '' }}>{{ $hari }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Waktu / Jam Praktikum</label>
                <input type="text" name="waktu" value="{{ old('waktu', $mataKuliah->waktu) }}" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Dosen Pengampu</label>
            <select name="dosen_id" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                @foreach($dosens as $dosen)
                    <option value="{{ $dosen->id }}" {{ old('dosen_id', $mataKuliah->dosen_id) == $dosen->id ? 'selected' : '' }}>
                        {{ $dosen->nama }} ({{ $dosen->nomor_induk }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Tugaskan Asisten Praktikum (Aslab)</label>
            <div class="space-y-2 border border-slate-200 rounded-lg p-3 max-h-44 overflow-y-auto bg-slate-50">
                @php 
                    $selectedAslabs = old('aslab_ids', $mataKuliah->asisten->pluck('id')->toArray());
                @endphp
                @forelse($aslabs as $aslab)
                    <label class="flex items-center space-x-2 text-sm text-slate-700 hover:text-slate-900 cursor-pointer">
                        <input type="checkbox" name="aslab_ids[]" value="{{ $aslab->id }}"
                            {{ in_array($aslab->id, $selectedAslabs) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span>{{ $aslab->nama }} <span class="text-xs text-slate-400">({{ $aslab->nomor_induk }})</span></span>
                    </label>
                @empty
                    <p class="text-xs text-slate-400">Belum ada akun pengguna dengan role Aslab.</p>
                @endforelse
            </div>
            <p class="text-xs text-slate-400 mt-1">Dapat memilih lebih dari satu asisten praktikum.</p>
        </div>
        <div class="pt-4 flex items-center justify-end space-x-3">
            <a href="{{ route('laboran.mata-kuliah.index') }}" 
                class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" 
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                Perbarui Mata Kuliah
            </button>
        </div>
    </form>
</div>
@endsection