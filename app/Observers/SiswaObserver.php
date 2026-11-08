<?php

namespace App\Observers;

use App\Models\Siswa;
use App\Services\ActivityLogger;

class SiswaObserver
{
    public function created(Siswa $siswa): void
    {
        ActivityLogger::logCreated(
            model: Siswa::class,
            modelId: $siswa->id,
            label: 'Siswa ' . $siswa->nama,
            dataBaru: [
                'nama'  => $siswa->nama,
                'nis'   => $siswa->nis,
                'kelas' => $siswa->kelas,
                'email' => $siswa->email,
            ]
        );
    }

    public function updated(Siswa $siswa): void
    {
        $changes = $siswa->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        ActivityLogger::logUpdated(
            model: Siswa::class,
            modelId: $siswa->id,
            label: 'Siswa ' . $siswa->nama,
            dataLama: collect($siswa->getOriginal())->only(array_keys($changes))->toArray(),
            dataBaru: $changes
        );
    }

    public function deleted(Siswa $siswa): void
    {
        ActivityLogger::logDeleted(
            model: Siswa::class,
            modelId: $siswa->id,
            label: 'Siswa ' . $siswa->nama,
            dataLama: [
                'nama'  => $siswa->nama,
                'nis'   => $siswa->nis,
                'kelas' => $siswa->kelas,
            ]
        );
    }
}