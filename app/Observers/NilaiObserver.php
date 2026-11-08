<?php

namespace App\Observers;

use App\Models\Nilai;
use App\Services\ActivityLogger;
use App\Services\NotifikasiService;

class NilaiObserver
{
    public function created(Nilai $nilai): void
    {
        // Notifikasi (Fase J3)
        $this->handleNotifikasi($nilai);

        // Audit trail (Fase J6)
        ActivityLogger::logCreated(
            model: Nilai::class,
            modelId: $nilai->id,
            label: 'Nilai ' . ($nilai->siswa?->nama ?? 'siswa #' . $nilai->siswa_id),
            dataBaru: [
                'siswa'      => $nilai->siswa?->nama ?? '-',
                'kinerja'    => $nilai->nilai_kinerja,
                'kompetensi' => $nilai->nilai_kompetensi,
                'laporan'    => $nilai->nilai_laporan,
                'presentasi' => $nilai->nilai_presentasi,
                'sidang'     => $nilai->nilai_sidang,
            ]
        );
    }

    public function updated(Nilai $nilai): void
    {
        // Notifikasi (Fase J3)
        $this->handleNotifikasi($nilai);

        // Audit trail (Fase J6) — cuma kalau ada perubahan field nilai
        $fieldNilai = [
            'nilai_kinerja', 'nilai_kompetensi',
            'nilai_laporan', 'nilai_presentasi',
            'nilai_sidang', 'nilai_akhir',
        ];

        $changed = [];
        foreach ($fieldNilai as $f) {
            if ($nilai->wasChanged($f)) {
                $changed[$f] = [
                    'lama' => $nilai->getOriginal($f),
                    'baru' => $nilai->{$f},
                ];
            }
        }

        if (! empty($changed)) {
            ActivityLogger::logUpdated(
                model: Nilai::class,
                modelId: $nilai->id,
                label: 'Nilai ' . ($nilai->siswa?->nama ?? 'siswa #' . $nilai->siswa_id),
                dataLama: collect($changed)->map(fn ($v) => $v['lama'])->toArray(),
                dataBaru: collect($changed)->map(fn ($v) => $v['baru'])->toArray()
            );
        }
    }

    protected function handleNotifikasi(Nilai $nilai): void
    {
        // Kalau nilai_kinerja / nilai_kompetensi baru diisi DUDI
        if ($nilai->wasChanged('nilai_kinerja') || $nilai->wasChanged('nilai_kompetensi')) {
            if ($nilai->nilai_kinerja !== null || $nilai->nilai_kompetensi !== null) {
                NotifikasiService::notifNilaiDudiBaru($nilai);
            }
        }

        // Kalau nilai_laporan / nilai_presentasi baru diisi Guru
        if ($nilai->wasChanged('nilai_laporan') || $nilai->wasChanged('nilai_presentasi')) {
            if ($nilai->nilai_laporan !== null || $nilai->nilai_presentasi !== null) {
                NotifikasiService::notifNilaiGuruBaru($nilai);
            }
        }
    }
}