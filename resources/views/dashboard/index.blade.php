@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Ringkasan Sistem')

@section('content')
<div class="space-y-6">
    <!-- Banner Sambutan -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-800">Selamat Datang, {{ Auth::user()->nama }}!</h2>
        <p class="text-slate-500 text-sm mt-1">
            Anda masuk ke sistem repositori praktikum sebagai <span class="font-semibold text-slate-700">{{ Auth::user()->role->nama_role }}</span> (Nomor Induk: {{ Auth::user()->nomor_induk }}).
        </p>
    </div>

    <!-- Ringkasan Status -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center">
            <div class="p-4 rounded-lg bg-blue-50 text-blue-600 mr-4">
                <i class="ri-shield-user-line text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase">Role Akun</p>
                <h3 class="text-lg font-bold text-slate-800">{{ Auth::user()->role->nama_role }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center">
            <div class="p-4 rounded-lg bg-emerald-50 text-emerald-600 mr-4">
                <i class="ri-checkbox-circle-line text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase">Status Akun</p>
                <h3 class="text-lg font-bold text-slate-800">Aktif</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center">
            <div class="p-4 rounded-lg bg-purple-50 text-purple-600 mr-4">
                <i class="ri-calendar-line text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase">Tanggal Hari Ini</p>
                <h3 class="text-lg font-bold text-slate-800">{{ date('d M Y') }}</h3>
            </div>
        </div>
    </div>
</div>
@endsection