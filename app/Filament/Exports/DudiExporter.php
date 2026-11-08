<?php

namespace App\Filament\Exports;

use App\Models\Dudi;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class DudiExporter extends Exporter
{
    protected static ?string $model = Dudi::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('nama_perusahaan')->label('Nama Perusahaan'),
            ExportColumn::make('bidang_usaha')->label('Bidang Usaha'),
            ExportColumn::make('alamat')->label('Alamat'),
            ExportColumn::make('kota')->label('Kota'),
            ExportColumn::make('kontak_person')->label('Kontak Person'),
            ExportColumn::make('no_hp')->label('No HP'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('kuota')->label('Kuota'),
            ExportColumn::make('is_active')->label('Aktif'),
            ExportColumn::make('latitude')->label('Latitude'),
            ExportColumn::make('longitude')->label('Longitude'),
            ExportColumn::make('created_at')->label('Dibuat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return '✅ Export DUDI selesai. '.number_format($export->successful_rows).' baris berhasil.';
    }
}