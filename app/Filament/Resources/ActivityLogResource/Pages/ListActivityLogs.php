<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Resources\Pages\ListRecords;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        // Read-only — gak ada tombol Create
        return [];
    }

    public function getTitle(): string
    {
        return 'Activity Log';
    }

    public function getSubheading(): ?string
    {
        return 'Log aktivitas sistem — siapa melakukan apa, kapan.';
    }
}