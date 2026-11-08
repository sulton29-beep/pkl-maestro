<?php

namespace App\Observers;

use App\Models\Guru;
use App\Services\ActivityLogger;

class GuruObserver
{
    public function created(Guru $guru): void
    {
        ActivityLogger::logCreated(
            model: Guru::class,
            modelId: $guru->id,
            label: 'Guru ' . $guru->nama,
            dataBaru: [
                'nama'  => $guru->nama,
                'nip'   => $guru->nip,
                'email' => $guru->email,
            ]
        );
    }

    public function updated(Guru $guru): void
    {
        $changes = $guru->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        ActivityLogger::logUpdated(
            model: Guru::class,
            modelId: $guru->id,
            label: 'Guru ' . $guru->nama,
            dataLama: collect($guru->getOriginal())->only(array_keys($changes))->toArray(),
            dataBaru: $changes
        );
    }

    public function deleted(Guru $guru): void
    {
        ActivityLogger::logDeleted(
            model: Guru::class,
            modelId: $guru->id,
            label: 'Guru ' . $guru->nama,
            dataLama: [
                'nama' => $guru->nama,
                'nip'  => $guru->nip,
            ]
        );
    }
}