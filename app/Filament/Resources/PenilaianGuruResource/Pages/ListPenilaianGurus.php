<?php

namespace App\Filament\Resources\PenilaianGuruResource\Pages;

use App\Filament\Resources\PenilaianGuruResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenilaianGurus extends ListRecords
{
    protected static string $resource = PenilaianGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Isi Nilai Siswa'),
        ];
    }
}