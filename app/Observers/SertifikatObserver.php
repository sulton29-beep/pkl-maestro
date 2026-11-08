<?php

namespace App\Observers;

use App\Models\Sertifikat;
use App\Services\ActivityLogger;
use App\Services\NotifikasiService;

class SertifikatObserver
{
    public function created(Sertifikat $sertifikat): void
    {
        // Notifikasi (Fase J3)
        if ($sertifikat->status === 'draft') {
            NotifikasiService::notifSertifikatDiajukan($sertifikat);
        }

        // Audit trail (Fase J6)
        ActivityLogger::logCreated(
            model: Sertifikat::class,
            modelId: $sertifikat->id,
            label: 'Sertifikat ' . ($sertifikat->siswa?->nama ?? '-'),
            dataBaru: [
                'siswa'  => $sertifikat->siswa?->nama ?? '-',
                'nomor'  => $sertifikat->nomor_sertifikat ?? '-',
                'status' => $sertifikat->status ?? 'draft',
            ]
        );
    }

    public function updated(Sertifikat $sertifikat): void
    {
        if (! $sertifikat->wasChanged('status')) {
            return;
        }

        $statusBaru = $sertifikat->status;
        $statusLama = $sertifikat->getOriginal('status');

        // Notifikasi (Fase J3)
        match ($statusBaru) {
            'divalidasi_kaprodi' => NotifikasiService::notifSertifikatDivalidasiKaprodi($sertifikat),
            'disetujui_waka'     => NotifikasiService::notifSertifikatDisetujuiWaka($sertifikat),
            'terbit'             => NotifikasiService::notifSertifikatTerbit($sertifikat),
            'ditolak'            => NotifikasiService::notifSertifikatDitolak($sertifikat, $sertifikat->catatan_revisi ?? ''),
            'draft'              => NotifikasiService::notifSertifikatDiajukan($sertifikat),
            default              => null,
        };

        // Audit trail (Fase J6)
        ActivityLogger::log(
            aksi: 'updated',
            model: Sertifikat::class,
            modelId: $sertifikat->id,
            deskripsi: 'Sertifikat ' . ($sertifikat->siswa?->nama ?? '-') . ' → status: ' . $statusBaru,
            dataLama: ['status' => $statusLama],
            dataBaru: ['status' => $statusBaru]
        );
    }
}