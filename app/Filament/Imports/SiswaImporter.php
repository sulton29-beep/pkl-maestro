<?php

namespace App\Filament\Imports;

use App\Models\Siswa;
use App\Models\User;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class SiswaImporter extends Importer
{
    protected static ?string $model = Siswa::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nama')
                ->label('Nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('nis')
                ->label('NIS')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('nisn')
                ->label('NISN')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->rules(['nullable', 'in:L,P']),
            ImportColumn::make('kelas')
                ->label('Kelas')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('email')
                ->label('Email')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('no_hp')
                ->label('No HP')
                ->rules(['nullable', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Siswa
    {
        // Match existing siswa by NIS, atau bikin baru
        $siswa = Siswa::firstOrNew([
            'nis' => $this->data['nis'],
        ]);

        // Auto-assign user_id dari email (cari user dengan role siswa)
        if (empty($siswa->user_id) && !empty($this->data['email'])) {
            $user = User::where('email', $this->data['email'])
                ->whereHas('roles', fn ($q) => $q->where('name', 'siswa'))
                ->first();
            if ($user) {
                $siswa->user_id = $user->id;
            }
        }

        return $siswa;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = '✅ Import siswa selesai. '.number_format($import->successful_rows).' baris berhasil.';
        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' baris gagal.';
        }
        return $body;
    }
}