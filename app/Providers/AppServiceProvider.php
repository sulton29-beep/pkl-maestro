<?php

namespace App\Providers;

use App\Models\Absensi;
use App\Models\Dudi;
use App\Models\Guru;
use App\Models\Logbook;
use App\Models\Nilai;
use App\Models\Sertifikat;
use App\Models\Siswa;
use App\Observers\AbsensiObserver;
use App\Observers\DudiObserver;
use App\Observers\GuruObserver;
use App\Observers\LogbookObserver;
use App\Observers\NilaiObserver;
use App\Observers\SertifikatObserver;
use App\Observers\SiswaObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ============================================
        // Register Observers untuk Notifikasi + Audit Trail
        // Fase J3 (Notifikasi) + Fase J6 (Audit Trail)
        // ============================================

        // Notifikasi + Audit Trail (existing + updated)
        Logbook::observe(LogbookObserver::class);
        Absensi::observe(AbsensiObserver::class);
        Nilai::observe(NilaiObserver::class);
        Sertifikat::observe(SertifikatObserver::class);

        // Audit Trail only (J6 — file baru)
        Siswa::observe(SiswaObserver::class);
        Guru::observe(GuruObserver::class);
        Dudi::observe(DudiObserver::class);
    }
}