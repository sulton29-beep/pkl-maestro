<?php

namespace App\Observers;

use App\Models\Dudi;
use App\Services\ActivityLogger;

class DudiObserver
{
    public function created(Dudi $dudi): void
    {
        ActivityLogger::logCreated(
            model: Dudi::class,
            modelId: $dudi->id,
            label: 'DUDI ' . $dudi->nama_perusahaan,
            dataBaru: [
                'nama'   => $dudi->nama_perusahaan,
                'bidang' => $dudi->bidang_usaha,
                'kota'   => $dudi->kota,
            ]
        );
    }

    public function updated(Dudi $dudi): void
    {
        $changes = $dudi->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        ActivityLogger::logUpdated(
            model: Dudi::class,
            modelId: $dudi->id,
            label: 'DUDI ' . $dudi->nama_perusahaan,
            dataLama: collect($dudi->getOriginal())->only(array_keys($changes))->toArray(),
            dataBaru: $changes
        );
    }

    public function deleted(Dudi $dudi): void
    {
        ActivityLogger::logDeleted(
            model: Dudi::class,
            modelId: $dudi->id,
            label: 'DUDI ' . $dudi->nama_perusahaan,
            dataLama: [
                'nama' => $dudi->nama_perusahaan,
                'kota' => $dudi->kota,
            ]
        );
    }
}