<?php

namespace App\Filament\Exports;

use App\Models\Siswa;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class SiswaExporter extends Exporter
{
    protected static ?string $model = Siswa::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('nama')->label('Nama'),
            ExportColumn::make('nis')->label('NIS'),
            ExportColumn::make('nisn')->label('NISN'),
            ExportColumn::make('jenis_kelamin')->label('Jenis Kelamin'),
            ExportColumn::make('jurusan.nama')->label('Jurusan'),
            ExportColumn::make('kelas')->label('Kelas'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('no_hp')->label('No HP'),
            ExportColumn::make('alamat')->label('Alamat'),
            ExportColumn::make('created_at')->label('Dibuat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return '✅ Export Siswa selesai. '.number_format($export->successful_rows).' baris berhasil.';
    }
}