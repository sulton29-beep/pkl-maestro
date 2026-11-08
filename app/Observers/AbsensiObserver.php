<?php

namespace App\Observers;

use App\Models\Absensi;
use App\Services\ActivityLogger;
use App\Services\NotifikasiService;

class AbsensiObserver
{
    public function created(Absensi $absensi): void
    {
        // 1. Notifikasi ke guru pembimbing (Fase J3)
        NotifikasiService::notifAbsensiMasuk($absensi);

        // 2. Audit trail (Fase J6)
        $tanggalStr = $absensi->tanggal
            ? \Carbon\Carbon::parse($absensi->tanggal)->format('d M Y')
            : '-';

        $jamStr = $absensi->jam_masuk
            ?? $absensi->jam_keluar
            ?? '-';

        ActivityLogger::log(
            aksi: 'created',
            model: Absensi::class,
            modelId: $absensi->id,
            deskripsi: 'Absensi ' . ($absensi->tipe ?? 'masuk') . ' — ' . ($absensi->siswa?->nama ?? '-'),
            dataBaru: [
                'siswa'   => $absensi->siswa?->nama ?? '-',
                'dudi'    => $absensi->dudi?->nama_perusahaan ?? '-',
                'tanggal' => $tanggalStr,
                'tipe'    => $absensi->tipe ?? 'masuk',
                'jam'     => $jamStr,
            ]
        );
    }
}