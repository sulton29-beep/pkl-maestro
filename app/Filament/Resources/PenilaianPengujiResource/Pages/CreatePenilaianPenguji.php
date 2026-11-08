<?php

namespace App\Filament\Resources\PenilaianPengujiResource\Pages;

use App\Filament\Resources\PenilaianPengujiResource;
use App\Services\HitungNilaiService;
use Filament\Resources\Pages\CreateRecord;

class CreatePenilaianPenguji extends CreateRecord
{
    protected static string $resource = PenilaianPengujiResource::class;

    protected function afterCreate(): void
    {
        HitungNilaiService::hitung($this->record);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Nilai sidang berhasil disimpan';
    }
}