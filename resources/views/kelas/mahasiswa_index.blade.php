@extends('layouts.app')
@section('title', 'Mata Kuliah Praktikum')
@section('page_title', 'Praktikum / Mata Kuliah Saya')
@section('content')
<div class="space-y-8">
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm flex items-center">
            <i class="ri-checkbox-circle-fill text-lg mr-2"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center">
            <i class="ri-error-warning-fill text-lg mr-2"></i>
            {{ session('error') }}
        </div>
    @endif
    <!-- Bagian 1: Mata Kuliah Yang Sudah Diikuti -->
    <div>
        <div class="mb-4">
            <h2 class="text-xl font-bold text-slate-800">Mata Kuliah Yang Anda Ikuti</h2>
            <p class="text-sm text-slate-500">Pilih mata kuliah untuk membuka materi modul dan pengumpulan tugas.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($kelasSaya as $mk)
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                                {{ $mk->kode_mk }}
                            </span>
                            <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                Terdaftar
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-lg mb-2">{{ $mk->nama_mk }}</h3>
                        
                        <div class="space-y-1 text-xs text-slate-600 mb-4">
                            <p class="flex items-center">
                                <i class="ri-user-star-line text-blue-500 mr-1.5"></i>
                                Dosen: <span class="font-medium ml-1 text-slate-800">{{ $mk->dosen ? $mk->dosen->nama : '-' }}</span>
                            </p>
                            <p class="flex items-center">
                                <i class="ri-calendar-line text-amber-500 mr-1.5"></i>
                                Hari & Waktu: <span class="font-semibold ml-1 text-slate-800">{{ $mk->hari }}, {{ $mk->waktu }}</span>
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('kelas.show', $mk->id) }}" class="w-full inline-flex items-center justify-center py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                        Masuk Ruang Kelas <i class="ri-arrow-right-line ml-1.5"></i>
                    </a>
                </div>
            @empty
                <div class="col-span-3 bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-400">
                    <i class="ri-folder-open-line text-4xl mb-2 block text-slate-300"></i>
                    Anda belum terdaftar di mata kuliah praktikum mana pun. Silakan daftar pada bagian di bawah.
                </div>
            @endforelse
        </div>
    </div>
    <!-- Bagian 2: Katalog Pendaftaran Mata Kuliah Baru -->
    <div class="pt-6 border-t border-slate-200">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-slate-800">Daftar Mata Kuliah Tersedia</h2>
            <p class="text-sm text-slate-500">Mendaftar ke mata kuliah praktikum baru yang tersedia di laboratorium.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($kelasTersedia as $mk)
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="font-mono text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                            {{ $mk->kode_mk }}
                        </span>
                        <h3 class="font-bold text-slate-800 text-base mt-2 mb-2">{{ $mk->nama_mk }}</h3>
                        
                        <div class="space-y-1 text-xs text-slate-600 mb-4">
                            <p class="flex items-center">
                                <i class="ri-user-star-line text-slate-400 mr-1.5"></i>
                                Dosen: <span class="font-medium ml-1 text-slate-800">{{ $mk->dosen ? $mk->dosen->nama : '-' }}</span>
                            </p>
                            <p class="flex items-center">
                                <i class="ri-calendar-line text-slate-400 mr-1.5"></i>
                                Jadwal: <span class="font-medium ml-1 text-slate-800">{{ $mk->hari }}, {{ $mk->waktu }}</span>
                            </p>
                        </div>
                    </div>
                    <form action="{{ route('kelas.enroll', $mk->id) }}" method="POST" onsubmit="return confirm('Apakah Anda ingin mendaftar ke mata kuliah ini?')">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center py-2 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-sm font-medium transition">
                            <i class="ri-user-add-line mr-1.5"></i> Gabung Mata Kuliah
                        </button>
                    </form>
                </div>
            @empty
                <div class="col-span-3 bg-white border border-slate-200 rounded-xl p-6 text-center text-slate-400 text-sm">
                    Tidak ada mata kuliah baru yang tersedia untuk didaftarkan.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection