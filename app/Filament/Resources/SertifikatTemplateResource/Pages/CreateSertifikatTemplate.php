<?php

namespace App\Filament\Resources\SertifikatTemplateResource\Pages;

use App\Filament\Resources\SertifikatTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSertifikatTemplate extends CreateRecord
{
    protected static string $resource = SertifikatTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Template berhasil dibuat';
    }
}