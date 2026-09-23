<?php

namespace App\Filament\Resources\DudiResource\Pages;

use App\Filament\Resources\DudiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDudi extends EditRecord
{
    protected static string $resource = DudiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
