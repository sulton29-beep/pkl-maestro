<?php

namespace App\Observers;

use App\Models\Logbook;
use App\Services\ActivityLogger;
use App\Services\NotifikasiService;
use Illuminate\Support\Str;

class LogbookObserver
{
    public function created(Logbook $logbook): void
    {
        // 1. Notifikasi ke guru pembimbing (Fase J3)
        NotifikasiService::notifLogbookBaru($logbook);

        // 2. Audit trail (Fase J6)
        $tanggalStr = $logbook->tanggal
            ? \Carbon\Carbon::parse($logbook->tanggal)->format('d M Y')
            : '-';

        ActivityLogger::logCreated(
            model: Logbook::class,
            modelId: $logbook->id,
            label: 'Logbook tanggal ' . $tanggalStr,
            dataBaru: [
                'siswa'    => $logbook->siswa?->nama ?? '-',
                'tanggal'  => $tanggalStr,
                'kegiatan' => Str::limit($logbook->kegiatan ?? '-', 80),
                'status'   => $logbook->status ?? 'pending',
            ]
        );
    }

    public function updated(Logbook $logbook): void
    {
        // Log perubahan status logbook
        if ($logbook->wasChanged('status')) {
            ActivityLogger::log(
                aksi: 'updated',
                model: Logbook::class,
                modelId: $logbook->id,
                deskripsi: 'Update status logbook → ' . ($logbook->status ?? '-'),
                dataLama: ['status' => $logbook->getOriginal('status')],
                dataBaru: ['status' => $logbook->status]
            );
        }
    }

    public function deleted(Logbook $logbook): void
    {
        ActivityLogger::logDeleted(
            model: Logbook::class,
            modelId: $logbook->id,
            label: 'Logbook ' . ($logbook->siswa?->nama ?? '-'),
            dataLama: [
                'siswa'    => $logbook->siswa?->nama ?? '-',
                'tanggal'  => $logbook->tanggal
                    ? \Carbon\Carbon::parse($logbook->tanggal)->format('Y-m-d')
                    : '-',
                'kegiatan' => Str::limit($logbook->kegiatan ?? '-', 80),
            ]
        );
    }
}