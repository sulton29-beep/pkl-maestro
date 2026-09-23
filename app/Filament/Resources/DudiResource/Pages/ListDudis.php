<?php

namespace App\Filament\Resources\DudiResource\Pages;

use App\Filament\Resources\DudiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDudis extends ListRecords
{
    protected static string $resource = DudiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
