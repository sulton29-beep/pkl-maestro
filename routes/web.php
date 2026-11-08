<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Filament\Pages\AbsensiPkl;
use App\Http\Controllers\ExportExcelController;
use App\Http\Controllers\ImportTemplateController;
use App\Http\Controllers\RekapNilaiExportController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\VerifikasiController;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('landing');
});

Route::get('/login', function () {
    return redirect('/admin/login');
});

/*
|--------------------------------------------------------------------------
| Absensi PKL (Selfie GPS)
|--------------------------------------------------------------------------
*/

Route::post('/absensi/masuk', [AbsensiPkl::class, 'absenMasuk'])->middleware('auth');
Route::post('/absensi/keluar', [AbsensiPkl::class, 'absenKeluar'])->middleware('auth');

Route::get('/storage/absensi/{filename}', function ($filename) {
    $path = storage_path('app/public/absensi/' . $filename);
    abort_unless(file_exists($path), 404);
    return response()->file($path);
})->where('filename', '.*');

/*
|--------------------------------------------------------------------------
| Verifikasi Sertifikat Publik (tanpa login)
|--------------------------------------------------------------------------
*/

Route::get('/verifikasi/{token}', [VerifikasiController::class, 'show'])
    ->name('verifikasi.sertifikat');

/*
|--------------------------------------------------------------------------
| Route dengan auth (Export + Sertifikat + Template Import)
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth'])->group(function () {

    // Export Rekap Nilai PKL (Excel .xls)
    Route::get('/admin/rekap-nilai/export', [RekapNilaiExportController::class, 'export'])
        ->name('rekap-nilai.export');

    // Download Sertifikat PKL (PDF, on-demand)
    Route::get('/admin/sertifikat/{sertifikat}/download', [SertifikatController::class, 'download'])
        ->name('sertifikat.download');

    // ==========================================
    // EXPORT EXCEL BERWARNA (HTML→XLS)
    // ==========================================
    Route::prefix('admin/export-excel')->name('export-excel.')->group(function () {
        Route::get('/dudi',    [ExportExcelController::class, 'dudi'])->name('dudi');
        Route::get('/guru',    [ExportExcelController::class, 'guru'])->name('guru');
        Route::get('/siswa',   [ExportExcelController::class, 'siswa'])->name('siswa');
        Route::get('/logbook', [ExportExcelController::class, 'logbook'])->name('logbook');
        Route::get('/absensi', [ExportExcelController::class, 'absensi'])->name('absensi');
    });

    // ==========================================
    // TEMPLATE CSV IMPORT
    // ==========================================
    Route::get('/admin/import-template/{type}', [ImportTemplateController::class, 'download'])
        ->where('type', 'siswa|guru|dudi')
        ->name('import-template.download');
});