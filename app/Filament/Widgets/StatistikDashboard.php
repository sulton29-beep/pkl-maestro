<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use App\Models\Dudi;
use App\Models\Guru;
use App\Models\Logbook;
use App\Models\Nilai;
use App\Models\PenempatanPkl;
use App\Models\Sertifikat;
use App\Models\Siswa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class StatistikDashboard extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        try {
            $user = auth()->user();
            if (! $user) {
                return [];
            }

            if ($user->hasRole(['admin', 'kepala_sekolah', 'waka_kurikulum'])) {
                return $this->statsGlobal();
            }
            if ($user->hasRole('kaprodi')) {
                return $this->statsKaprodi();
            }
            if ($user->hasRole('guru_pembimbing')) {
                return $this->statsGuruPembimbing($user);
            }
            if ($user->hasRole('guru_penguji')) {
                return $this->statsGuruPenguji($user);
            }
            if ($user->hasRole('siswa')) {
                return $this->statsSiswa($user);
            }
            if ($user->hasRole('dudi')) {
                return $this->statsDudi($user);
            }

            return [];
        } catch (\Throwable $e) {
            Log::error('[StatistikDashboard] '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                Stat::make('Statistik', 'Gagal memuat')
                    ->description('Cek storage/logs/laravel.log')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger'),
            ];
        }
    }

    /* ==========================================================
     * HELPER: cari record Siswa/Guru/Dudi milik user yang login
     * Coba beberapa kemungkinan nama kolom (defensive)
     * ========================================================== */

    protected function currentSiswa($user): ?Siswa
    {
        if (Schema::hasColumn('siswas', 'user_id')) {
            $s = Siswa::where('user_id', $user->id)->first();
            if ($s) return $s;
        }
        if (Schema::hasColumn('siswas', 'email')) {
            $s = Siswa::where('email', $user->email)->first();
            if ($s) return $s;
        }
        if (Schema::hasColumn('siswas', 'user_email')) {
            $s = Siswa::where('user_email', $user->email)->first();
            if ($s) return $s;
        }
        return null;
    }

    protected function currentGuru($user): ?Guru
    {
        if (Schema::hasColumn('gurus', 'user_id')) {
            $g = Guru::where('user_id', $user->id)->first();
            if ($g) return $g;
        }
        if (Schema::hasColumn('gurus', 'email')) {
            $g = Guru::where('email', $user->email)->first();
            if ($g) return $g;
        }
        if (Schema::hasColumn('gurus', 'user_email')) {
            $g = Guru::where('user_email', $user->email)->first();
            if ($g) return $g;
        }
        return null;
    }

    protected function currentDudi($user): ?Dudi
    {
        if (Schema::hasColumn('dudis', 'user_id')) {
            $d = Dudi::where('user_id', $user->id)->first();
            if ($d) return $d;
        }
        if (Schema::hasColumn('dudis', 'email')) {
            $d = Dudi::where('email', $user->email)->first();
            if ($d) return $d;
        }
        if (Schema::hasColumn('dudis', 'user_email')) {
            $d = Dudi::where('user_email', $user->email)->first();
            if ($d) return $d;
        }
        return null;
    }

    /**
     * Cari kolom FK di tabel penempatan_pkls.
     */
    protected function findPenempatanByDudiId($dudiId): array
    {
        if (! Schema::hasTable('penempatan_pkls')) {
            return [];
        }
        if (Schema::hasColumn('penempatan_pkls', 'dudi_id')) {
            return PenempatanPkl::where('dudi_id', $dudiId)->pluck('id')->toArray();
        }
        if (Schema::hasColumn('penempatan_pkls', 'dudis_id')) {
            return PenempatanPkl::where('dudis_id', $dudiId)->pluck('id')->toArray();
        }
        return [];
    }

    protected function countByPenempatan(string $table, array $penempatanIds, ?string $whereNotNull = null): int
    {
        if (empty($penempatanIds) || ! Schema::hasTable($table)) {
            return 0;
        }

        $fk = null;
        foreach (['penempatan_pkl_id', 'penempatan_id', 'penempatan_pkls_id'] as $col) {
            if (Schema::hasColumn($table, $col)) {
                $fk = $col;
                break;
            }
        }
        if (! $fk) {
            return 0;
        }

        $model = match ($table) {
            'logbooks' => Logbook::class,
            'absensi'  => Absensi::class,
            'nilais'   => Nilai::class,
            'sertifikats' => Sertifikat::class,
            default    => null,
        };
        if (! $model) {
            return 0;
        }

        $q = $model::whereIn($fk, $penempatanIds);
        if ($whereNotNull && Schema::hasColumn($table, $whereNotNull)) {
            $q->whereNotNull($whereNotNull);
        }
        return $q->count();
    }

    /* ==========================================================
     * STATISTIK PER ROLE
     * ========================================================== */

    protected function statsGlobal(): array
    {
        return [
            Stat::make('Total Siswa', Siswa::count())
                ->description('Siswa terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Total Guru', Guru::count())
                ->description('Pembimbing & penguji')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
            Stat::make('Total DUDI', Dudi::count())
                ->description('Perusahaan mitra')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success'),
            Stat::make('Penempatan Aktif', PenempatanPkl::count())
                ->description('Siswa di DUDI')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('warning'),
            Stat::make('Entri Logbook', Logbook::count())
                ->description('Total keseluruhan')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),
            Stat::make('Absensi', Absensi::count())
                ->description('Total kehadiran')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Sertifikat Terbit', Sertifikat::where('status', 'terbit')->count())
                ->description('Sudah di-approve final')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('success'),
            Stat::make('Nilai Terisi', Nilai::whereNotNull('nilai_akhir')->count())
                ->description('Sudah dinilai lengkap')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
        ];
    }

    protected function statsKaprodi(): array
    {
        return [
            Stat::make('Siswa', Siswa::count())
                ->description('Total siswa')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Penempatan', PenempatanPkl::count())
                ->description('Siswa di DUDI')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('warning'),
            Stat::make('Logbook', Logbook::count())
                ->description('Entri logbook')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),
            Stat::make('Nilai Final', Nilai::whereNotNull('nilai_akhir')->count())
                ->description('Sudah dinilai')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),
        ];
    }

    protected function statsGuruPembimbing($user): array
    {
        $guru = $this->currentGuru($user);
        if (! $guru) {
            return [
                Stat::make('Status', 'Akun belum terhubung ke data Guru')
                    ->description('Hubungi admin')
                    ->descriptionIcon('heroicon-m-exclamation-circle')
                    ->color('danger'),
            ];
        }

        $penempatanIds = Schema::hasColumn('penempatan_pkls', 'guru_pembimbing_id')
            ? PenempatanPkl::where('guru_pembimbing_id', $guru->id)->pluck('id')->toArray()
            : [];

        return [
            Stat::make('Siswa Bimbingan', count($penempatanIds))
                ->description('Siswa aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Logbook Bimbingan', $this->countByPenempatan('logbooks', $penempatanIds))
                ->description('Entri logbook')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),
            Stat::make('Absensi Bimbingan', $this->countByPenempatan('absensi', $penempatanIds))
                ->description('Total kehadiran')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Sudah Dinilai', $this->countByPenempatan('nilais', $penempatanIds, 'nilai_akhir'))
                ->description('Siswa selesai dinilai')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
        ];
    }

    protected function statsGuruPenguji($user): array
    {
        $guru = $this->currentGuru($user);
        if (! $guru) {
            return [
                Stat::make('Status', 'Akun belum terhubung ke data Guru')
                    ->description('Hubungi admin')
                    ->descriptionIcon('heroicon-m-exclamation-circle')
                    ->color('danger'),
            ];
        }

        $penempatanIds = Schema::hasColumn('penempatan_pkls', 'guru_penguji_id')
            ? PenempatanPkl::where('guru_penguji_id', $guru->id)->pluck('id')->toArray()
            : [];

        return [
            Stat::make('Siswa Diuji', count($penempatanIds))
                ->description('Siswa yang diuji')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Nilai Sidang Terisi', $this->countByPenempatan('nilais', $penempatanIds, 'nilai_sidang'))
                ->description('Sudah input sidang')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success'),
        ];
    }

    protected function statsSiswa($user): array
    {
        $siswa = $this->currentSiswa($user);
        if (! $siswa) {
            return [
                Stat::make('Status', 'Akun belum terhubung ke data Siswa')
                    ->description('Hubungi admin')
                    ->descriptionIcon('heroicon-m-exclamation-circle')
                    ->color('danger'),
            ];
        }

        $penempatanId = Schema::hasColumn('penempatan_pkls', 'siswa_id')
            ? PenempatanPkl::where('siswa_id', $siswa->id)->value('id')
            : null;

        $logbook = $penempatanId ? Logbook::where('penempatan_pkl_id', $penempatanId)->count() : 0;
        $absensi = $penempatanId && Schema::hasColumn('absensi', 'penempatan_pkl_id')
            ? Absensi::where('penempatan_pkl_id', $penempatanId)->count()
            : 0;
        $nilai = $penempatanId && Schema::hasColumn('nilais', 'penempatan_pkl_id')
            ? Nilai::where('penempatan_pkl_id', $penempatanId)->first()
            : null;
        $sertifikat = $penempatanId && Schema::hasColumn('sertifikats', 'penempatan_pkl_id')
            ? Sertifikat::where('penempatan_pkl_id', $penempatanId)->first()
            : null;

        return [
            Stat::make('Logbook Saya', $logbook)
                ->description('Entri tercatat')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),
            Stat::make('Absensi Saya', $absensi)
                ->description('Total kehadiran')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Nilai Akhir', $nilai?->nilai_akhir ?? '—')
                ->description($nilai?->nilai_akhir ? 'Sudah dinilai' : 'Belum dinilai')
                ->descriptionIcon('heroicon-m-star')
                ->color($nilai?->nilai_akhir ? 'warning' : 'gray'),
            Stat::make('Status Sertifikat', $sertifikat?->status ?? 'Belum ada')
                ->description($sertifikat ? 'Status pengajuan' : 'Belum diajukan')
                ->descriptionIcon('heroicon-m-document-check')
                ->color($sertifikat?->status === 'terbit' ? 'success' : 'gray'),
        ];
    }

    protected function statsDudi($user): array
    {
        $dudi = $this->currentDudi($user);
        if (! $dudi) {
            return [
                Stat::make('Status', 'Akun belum terhubung ke data DUDI')
                    ->description('Hubungi admin')
                    ->descriptionIcon('heroicon-m-exclamation-circle')
                    ->color('danger'),
            ];
        }

        $penempatanIds = $this->findPenempatanByDudiId($dudi->id);

        return [
            Stat::make('Siswa Magang', count($penempatanIds))
                ->description('Siswa aktif di perusahaan')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Logbook Masuk', $this->countByPenempatan('logbooks', $penempatanIds))
                ->description('Entri logbook')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),
            Stat::make('Absensi', $this->countByPenempatan('absensi', $penempatanIds))
                ->description('Total kehadiran')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Sudah Dinilai', $this->countByPenempatan('nilais', $penempatanIds, 'nilai_kinerja'))
                ->description('Siswa sudah diberi nilai')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
        ];
    }
}