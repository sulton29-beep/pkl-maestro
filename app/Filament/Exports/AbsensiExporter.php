<?php

namespace App\Filament\Exports;

use App\Models\Absensi;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class AbsensiExporter extends Exporter
{
    protected static ?string $model = Absensi::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('siswa.nama')->label('Nama Siswa'),
            ExportColumn::make('dudi.nama_perusahaan')->label('DUDI'),
            ExportColumn::make('tanggal')->label('Tanggal'),
            ExportColumn::make('jam_masuk')->label('Jam Masuk'),
            ExportColumn::make('jam_keluar')->label('Jam Keluar'),
            ExportColumn::make('jarak_masuk')->label('Jarak Masuk (m)'),
            ExportColumn::make('status_masuk')->label('Status Masuk'),
            ExportColumn::make('status_keluar')->label('Status Keluar'),
            ExportColumn::make('created_at')->label('Dibuat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return '✅ Export Absensi selesai. '.number_format($export->successful_rows).' baris berhasil.';
    }
}