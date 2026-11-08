<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewActivityLog extends ViewRecord
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->hasRole('admin')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                Infolists\Components\Section::make('Info Aktivitas')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Waktu')
                            ->dateTime('d M Y H:i:s'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Kapan')
                            ->state(fn (ActivityLog $record) => $record->created_at?->diffForHumans())
                            ->badge()
                            ->color('gray'),
                        Infolists\Components\TextEntry::make('user_name')
                            ->label('User')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('user_role')
                            ->label('Role')
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('aksi')
                            ->label('Aksi')
                            ->badge()
                            ->color(fn (ActivityLog $record): string => $record->aksi_color)
                            ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                        Infolists\Components\TextEntry::make('model_short_name')
                            ->label('Model')
                            ->badge()
                            ->color('warning'),
                        Infolists\Components\TextEntry::make('model_id')
                            ->label('Record ID'),
                        Infolists\Components\TextEntry::make('ip_address')
                            ->label('IP Address')
                            ->badge()
                            ->color('gray'),
                        Infolists\Components\TextEntry::make('user_agent')
                            ->label('Browser / User Agent')
                            ->columnSpanFull(),
                    ])->columns(3),

                Infolists\Components\Section::make('Deskripsi')
                    ->schema([
                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('📦 Data Lama (Sebelum Perubahan)')
                    ->description('Cuma ada di log updated & deleted')
                    ->schema([
                        Infolists\Components\TextEntry::make('data_lama')
                            ->label('')
                            ->state(fn (ActivityLog $record) => json_encode($record->data_lama, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                            ->fontFamily('mono')
                            ->copyable()
                            ->columnSpanFull()
                            ->placeholder('(gak ada data lama)'),
                    ])
                    ->collapsible()
                    ->collapsed(fn (ActivityLog $record) => empty($record->data_lama)),

                Infolists\Components\Section::make('✨ Data Baru (Sesudah Perubahan)')
                    ->description('Cuma ada di log created & updated')
                    ->schema([
                        Infolists\Components\TextEntry::make('data_baru')
                            ->label('')
                            ->state(fn (ActivityLog $record) => json_encode($record->data_baru, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                            ->fontFamily('mono')
                            ->copyable()
                            ->columnSpanFull()
                            ->placeholder('(gak ada data baru)'),
                    ])
                    ->collapsible()
                    ->collapsed(fn (ActivityLog $record) => empty($record->data_baru)),
            ]);
    }

    public function getTitle(): string
    {
        return 'Detail Activity Log #' . $this->record->id;
    }
}