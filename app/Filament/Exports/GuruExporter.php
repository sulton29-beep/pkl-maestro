<?php

namespace App\Filament\Exports;

use App\Models\Guru;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class GuruExporter extends Exporter
{
    protected static ?string $model = Guru::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('nama')->label('Nama'),
            ExportColumn::make('nip')->label('NIP'),
            ExportColumn::make('jenis_kelamin')->label('Jenis Kelamin'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('no_hp')->label('No HP'),
            ExportColumn::make('created_at')->label('Dibuat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return '✅ Export Guru selesai. '.number_format($export->successful_rows).' baris berhasil.';
    }
}