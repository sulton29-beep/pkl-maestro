<?php

namespace App\Filament\Resources\PenilaianDudiResource\Pages;

use App\Filament\Resources\PenilaianDudiResource;
use App\Services\HitungNilaiService;
use Filament\Resources\Pages\CreateRecord;

class CreatePenilaianDudi extends CreateRecord
{
    protected static string $resource = PenilaianDudiResource::class;

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
        return 'Nilai berhasil disimpan';
    }
}