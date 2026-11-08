<?php

namespace App\Filament\Imports;

use App\Models\Dudi;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class DudiImporter extends Importer
{
    protected static ?string $model = Dudi::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nama_perusahaan')
                ->label('Nama Perusahaan')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('bidang_usaha')
                ->label('Bidang Usaha')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('alamat')
                ->label('Alamat')
                ->rules(['nullable']),
            ImportColumn::make('kota')
                ->label('Kota')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('kontak_person')
                ->label('Kontak Person')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('no_hp')
                ->label('No HP')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('email')
                ->label('Email')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('kuota')
                ->label('Kuota')
                ->rules(['nullable', 'integer', 'min:0']),
        ];
    }

    public function resolveRecord(): ?Dudi
    {
        // Match by nama_perusahaan
        return Dudi::firstOrNew([
            'nama_perusahaan' => $this->data['nama_perusahaan'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = '✅ Import DUDI selesai. '.number_format($import->successful_rows).' baris berhasil.';
        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' baris gagal.';
        }
        return $body;
    }
}