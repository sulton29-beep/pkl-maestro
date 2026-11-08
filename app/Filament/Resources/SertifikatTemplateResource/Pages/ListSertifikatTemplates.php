<?php

namespace App\Filament\Resources\SertifikatTemplateResource\Pages;

use App\Filament\Resources\SertifikatTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSertifikatTemplates extends ListRecords
{
    protected static string $resource = SertifikatTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Template')
                ->icon('heroicon-o-plus'),
        ];
    }
}