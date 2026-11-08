<?php

namespace App\Services;

use App\Mail\NotifikasiPkl;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifikasiService
{
    /**
     * Kirim notifikasi ke 1 user (email + in-app).
     */
    public static function kirimKeUser(
        ?User $user,
        string $judul,
        string $pesan,
        array $dataTambahan = [],
        ?string $linkAksi = null,
        ?string $labelAksi = null
    ): bool {
        if (! $user || ! $user->email) {
            return false;
        }

        try {
            // 1. Kirim EMAIL
            Mail::to($user->email)->send(
                new NotifikasiPkl(
                    judul: $judul,
                    pesan: $pesan,
                    namaPenerima: $user->name ?? 'Pengguna',
                    linkAksi: $linkAksi,
                    labelAksi: $labelAksi ?? 'Buka Aplikasi',
                    dataTambahan: $dataTambahan
                )
            );

            // 2. Kirim IN-APP (Filament database notification)
            if (method_exists($user, 'notify')) {
                $user->notify(new \Filament\Notifications\DatabaseNotification(
                    title: $judul,
                    body: $pesan,
                    actions: $linkAksi ? [
                        \Filament\Notifications\Actions\Action::make('lihat')
                            ->label($labelAksi ?? 'Lihat')
                            ->url($linkAksi)
                            ->markAsRead(),
                    ] : [],
                ));
            }

            Log::info('[NotifikasiService] Terkirim ke '.$user->email.' — '.$judul);
            return true;
        } catch (\Throwable $e) {
            Log::error('[NotifikasiService] Gagal kirim ke '.($user->email ?? 'null').': '.$e->getMessage());
            return false;
        }
    }

    /**
     * Kirim ke semua user dengan role tertentu.
     */
    public static function kirimKeRole(
        string $roleName,
        string $judul,
        string $pesan,
        array $dataTambahan = [],
        ?string $linkAksi = null,
        ?string $labelAksi = null
    ): int {
        $users = User::role($roleName)->get();
        $count = 0;
        foreach ($users as $user) {
            if (self::kirimKeUser($user, $judul, $pesan, $dataTambahan, $linkAksi, $labelAksi)) {
                $count++;
            }
        }
        return $count;
    }

    /* ==========================================================
     * Helper spesifik per event
     * ========================================================== */

    public static function notifLogbookBaru($logbook): void
    {
        try {
            $penempatan = $logbook->penempatanPkl ?? null;
            if (! $penempatan) return;

            $guru = $penempatan->guruPembimbing ?? null;
            $guruUser = $guru?->user ?? null;
            $siswa = $penempatan->siswa ?? null;

            self::kirimKeUser(
                $guruUser,
                'Logbook Baru dari Siswa Bimbingan',
                'Siswa bimbingan Anda baru saja mengisi logbook. Mohon diperiksa dan diverifikasi.',
                [
                    'Nama Siswa'  => $siswa?->nama ?? '-',
                    'Tanggal'     => optional($logbook->created_at)->format('d/m/Y H:i') ?? '-',
                    'Kegiatan'    => \Illuminate\Support\Str::limit($logbook->kegiatan ?? '-', 60),
                ],
                url('/admin/logbook-pkls'),
                'Buka Logbook'
            );
        } catch (\Throwable $e) {
            Log::error('[NotifLogbook] '.$e->getMessage());
        }
    }

    public static function notifAbsensiMasuk($absensi): void
    {
        try {
            $penempatan = $absensi->penempatanPkl ?? null;
            if (! $penempatan) return;

            $guru = $penempatan->guruPembimbing ?? null;
            $siswa = $penempatan->siswa ?? null;

            self::kirimKeUser(
                $guru?->user,
                'Absensi Baru Siswa Bimbingan',
                'Siswa bimbingan Anda baru saja melakukan absensi.',
                [
                    'Nama Siswa' => $siswa?->nama ?? '-',
                    'Waktu'      => optional($absensi->created_at)->format('d/m/Y H:i') ?? '-',
                    'Tipe'       => $absensi->tipe ?? 'Masuk',
                ],
                url('/admin/rekap-absensi'),
                'Lihat Rekap Absensi'
            );
        } catch (\Throwable $e) {
            Log::error('[NotifAbsensi] '.$e->getMessage());
        }
    }

    public static function notifNilaiDudiBaru($nilai): void
    {
        try {
            $penempatan = $nilai->penempatanPkl ?? null;
            if (! $penempatan) return;

            $guru = $penempatan->guruPembimbing ?? null;
            $siswa = $penempatan->siswa ?? null;

            self::kirimKeUser(
                $guru?->user,
                'Penilaian DUDI Sudah Diisi',
                'Pembimbing DUDI telah mengisi nilai kinerja & kompetensi siswa bimbingan Anda.',
                [
                    'Nama Siswa'      => $siswa?->nama ?? '-',
                    'Nilai Kinerja'   => $nilai->nilai_kinerja ?? '-',
                    'Nilai Kompetensi'=> $nilai->nilai_kompetensi ?? '-',
                ],
                url('/admin/nilais'),
                'Lihat Nilai'
            );
        } catch (\Throwable $e) {
            Log::error('[NotifNilaiDudi] '.$e->getMessage());
        }
    }

    public static function notifNilaiGuruBaru($nilai): void
    {
        try {
            $penempatan = $nilai->penempatanPkl ?? null;
            if (! $penempatan) return;

            $siswa = $penempatan->siswa ?? null;

            self::kirimKeUser(
                $siswa?->user,
                'Nilai Laporan & Presentasi Sudah Diisi',
                'Guru pembimbing Anda telah mengisi nilai laporan & presentasi. Silakan cek di menu Nilai Saya.',
                [
                    'Nilai Laporan'    => $nilai->nilai_laporan ?? '-',
                    'Nilai Presentasi' => $nilai->nilai_presentasi ?? '-',
                ],
                url('/admin/nilai-saya'),
                'Lihat Nilai Saya'
            );
        } catch (\Throwable $e) {
            Log::error('[NotifNilaiGuru] '.$e->getMessage());
        }
    }

    public static function notifSertifikatDiajukan($sertifikat): void
    {
        try {
            $penempatan = $sertifikat->penempatanPkl ?? null;
            $siswa = $penempatan?->siswa ?? null;

            // Notif ke semua KAPRODI
            self::kirimKeRole(
                'kaprodi',
                'Pengajuan Sertifikat Baru',
                'Ada pengajuan sertifikat PKL yang perlu divalidasi.',
                [
                    'Nama Siswa'       => $siswa?->nama ?? '-',
                    'Nomor Sertifikat' => $sertifikat->nomor_sertifikat ?? '-',
                    'Status'           => $sertifikat->status ?? 'draft',
                ],
                url('/admin/approval-sertifikat'),
                'Buka Approval'
            );
        } catch (\Throwable $e) {
            Log::error('[NotifSertifikatDiajukan] '.$e->getMessage());
        }
    }

    public static function notifSertifikatDivalidasiKaprodi($sertifikat): void
    {
        try {
            $penempatan = $sertifikat->penempatanPkl ?? null;
            $siswa = $penempatan?->siswa ?? null;

            self::kirimKeRole(
                'waka_kurikulum',
                'Sertifikat Perlu Persetujuan Waka',
                'Sertifikat sudah divalidasi Kaprodi, perlu persetujuan Anda.',
                [
                    'Nama Siswa'       => $siswa?->nama ?? '-',
                    'Nomor Sertifikat' => $sertifikat->nomor_sertifikat ?? '-',
                ],
                url('/admin/approval-sertifikat'),
                'Buka Approval'
            );
        } catch (\Throwable $e) {
            Log::error('[NotifSertifikatKaprodi] '.$e->getMessage());
        }
    }

    public static function notifSertifikatDisetujuiWaka($sertifikat): void
    {
        try {
            $penempatan = $sertifikat->penempatanPkl ?? null;
            $siswa = $penempatan?->siswa ?? null;

            self::kirimKeRole(
                'kepala_sekolah',
                'Sertifikat Perlu TTD Kepala Sekolah',
                'Sertifikat sudah disetujui Waka, perlu tanda tangan Anda.',
                [
                    'Nama Siswa'       => $siswa?->nama ?? '-',
                    'Nomor Sertifikat' => $sertifikat->nomor_sertifikat ?? '-',
                ],
                url('/admin/approval-sertifikat'),
                'Buka Approval'
            );
        } catch (\Throwable $e) {
            Log::error('[NotifSertifikatWaka] '.$e->getMessage());
        }
    }

    public static function notifSertifikatTerbit($sertifikat): void
    {
        try {
            $penempatan = $sertifikat->penempatanPkl ?? null;
            $siswa = $penempatan?->siswa ?? null;

            self::kirimKeUser(
                $siswa?->user,
                '🎉 Sertifikat PKL Anda Sudah Terbit!',
                'Selamat! Sertifikat PKL Anda sudah ditandatangani dan siap diunduh. Silakan cek di menu Nilai Saya.',
                [
                    'Nomor Sertifikat' => $sertifikat->nomor_sertifikat ?? '-',
                    'Tanggal Terbit'   => optional($sertifikat->tanggal_terbit)->format('d/m/Y') ?? '-',
                ],
                url('/admin/nilai-saya'),
                'Unduh Sertifikat'
            );
        } catch (\Throwable $e) {
            Log::error('[NotifSertifikatTerbit] '.$e->getMessage());
        }
    }

    public static function notifSertifikatDitolak($sertifikat, string $catatan = ''): void
    {
        try {
            $penempatan = $sertifikat->penempatanPkl ?? null;
            $siswa = $penempatan?->siswa ?? null;

            self::kirimKeUser(
                $siswa?->user,
                'Sertifikat Ditolak — Perlu Revisi',
                'Pengajuan sertifikat Anda ditolak. Mohon periksa catatan revisi dari approver.',
                [
                    'Nomor Sertifikat' => $sertifikat->nomor_sertifikat ?? '-',
                    'Catatan Revisi'   => $catatan ?: ($sertifikat->catatan_revisi ?? '-'),
                ],
                url('/admin/nilai-saya'),
                'Lihat Detail'
            );
        } catch (\Throwable $e) {
            Log::error('[NotifSertifikatDitolak] '.$e->getMessage());
        }
    }
}