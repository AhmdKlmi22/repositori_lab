<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    DashboardController,
    UserController,
    MataKuliahController,
    KelasController,
    ModulPraktikumController,
    TugasController,
    KelompokController,
    ProfilController
};

// ==========================================
// AUTENTIKASI
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==========================================
// RUTE YANG MEMBUTUHKAN LOGIN
// ==========================================
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Ruang kelas: otorisasi per mata kuliah ada di KelasController
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/{id}', [KelasController::class, 'show'])->name('kelas.show');

    // Daftar tugas: mahasiswa juga perlu akses. Data difilter di TugasController::index()
    Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');

    // Unduh berkas: otorisasi per berkas ada di TugasController::download()
    Route::get('/pengumpulan/{pengumpulan_id}/unduh', [TugasController::class, 'download'])
        ->name('pengumpulan.download');

    // ==========================================
    // KHUSUS LABORAN (Master Data)
    // ==========================================
    Route::middleware('role:Laboran')->prefix('laboran')->name('laboran.')->group(function () {
        // Route khusus harus didaftarkan sebelum resource agar tidak tertangkap {user}
        Route::get('/users/template', [UserController::class, 'template'])->name('users.template');
        Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
        Route::delete('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
        Route::resource('users', UserController::class)->except(['show']);

        Route::resource('mata-kuliah', MataKuliahController::class);
    });

    // ==========================================
    // KHUSUS DOSEN, ASLAB, & LABORAN (Akademik & Pengajar)
    // ==========================================
    Route::middleware('role:Dosen,Aslab,Laboran')->group(function () {
        Route::resource('modul', ModulPraktikumController::class)->except(['create', 'edit', 'update', 'show']);

        // index sudah ada di grup auth, show tidak dipakai
        Route::resource('tugas', TugasController::class)->except(['index', 'show']);

        Route::get('/tugas/{id}/pengumpulan', [TugasController::class, 'pengumpulan'])->name('pengumpulan.index');
        Route::get('/kelas/{id}/rekap', [KelasController::class, 'rekapPengumpulan'])->name('kelas.rekap-pengumpulan');

        Route::get('/kelompok/template', [KelompokController::class, 'template'])->name('kelompok.template');
        Route::post('/kelas/{id}/kelompok/import', [KelompokController::class, 'import'])->name('kelompok.import');
        Route::post('/kelas/{id}/kelompok', [KelompokController::class, 'store'])->name('kelompok.store');
        Route::delete('/kelompok/{id}', [KelompokController::class, 'destroy'])->name('kelompok.destroy');
        Route::post('/kelompok/{id}/add-member', [KelompokController::class, 'addMember'])->name('kelompok.add-member');
        Route::delete('/kelompok/{id}/remove-member/{mhs_id}', [KelompokController::class, 'removeMember'])->name('kelompok.remove-member');
    });

    // ==========================================
    // KHUSUS MAHASISWA
    // ==========================================
    Route::middleware('role:Mahasiswa')->group(function () {
        Route::post('/kelas/{id}/enroll', [KelasController::class, 'enroll'])->name('kelas.enroll');
        Route::post('/tugas-praktikum/{id}/kumpul', [TugasController::class, 'kumpul'])->name('tugas.kumpul');

        Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
        Route::put('/profil/update-password', [ProfilController::class, 'updatePassword'])->name('profil.update-password');
    });
});