@extends('layouts.app')
@section('title', 'Profil Akun')
@section('page_title', 'Profil & Ganti Password')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Notifikasi Berhasil --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center shadow-sm">
            <i class="ri-checkbox-circle-fill text-emerald-500 text-xl mr-3"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    {{-- Informasi Akun Mahasiswa --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <h3 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
            <i class="ri-information-line text-blue-500 mr-2 text-lg"></i> Informasi Mahasiswa
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                <span class="text-xs text-slate-500 block font-medium">Nama Lengkap</span>
                <span class="text-slate-800 font-semibold text-base">{{ $user->nama }}</span>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                <span class="text-xs text-slate-500 block font-medium">Nomor Induk Mahasiswa (NIM)</span>
                <span class="text-slate-800 font-semibold text-base">{{ $user->nomor_induk }}</span>
            </div>
        </div>
    </div>
    {{-- Form Ganti Password (3 Box Input) --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <h3 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3 mb-6 flex items-center">
            <i class="ri-lock-password-line text-blue-500 mr-2 text-lg"></i> Kustomisasi Password Baru
        </h3>
        <form action="{{ route('profil.update-password') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            {{-- Box 1: Password Lama --}}
            <div>
                <label for="password_lama" class="block text-sm font-medium text-slate-700 mb-1">
                    Password Lama
                </label>
                <input type="password" 
                       id="password_lama" 
                       name="password_lama" 
                       placeholder="Masukkan password saat ini" 
                       class="w-full px-4 py-2.5 rounded-lg border @error('password_lama') border-red-500 ring-1 ring-red-500 @else border-slate-300 @enderror text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                       required>
                @error('password_lama')
                    <p class="text-xs text-red-500 mt-1 flex items-center">
                        <i class="ri-error-warning-line mr-1"></i> {{ $message }}
                    </p>
                @enderror
            </div>
            {{-- Box 2: Password Baru --}}
            <div>
                <label for="password_baru" class="block text-sm font-medium text-slate-700 mb-1">
                    Password Baru
                </label>
                <input type="password" 
                       id="password_baru" 
                       name="password_baru" 
                       placeholder="Minimal 6 karakter" 
                       class="w-full px-4 py-2.5 rounded-lg border @error('password_baru') border-red-500 ring-1 ring-red-500 @else border-slate-300 @enderror text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                       required>
                @error('password_baru')
                    <p class="text-xs text-red-500 mt-1 flex items-center">
                        <i class="ri-error-warning-line mr-1"></i> {{ $message }}
                    </p>
                @enderror
            </div>
            {{-- Box 3: Konfirmasi Password Baru --}}
            <div>
                <label for="konfirmasi_password_baru" class="block text-sm font-medium text-slate-700 mb-1">
                    Konfirmasi Password Baru
                </label>
                <input type="password" 
                       id="konfirmasi_password_baru" 
                       name="konfirmasi_password_baru" 
                       placeholder="Ulangi password baru Anda" 
                       class="w-full px-4 py-2.5 rounded-lg border @error('konfirmasi_password_baru') border-red-500 ring-1 ring-red-500 @else border-slate-300 @enderror text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                       required>
                @error('konfirmasi_password_baru')
                    <p class="text-xs text-red-500 mt-1 flex items-center">
                        <i class="ri-error-warning-line mr-1"></i> {{ $message }}
                    </p>
                @enderror
            </div>
            <div class="pt-4 flex justify-end">
                <button type="submit" 
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow transition duration-150 flex items-center">
                    <i class="ri-save-line mr-2 text-base"></i> Simpan Password Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection