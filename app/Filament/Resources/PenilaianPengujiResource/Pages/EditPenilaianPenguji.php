<?php

namespace App\Filament\Resources\PenilaianPengujiResource\Pages;

use App\Filament\Resources\PenilaianPengujiResource;
use App\Services\HitungNilaiService;
use Filament\Resources\Pages\EditRecord;

class EditPenilaianPenguji extends EditRecord
{
    protected static string $resource = PenilaianPengujiResource::class;

    protected function afterSave(): void
    {
        HitungNilaiService::hitung($this->record);
        $this->record->refresh();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Nilai sidang berhasil diperbarui';
    }
}