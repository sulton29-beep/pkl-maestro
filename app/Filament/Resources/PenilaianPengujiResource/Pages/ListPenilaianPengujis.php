<?php

namespace App\Filament\Resources\PenilaianPengujiResource\Pages;

use App\Filament\Resources\PenilaianPengujiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenilaianPengujis extends ListRecords
{
    protected static string $resource = PenilaianPengujiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Isi Nilai Sidang'),
        ];
    }
}