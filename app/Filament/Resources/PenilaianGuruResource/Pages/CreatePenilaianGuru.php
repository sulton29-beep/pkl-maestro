<?php

namespace App\Filament\Resources\PenilaianGuruResource\Pages;

use App\Filament\Resources\PenilaianGuruResource;
use App\Services\HitungNilaiService;
use Filament\Resources\Pages\CreateRecord;

class CreatePenilaianGuru extends CreateRecord
{
    protected static string $resource = PenilaianGuruResource::class;

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
        return 'Nilai laporan berhasil disimpan';
    }
}