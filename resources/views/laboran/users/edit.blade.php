@extends('layouts.app')
@section('title', 'Edit Akun')
@section('page_title', 'Master Data / Edit Akun Pengguna')
@section('content')
<div class="max-w-2xl bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-800">Perbarui Data Akun</h2>
        <p class="text-sm text-slate-500">Edit informasi akun pengguna: <strong class="text-slate-700">{{ $user->nama }}</strong></p>
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
    <form action="{{ route('laboran.users.update', $user->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
            <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nomor Induk (NIM / NIDN / NIP)</label>
            <input type="text" name="nomor_induk" value="{{ old('nomor_induk', $user->nomor_induk) }}" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Peran / Role</label>
            <select name="role_id" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                        {{ $role->nama_role }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Ganti Password</label>
            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password"
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            <p class="text-xs text-slate-400 mt-1">Hanya diisi jika ingin mereset/mengganti password akun ini.</p>
        </div>
        <div class="pt-4 flex items-center justify-end space-x-3">
            <a href="{{ route('laboran.users.index') }}" 
                class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" 
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                Perbarui Akun
            </button>
        </div>
    </form>
</div>
@endsection