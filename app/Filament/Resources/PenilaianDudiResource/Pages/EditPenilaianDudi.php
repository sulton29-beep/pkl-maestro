<?php

namespace App\Filament\Resources\PenilaianDudiResource\Pages;

use App\Filament\Resources\PenilaianDudiResource;
use App\Services\HitungNilaiService;
use Filament\Resources\Pages\EditRecord;

class EditPenilaianDudi extends EditRecord
{
    protected static string $resource = PenilaianDudiResource::class;

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
        return 'Nilai berhasil diperbarui';
    }
}