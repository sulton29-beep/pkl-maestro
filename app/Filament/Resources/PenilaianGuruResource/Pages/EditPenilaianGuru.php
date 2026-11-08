<?php

namespace App\Filament\Resources\PenilaianGuruResource\Pages;

use App\Filament\Resources\PenilaianGuruResource;
use App\Services\HitungNilaiService;
use Filament\Resources\Pages\EditRecord;

class EditPenilaianGuru extends EditRecord
{
    protected static string $resource = PenilaianGuruResource::class;

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
        return 'Nilai laporan berhasil diperbarui';
    }
}