<?php

namespace App\Filament\Exports;

use App\Models\Logbook;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class LogbookExporter extends Exporter
{
    protected static ?string $model = Logbook::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('siswa.nama')->label('Nama Siswa'),
            ExportColumn::make('tanggal')->label('Tanggal'),
            ExportColumn::make('kegiatan')->label('Kegiatan'),
            ExportColumn::make('catatan_siswa')->label('Catatan Siswa'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('catatan_dudi')->label('Catatan DUDI'),
            ExportColumn::make('verifier.name')->label('Diverifikasi Oleh'),
            ExportColumn::make('verified_at')->label('Waktu Verifikasi'),
            ExportColumn::make('created_at')->label('Dibuat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return '✅ Export Logbook selesai. '.number_format($export->successful_rows).' baris berhasil.';
    }
}