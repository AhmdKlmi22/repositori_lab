@extends('layouts.app')
@section('title', 'Mata Kuliah Diampu')
@section('page_title', 'Akademik / Mata Kuliah Diampu')
@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Mata Kuliah Praktikum Diampu</h2>
        <p class="text-sm text-slate-500">Pilih mata kuliah untuk mengelola modul, memeriksa tugas, dan melihat daftar mahasiswa terdaftar.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($mataKuliah as $mk)
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                            {{ $mk->kode_mk }}
                        </span>
                        <span class="text-xs font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-full">
                            <i class="ri-group-line mr-1"></i> {{ $mk->peserta->count() }} Mahasiswa
                        </span>
                    </div>
                    <h3 class="font-bold text-slate-800 text-lg mb-2">{{ $mk->nama_mk }}</h3>
                    
                    <div class="space-y-1.5 text-xs text-slate-600 mb-5">
                        <p class="flex items-center">
                            <i class="ri-calendar-line text-blue-500 mr-1.5"></i>
                            Jadwal: <span class="font-semibold text-slate-800 ml-1">{{ $mk->hari }}, {{ $mk->waktu }}</span>
                        </p>
                        <p class="flex items-center">
                            <i class="ri-team-line text-amber-500 mr-1.5"></i>
                            Aslab: 
                            <span class="font-medium text-slate-800 ml-1">
                                {{ $mk->asisten->pluck('nama')->join(', ') ?: 'Belum ada aslab' }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <a href="{{ route('kelas.show', $mk->id) }}" class="w-full inline-flex items-center justify-center py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                        Masuk Ruang Kelas <i class="ri-arrow-right-line ml-1.5"></i>
                    </a>
                    <a href="{{ route('kelas.rekap-pengumpulan', $mk->id) }}" class="w-full inline-flex items-center justify-center py-2 px-4 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-sm font-semibold transition">
                        <i class="ri-file-chart-line mr-1.5"></i> Rekap Pengumpulan Tugas
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-400">
                Belum ada mata kuliah yang ditugaskan kepada Anda.
            </div>
        @endforelse
    </div>
</div>
@endsection