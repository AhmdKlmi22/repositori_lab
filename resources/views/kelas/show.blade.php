@extends('layouts.app')
@section('title', $mataKuliah->nama_mk)
@section('page_title', 'Ruang Kelas / ' . $mataKuliah->nama_mk)
@section('content')
@php
    $validTabs = ['modul', 'tugas', 'kelompok', 'peserta'];
    $activeTab = in_array(request('tab'), $validTabs) ? request('tab') : 'modul';
    $isStaff = in_array($role, ['Dosen', 'Aslab', 'Laboran'], true);
@endphp
<div class="space-y-8">
    {{-- Banner Header Kelas --}}
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-2">
                <span class="px-2.5 py-0.5 rounded font-mono text-xs font-bold bg-blue-500 text-white">
                    {{ $mataKuliah->kode_mk }}
                </span>
                <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-slate-700 text-slate-200">
                    <i class="ri-calendar-check-line mr-1"></i> {{ $mataKuliah->hari }}, {{ $mataKuliah->waktu }}
                </span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">{{ $mataKuliah->nama_mk }}</h1>
            <p class="text-xs text-slate-300 mt-1">
                Dosen: <strong class="text-white">{{ $mataKuliah->dosen ? $mataKuliah->dosen->nama : '-' }}</strong> |
                Aslab: <strong class="text-white">{{ $mataKuliah->asisten->pluck('nama')->join(', ') ?: '-' }}</strong>
            </p>
        </div>
        <div class="flex items-center space-x-3">
            @if ($isStaff)
                <a href="{{ route('kelas.rekap-pengumpulan', $mataKuliah->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    <i class="ri-file-chart-line mr-1.5 text-base"></i> Rekap Nilai & Tugas
                </a>
            @endif
            <span class="inline-flex items-center px-3 py-2 rounded-xl bg-white/10 text-white text-xs font-medium">
                <i class="ri-user-smile-line mr-1.5 text-base"></i> {{ $mataKuliah->peserta->count() }} Peserta
            </span>
        </div>
    </div>

    {{-- Notifikasi --}}
    <div class="space-y-2">
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm flex items-center shadow-sm">
                <i class="ri-checkbox-circle-fill text-lg mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center shadow-sm">
                <i class="ri-error-warning-fill text-lg mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm shadow-sm">
                <div class="font-bold mb-2 flex items-center">
                    <i class="ri-error-warning-fill text-lg mr-2"></i> Pengumpulan Ditolak:
                </div>
                <ul class="list-disc list-inside space-y-1 ml-6">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Navigasi Tab --}}
    <div class="border-b border-slate-200 flex flex-wrap gap-4 md:gap-8 text-sm font-medium">
        <button type="button" onclick="switchTab('tab-modul')" id="btn-modul"
                class="pb-3 border-b-2 {{ $activeTab === 'modul' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }} flex items-center">
            <i class="ri-article-line mr-2"></i> Modul Praktikum ({{ $mataKuliah->modul->count() }})
        </button>
        <button type="button" onclick="switchTab('tab-tugas')" id="btn-tugas"
                class="pb-3 border-b-2 {{ $activeTab === 'tugas' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }} flex items-center">
            <i class="ri-task-line mr-2"></i> Tugas Praktikum ({{ $mataKuliah->tugas->count() }})
        </button>
        <button type="button" onclick="switchTab('tab-kelompok')" id="btn-kelompok"
                class="pb-3 border-b-2 {{ $activeTab === 'kelompok' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }} flex items-center">
            <i class="ri-team-line mr-2"></i> Kelompok Praktikum ({{ $mataKuliah->kelompok->count() }})
        </button>
        <button type="button" onclick="switchTab('tab-peserta')" id="btn-peserta"
                class="pb-3 border-b-2 {{ $activeTab === 'peserta' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }} flex items-center">
            <i class="ri-group-line mr-2"></i> Mahasiswa Terdaftar ({{ $mataKuliah->peserta->count() }})
        </button>
    </div>

    {{-- TAB 1: MODUL PRAKTIKUM --}}
    <div id="tab-modul" class="{{ $activeTab === 'modul' ? 'space-y-6' : 'hidden space-y-6' }}">
        @if ($isStaff)
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center">
                    <i class="ri-upload-cloud-line text-blue-600 mr-2"></i> Upload Modul untuk Mata Kuliah Ini
                </h3>
                <form action="{{ route('modul.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="mk_id" value="{{ $mataKuliah->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Judul Modul</label>
                            <input type="text" name="judul_modul" required placeholder="Contoh: Modul 1 - Setup Lingkungan"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Berkas Modul (PDF/DOCX/ZIP, Maks 10MB)</label>
                            <input type="file" name="file" required class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm">
                            Unggah Modul
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Judul Modul</th>
                        <th class="px-6 py-3.5">Nama Berkas</th>
                        <th class="px-6 py-3.5">Waktu Rilis</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($mataKuliah->modul as $m)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $m->judul_modul }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $m->file_name }}</td>
                            <td class="px-6 py-4 text-xs text-slate-400">{{ $m->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-center space-x-2 whitespace-nowrap">
                                <a href="{{ asset('storage/' . $m->file_path) }}" target="_blank" download="{{ $m->file_name }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-medium">
                                    <i class="ri-download-2-line mr-1"></i> Unduh
                                </a>
                                @if ($isStaff)
                                    <form action="{{ route('modul.destroy', $m->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus modul ini?')">
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
                            <td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada modul yang diunggah untuk mata kuliah ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TAB 2: TUGAS PRAKTIKUM --}}
    <div id="tab-tugas" class="{{ $activeTab === 'tugas' ? 'space-y-6' : 'hidden space-y-6' }}">
        @if ($isStaff)
            <div class="flex items-center justify-between">
                <a href="{{ route('kelas.rekap-pengumpulan', $mataKuliah->id) }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200 transition">
                    <i class="ri-file-chart-line mr-1.5 text-base"></i> Buka Rekap Pengumpulan Mahasiswa
                </a>
                <a href="{{ route('tugas.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    <i class="ri-add-line mr-1 text-sm"></i> Buat Tugas Baru
                </a>
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Judul Tugas</th>
                        <th class="px-6 py-3.5">Tipe</th>
                        <th class="px-6 py-3.5">Batas Pengumpulan</th>
                        <th class="px-6 py-3.5">Status Batas</th>
                        @if ($role === 'Mahasiswa')
                            <th class="px-6 py-3.5">Status Anda</th>
                        @endif
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($mataKuliah->tugas as $t)
                        @php
                            $isExpired = \Carbon\Carbon::parse($t->deadline)->isPast();
                            $isClosed = ($t->status === 'Tutup' || $isExpired);

                            $mySubmission = null;
                            $myKelompok = null;

                            if ($role === 'Mahasiswa') {
                                if ($t->tipe === 'Kelompok') {
                                    $myKelompok = $mataKuliah->kelompok->first(
                                        fn ($k) => $k->anggota->contains('id', Auth::id())
                                    );
                                    $mySubmission = $myKelompok
                                        ? $t->arsipPengumpulan->firstWhere('kelompok_id', $myKelompok->id)
                                        : null;
                                } else {
                                    $mySubmission = $t->arsipPengumpulan->firstWhere('user_id', Auth::id());
                                }
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $t->judul_tugas }}</td>
                            <td class="px-6 py-4">
                                @if ($t->tipe === 'Kelompok')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800">
                                        <i class="ri-team-line mr-1"></i> Kelompok
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                        <i class="ri-user-line mr-1"></i> Individu
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($isClosed)
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Ditutup</span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Buka</span>
                                @endif
                            </td>

                            @if ($role === 'Mahasiswa')
                                <td class="px-6 py-4">
                                    @if ($mySubmission)
                                        <span class="inline-flex items-center text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200">
                                            <i class="ri-checkbox-circle-line mr-1"></i>
                                            {{ $t->tipe === 'Kelompok' ? 'Terkumpul (' . $myKelompok->nama_kelompok . ')' : 'Terkumpul' }}
                                        </span>
                                    @elseif ($t->tipe === 'Kelompok' && !$myKelompok)
                                        <span class="inline-flex items-center text-xs font-semibold text-red-700 bg-red-50 px-2.5 py-1 rounded-md border border-red-200" title="Hubungi Aslab untuk masuk kelompok">
                                            <i class="ri-error-warning-line mr-1"></i> Belum Masuk Kelompok
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200">
                                            <i class="ri-time-line mr-1"></i> Belum Mengumpul
                                        </span>
                                    @endif
                                </td>
                            @endif

                            <td class="px-6 py-4 text-center whitespace-nowrap space-x-2">
                                @if ($role === 'Mahasiswa')
                                    @if (!$isClosed)
                                        @if ($t->tipe === 'Kelompok' && !$myKelompok)
                                            <button type="button" disabled class="px-3 py-1.5 bg-slate-200 text-slate-400 rounded-lg text-xs font-medium cursor-not-allowed">
                                                Perlu Kelompok
                                            </button>
                                        @else
                                            <button type="button"
                                                onclick="openUploadModal(@js($t->id), @js($t->judul_tugas), @js($t->tipe), @js($myKelompok?->nama_kelompok ?? ''), @js($mySubmission ? 'Kumpul Ulang' : 'Kumpul Tugas'))"
                                                class="inline-flex items-center px-3 py-1.5 {{ $mySubmission ? 'bg-amber-600 hover:bg-amber-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white rounded-lg text-xs font-medium transition">
                                                <i class="ri-upload-2-line mr-1"></i> {{ $mySubmission ? 'Kumpul Ulang' : 'Kumpul Tugas' }}
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-xs text-slate-400 italic">Pengumpulan Ditutup</span>
                                    @endif

                                    @if ($mySubmission)
                                        <a href="{{ route('pengumpulan.download', $mySubmission->id) }}"
                                           class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-medium" title="Unduh Berkas Saya">
                                            <i class="ri-download-line"></i>
                                        </a>
                                    @endif
                                @endif

                                @if ($isStaff)
                                    <a href="{{ route('pengumpulan.index', $t->id) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold">
                                        <i class="ri-folder-user-line mr-1.5 text-sm"></i> Berkas Masuk ({{ $t->arsipPengumpulan->count() }})
                                    </a>
                                    <span class="text-slate-300">|</span>
                                    <a href="{{ route('tugas.edit', $t->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-xs font-medium">
                                        <i class="ri-edit-line mr-1"></i> Edit
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $role === 'Mahasiswa' ? 6 : 5 }}" class="px-6 py-8 text-center text-slate-400">Belum ada tugas pada mata kuliah ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TAB 3: KELOMPOK PRAKTIKUM --}}
    <div id="tab-kelompok" class="{{ $activeTab === 'kelompok' ? 'space-y-6' : 'hidden space-y-6' }}">
        @php
            $kelompokSaya = null;
            if ($role === 'Mahasiswa') {
                $kelompokSaya = $mataKuliah->kelompok->first(
                    fn ($k) => $k->anggota->contains('id', Auth::id())
                );
            }
            $mahasiswaSudahBerkelompok = $mataKuliah->kelompok->flatMap(fn ($k) => $k->anggota->pluck('id'))->toArray();
            $mahasiswaBelumBerkelompok = $mataKuliah->peserta->whereNotIn('id', $mahasiswaSudahBerkelompok);
        @endphp

        @if ($role === 'Mahasiswa')
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-800 mb-3 flex items-center">
                    <i class="ri-shield-user-line text-blue-600 mr-2 text-lg"></i> Kelompok Praktikum Anda
                </h3>
                @if ($kelompokSaya)
                    <div class="p-4 bg-purple-50 border border-purple-200 rounded-xl">
                        <div class="flex items-center justify-between mb-3 border-b border-purple-200/60 pb-2">
                            <span class="text-base font-bold text-purple-900">{{ $kelompokSaya->nama_kelompok }}</span>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-purple-200 text-purple-800">
                                {{ $kelompokSaya->anggota->count() }} Anggota Tim
                            </span>
                        </div>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                            @foreach ($kelompokSaya->anggota as $anggota)
                                <li class="flex items-center p-2.5 rounded-lg bg-white border border-purple-100 shadow-sm">
                                    <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 font-bold flex items-center justify-center mr-2.5 text-xs">
                                        {{ strtoupper(substr($anggota->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 text-xs leading-tight">
                                            {{ $anggota->nama }}
                                            @if ($anggota->id === Auth::id())
                                                <span class="text-blue-600 font-bold">(Anda)</span>
                                            @endif
                                        </p>
                                        <p class="text-[11px] font-mono text-slate-500">{{ $anggota->nomor_induk }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm flex items-center">
                        <i class="ri-information-line text-xl mr-3 flex-shrink-0"></i>
                        <span>Anda belum dimasukkan ke dalam kelompok praktikum mana pun. Silakan hubungi Asisten Lab untuk pembagian kelompok.</span>
                    </div>
                @endif
            </div>
        @endif

        @if ($isStaff)
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Manajemen Kelompok Praktikum</h3>
                    <p class="text-xs text-slate-500">Kelola kelompok, lakukan penataan anggota, atau impor struktur kelompok via spreadsheet.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('kelompok.template') }}" class="inline-flex items-center px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                        <i class="ri-download-2-line mr-1.5"></i> Template CSV
                    </a>
                    <button type="button" onclick="document.getElementById('modalImportKelompok').classList.remove('hidden')" class="inline-flex items-center px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        <i class="ri-file-excel-2-line mr-1.5"></i> Impor CSV
                    </button>
                    <button type="button" onclick="document.getElementById('modalTambahKelompok').classList.remove('hidden')" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        <i class="ri-add-line mr-1.5"></i> Tambah Kelompok
                    </button>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($mataKuliah->kelompok as $kelompok)
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                            <h4 class="font-bold text-slate-800 text-base flex items-center">
                                <i class="ri-team-line text-purple-600 mr-2"></i> {{ $kelompok->nama_kelompok }}
                            </h4>
                            @if ($isStaff)
                                <form action="{{ route('kelompok.destroy', $kelompok->id) }}" method="POST" onsubmit="return confirm('Hapus {{ $kelompok->nama_kelompok }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Hapus Kelompok">
                                        <i class="ri-delete-bin-line text-base"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="space-y-2 mb-4">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                                Anggota ({{ $kelompok->anggota->count() }}):
                            </span>
                            @forelse ($kelompok->anggota as $mhs)
                                <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100 text-xs">
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $mhs->nama }}</p>
                                        <p class="font-mono text-slate-500 text-[11px]">{{ $mhs->nomor_induk }}</p>
                                    </div>
                                    @if ($isStaff)
                                        <form action="{{ route('kelompok.remove-member', [$kelompok->id, $mhs->id]) }}" method="POST" onsubmit="return confirm('Keluarkan {{ $mhs->nama }} dari kelompok ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600 p-1 transition" title="Keluarkan Anggota">
                                                <i class="ri-close-circle-line text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">Belum ada anggota di kelompok ini.</p>
                            @endforelse
                        </div>
                    </div>

                    @if ($isStaff)
                        <div class="pt-3 border-t border-slate-100">
                            <form action="{{ route('kelompok.add-member', $kelompok->id) }}" method="POST" class="flex gap-2">
                                @csrf
                                <select name="mahasiswa_id" required class="flex-1 px-2.5 py-1.5 text-xs border border-slate-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500">
                                    <option value="" disabled selected>-- Tambah Anggota --</option>
                                    @foreach ($mahasiswaBelumBerkelompok as $mb)
                                        <option value="{{ $mb->id }}">{{ $mb->nama }} ({{ $mb->nomor_induk }})</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                                    +
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-3 bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-400 text-sm">
                    <i class="ri-team-line text-4xl mb-2 block text-slate-300"></i>
                    Belum ada kelompok praktikum yang dibentuk pada mata kuliah ini.
                </div>
            @endforelse
        </div>
    </div>

    {{-- TAB 4: MAHASISWA TERDAFTAR --}}
    <div id="tab-peserta" class="{{ $activeTab === 'peserta' ? 'space-y-6' : 'hidden space-y-6' }}">
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-700 uppercase tracking-wider">Daftar Mahasiswa Terdaftar</span>
                <span class="text-xs font-bold text-blue-600 bg-blue-100 px-2.5 py-0.5 rounded-full">{{ $mataKuliah->peserta->count() }} Orang</span>
            </div>
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">No</th>
                        <th class="px-6 py-3.5">NIM</th>
                        <th class="px-6 py-3.5">Nama Mahasiswa</th>
                        <th class="px-6 py-3.5">Kelompok</th>
                        <th class="px-6 py-3.5">Tanggal Bergabung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($mataKuliah->peserta as $index => $mhs)
                        @php
                            $kelompokMhs = $mataKuliah->kelompok->first(
                                fn ($k) => $k->anggota->contains('id', $mhs->id)
                            );
                        @endphp
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-mono font-medium text-slate-800">{{ $mhs->nomor_induk }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $mhs->nama }}</td>
                            <td class="px-6 py-4">
                                @if ($kelompokMhs)
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-purple-50 text-purple-700 border border-purple-200">
                                        {{ $kelompokMhs->nama_kelompok }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ada kelompok</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">
                                {{ $mhs->pivot?->created_at ? $mhs->pivot->created_at->format('d M Y') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada mahasiswa yang mendaftar ke mata kuliah ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL UPLOAD TUGAS MAHASISWA --}}
@if ($role === 'Mahasiswa')
<div id="uploadModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">Unggah Berkas Tugas</h3>
            <button type="button" onclick="closeUploadModal()" class="text-slate-400 hover:text-slate-600">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        <div class="mb-4 p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs space-y-1">
            <p class="text-slate-700 font-semibold" id="modalTaskName"></p>
            <p class="text-slate-500" id="modalTaskInfo"></p>
        </div>
        <form id="uploadForm" method="POST" enctype="multipart/form-data" action="#" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Berkas Tugas (Maks 5MB)</label>
                <input type="file" name="file_tugas" required accept=".pdf,.doc,.docx,.xls,.xlsx,.pkt,.pka,.zip,.rar"
                       class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-lg">
            </div>
            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeUploadModal()" class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" id="modalSubmitBtn" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                    Kirim Berkas
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUploadModal(tugasId, tugasTitle, tipeTugas, namaKelompok, btnText) {
        const form = document.getElementById('uploadForm');
        form.action = `{{ url('/tugas-praktikum') }}/${tugasId}/kumpul`;

        document.getElementById('modalTaskName').textContent = 'Tugas: ' + tugasTitle;
        document.getElementById('modalTaskInfo').textContent = tipeTugas === 'Kelompok'
            ? 'Penugasan Kelompok: pengumpulan mewakili ' + namaKelompok
            : 'Penugasan Mandiri (Individu)';
        document.getElementById('modalSubmitBtn').textContent = btnText;
        document.getElementById('uploadModal').classList.remove('hidden');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
    }
</script>
@endif

{{-- MODAL TAMBAH KELOMPOK (Dosen, Aslab, Laboran) --}}
@if ($isStaff)
<div id="modalTambahKelompok" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">Buat Kelompok Baru</h3>
            <button type="button" onclick="document.getElementById('modalTambahKelompok').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        <form action="{{ route('kelompok.store', $mataKuliah->id) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kelompok</label>
                <input type="text" name="nama_kelompok" required placeholder="Contoh: Kelompok 1"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" onclick="document.getElementById('modalTambahKelompok').classList.add('hidden')" class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Simpan Kelompok
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL IMPOR KELOMPOK CSV --}}
<div id="modalImportKelompok" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800 flex items-center">
                <i class="ri-file-excel-2-line text-emerald-600 mr-2 text-xl"></i> Impor Kelompok (CSV)
            </h3>
            <button type="button" onclick="document.getElementById('modalImportKelompok').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        <form action="{{ route('kelompok.import', $mataKuliah->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-xs text-slate-600 space-y-1">
                <p class="font-bold text-slate-800">Petunjuk Format Kolom:</p>
                <p>Kolom 1: <strong>nama_kelompok</strong> (Contoh: Kelompok 1)</p>
                <p>Kolom 2: <strong>nomor_induk</strong> (Contoh: 202255202001)</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Berkas CSV</label>
                <input type="file" name="file_csv" accept=".csv,text/csv" required
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-slate-200 rounded-lg">
            </div>
            <div class="pt-4 flex items-center justify-end space-x-3">
                <button type="button" onclick="document.getElementById('modalImportKelompok').classList.add('hidden')" class="px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Mulai Impor
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function switchTab(tabId) {
        const tabs = ['tab-modul', 'tab-tugas', 'tab-kelompok', 'tab-peserta'];
        const btns = ['btn-modul', 'btn-tugas', 'btn-kelompok', 'btn-peserta'];

        tabs.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        });

        btns.forEach(btn => {
            const el = document.getElementById(btn);
            if (el) {
                el.classList.remove('border-blue-600', 'text-blue-600');
                el.classList.add('border-transparent', 'text-slate-500');
            }
        });

        const activeTab = document.getElementById(tabId);
        if (activeTab) activeTab.classList.remove('hidden');

        const activeBtn = document.getElementById('btn-' + tabId.replace('tab-', ''));
        if (activeBtn) {
            activeBtn.classList.remove('border-transparent', 'text-slate-500');
            activeBtn.classList.add('border-blue-600', 'text-blue-600');
        }

        // Sinkronkan tab aktif ke URL tanpa memuat ulang halaman
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set('tab', tabId.replace('tab-', ''));
        window.history.replaceState(null, '', newUrl.toString());
    }
</script>
@endsection