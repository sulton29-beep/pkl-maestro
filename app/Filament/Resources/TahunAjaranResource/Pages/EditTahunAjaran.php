<?php

namespace App\Filament\Resources\TahunAjaranResource\Pages;

use App\Filament\Resources\TahunAjaranResource;
use App\Models\TahunAjaran;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTahunAjaran extends EditRecord
{
    protected static string $resource = TahunAjaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Kalau user set is_active = true, non-aktifkan tahun lain.
     */
    protected function afterSave(): void
    {
        if ($this->record->is_active) {
            TahunAjaran::where('id', '!=', $this->record->id)
                ->update(['is_active' => false]);
        }
    }
}