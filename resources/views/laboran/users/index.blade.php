@extends('layouts.app')
@section('title', 'Kelola Pengguna')
@section('page_title', 'Master Pengguna')
@section('content')
<div class="space-y-6">
    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center shadow-sm">
            <i class="ri-checkbox-circle-fill text-emerald-500 text-xl mr-3"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center shadow-sm">
            <i class="ri-error-warning-fill text-red-500 text-xl mr-3"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif
    {{-- Bilah Aksi Utama --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Daftar Akun Pengguna</h2>
            <p class="text-sm text-slate-500">Kelola pengguna laboratorium, lakukan pencarian, atau impor mahasiswa secara massal.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- Unduh Template CSV --}}
            <a href="{{ route('laboran.users.template') }}" 
               class="inline-flex items-center px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">
                <i class="ri-download-2-line mr-1.5 text-base"></i> Template CSV
            </a>
            {{-- Buka Modal Import --}}
            <button type="button" 
                    onclick="document.getElementById('modalImportCsv').classList.remove('hidden')" 
                    class="inline-flex items-center px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm transition">
                <i class="ri-file-excel-2-line mr-1.5 text-base"></i> Impor Mahasiswa (CSV)
            </button>
            {{-- Tambah Manual --}}
            <a href="{{ route('laboran.users.create') }}" 
               class="inline-flex items-center px-3.5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition">
                <i class="ri-user-add-line mr-1.5 text-base"></i> Tambah Manual
            </a>
        </div>
    </div>
    {{-- Bilah Pencarian & Tombol Aksi Pilihan --}}
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
        {{-- Form Pencarian Berdasarkan Nama atau NIM --}}
        <form action="{{ route('laboran.users.index') }}" method="GET" class="flex items-center gap-2 max-w-md w-full">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i class="ri-search-line text-base"></i>
                </span>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari berdasarkan nama atau NIM..." 
                       class="w-full pl-9 pr-4 py-2 text-sm bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold transition">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('laboran.users.index') }}" 
                   class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-sm font-medium transition" 
                   title="Reset Pencarian">
                    Reset
                </a>
            @endif
        </form>
        {{-- Tombol Hapus Massal --}}
        <div>
            <button type="button" 
                    id="btnBulkDelete" 
                    onclick="confirmBulkDelete()" 
                    disabled 
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed text-white text-sm font-semibold shadow-sm transition">
                <i class="ri-delete-bin-line mr-1.5 text-base"></i> 
                Hapus Terpilih (<span id="countSelected">0</span>)
            </button>
        </div>
    </div>
    {{-- Form Pembungkus untuk Hapus Massal --}}
    <form id="formBulkDelete" action="{{ route('laboran.users.bulk-delete') }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-4 py-3.5 w-12 text-center">
                                <input type="checkbox" 
                                       id="checkAll" 
                                       onclick="toggleSelectAll(this)" 
                                       class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer">
                            </th>
                            <th class="px-6 py-3.5">Nama Lengkap</th>
                            <th class="px-6 py-3.5">Nomor Induk / NIM</th>
                            <th class="px-6 py-3.5">Role</th>
                            <th class="px-6 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-4 text-center">
                                    @if($user->id !== Auth::id())
                                        <input type="checkbox" 
                                               name="ids[]" 
                                               value="{{ $user->id }}" 
                                               onchange="updateBulkDeleteState()" 
                                               class="user-checkbox w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer">
                                    @else
                                        <span class="text-xs text-slate-300" title="Akun Anda">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $user->nama }}</td>
                                <td class="px-6 py-4 text-slate-700 font-mono text-xs">{{ $user->nomor_induk }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $roleName = $user->role->nama_role ?? '-';
                                        $badgeClass = match($roleName) {
                                            'Laboran'   => 'bg-purple-100 text-purple-800',
                                            'Dosen'     => 'bg-amber-100 text-amber-800',
                                            'Aslab'     => 'bg-blue-100 text-blue-800',
                                            'Mahasiswa' => 'bg-emerald-100 text-emerald-800',
                                            default     => 'bg-slate-100 text-slate-800',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                        {{ $roleName }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center space-x-2">
                                        <a href="{{ route('laboran.users.edit', $user) }}" 
                                           class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-md transition" 
                                           title="Edit">
                                            <i class="ri-edit-line text-lg"></i>
                                        </a>
                                        @if($user->id !== Auth::id())
                                            <button type="button" 
                                                    onclick="singleDelete('{{ $user->id }}', '{{ addslashes($user->nama) }}')" 
                                                    class="p-1.5 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-md transition" 
                                                    title="Hapus">
                                                <i class="ri-delete-bin-line text-lg"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                    <i class="ri-user-unfollow-line text-3xl block mb-2"></i>
                                    @if(request('search'))
                                        Tidak ditemukan akun dengan kata kunci "<span class="font-semibold text-slate-700">{{ request('search') }}</span>".
                                    @else
                                        Belum ada data pengguna yang terdaftar.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </form>
</div>
{{-- Form Tersembunyi untuk Aksi Hapus Satuan --}}
<form id="formSingleDelete" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
{{-- Modal Impor CSV --}}
<div id="modalImportCsv" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-100">
        
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900 flex items-center">
                <i class="ri-file-excel-2-line text-emerald-600 mr-2 text-xl"></i> Impor Akun Mahasiswa
            </h3>
            <button type="button" 
                    onclick="document.getElementById('modalImportCsv').classList.add('hidden')" 
                    class="text-slate-400 hover:text-slate-600 text-xl">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <form action="{{ route('laboran.users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-xs text-emerald-900 space-y-1.5 leading-relaxed">
                <p class="font-bold flex items-center text-emerald-800">
                    <i class="ri-information-line mr-1 text-sm"></i> Petunjuk Impor Spreadsheet / CSV:
                </p>
                <ul class="list-disc list-inside space-y-1 text-slate-700">
                    <li>Gunakan urutan kolom: <span class="font-semibold text-slate-900">nama,nomor_induk</span>.</li>
                    <li>Sistem otomatis memberikan peran <span class="font-semibold text-emerald-700">Mahasiswa</span>.</li>
                    <li>Kata sandi awal otomatis disetel sama dengan <span class="font-semibold text-slate-900">Nomor Induk / NIM</span>.</li>
                    <li>Gunakan tombol <span class="font-semibold text-slate-900">Template CSV</span> jika belum memiliki formatnya.</li>
                </ul>
            </div>
            <div>
                <label for="file_csv" class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Berkas CSV</label>
                <input type="file" 
                       id="file_csv" 
                       name="file_csv" 
                       accept=".csv,text/csv" 
                       required
                       class="block w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-slate-300 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" 
                        onclick="document.getElementById('modalImportCsv').classList.add('hidden')" 
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition flex items-center">
                    <i class="ri-upload-cloud-2-line mr-1.5 text-base"></i> Mulai Impor
                </button>
            </div>
        </form>
    </div>
</div>
{{-- Skrip JavaScript untuk Logika Checkbox & Hapus Massal --}}
<script>
    function toggleSelectAll(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateBulkDeleteState();
    }
    function updateBulkDeleteState() {
        const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
        const count = selectedCheckboxes.length;
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const countSpan = document.getElementById('countSelected');
        const checkAll = document.getElementById('checkAll');
        const allCheckboxes = document.querySelectorAll('.user-checkbox');
        countSpan.textContent = count;
        if (count > 0) {
            btnBulkDelete.removeAttribute('disabled');
        } else {
            btnBulkDelete.setAttribute('disabled', 'true');
        }
        if (allCheckboxes.length > 0) {
            checkAll.checked = (count === allCheckboxes.length);
        }
    }
    function confirmBulkDelete() {
        const count = document.querySelectorAll('.user-checkbox:checked').length;
        if (count === 0) return;
        if (confirm(`Apakah Anda yakin ingin menghapus ${count} akun terpilih sekaligus? Tindakan ini tidak dapat dibatalkan.`)) {
            document.getElementById('formBulkDelete').submit();
        }
    }
    function singleDelete(userId, userName) {
        if (confirm(`Apakah Anda yakin ingin menghapus akun ${userName}?`)) {
            const form = document.getElementById('formSingleDelete');
            form.action = `/users/${userId}`;
            form.submit();
        }
    }
</script>
@endsection