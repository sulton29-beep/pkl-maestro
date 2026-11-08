<?php

namespace App\Filament\Resources\PenilaianDudiResource\Pages;

use App\Filament\Resources\PenilaianDudiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenilaianDudis extends ListRecords
{
    protected static string $resource = PenilaianDudiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Isi Nilai Siswa'),
        ];
    }
}