<?php

namespace App\Filament\Resources\SertifikatTemplateResource\Pages;

use App\Filament\Resources\SertifikatTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSertifikatTemplate extends EditRecord
{
    protected static string $resource = SertifikatTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Template berhasil diupdate';
    }
}