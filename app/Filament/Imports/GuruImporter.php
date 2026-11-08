<?php

namespace App\Filament\Imports;

use App\Models\Guru;
use App\Models\User;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class GuruImporter extends Importer
{
    protected static ?string $model = Guru::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nama')
                ->label('Nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('nip')
                ->label('NIP')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->rules(['nullable', 'in:L,P']),
            ImportColumn::make('email')
                ->label('Email')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('no_hp')
                ->label('No HP')
                ->rules(['nullable', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Guru
    {
        $guru = Guru::firstOrNew([
            'nip' => $this->data['nip'],
        ]);

        // Auto-assign user_id dari email
        if (empty($guru->user_id) && !empty($this->data['email'])) {
            $user = User::where('email', $this->data['email'])
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['guru_pembimbing', 'guru_penguji']))
                ->first();
            if ($user) {
                $guru->user_id = $user->id;
            }
        }

        return $guru;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = '✅ Import guru selesai. '.number_format($import->successful_rows).' baris berhasil.';
        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' baris gagal.';
        }
        return $body;
    }
}