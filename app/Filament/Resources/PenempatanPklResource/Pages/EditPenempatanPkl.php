<?php

namespace App\Filament\Resources\PenempatanPklResource\Pages;

use App\Filament\Resources\PenempatanPklResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenempatanPkl extends EditRecord
{
    protected static string $resource = PenempatanPklResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
