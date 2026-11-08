<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DudiResource\Pages;
use App\Models\Dudi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;

class DudiResource extends Resource
{
    protected static ?string $model = Dudi::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'DUDI';

    protected static ?string $modelLabel = 'DUDI';

    protected static ?string $pluralModelLabel = 'DUDI';

    protected static ?string $navigationGroup = 'PKL';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'waka_kurikulum', 'kaprodi', 'guru_pembimbing']) ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Perusahaan')
                    ->schema([
                        Forms\Components\TextInput::make('nama_perusahaan')
                            ->label('Nama Perusahaan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bidang_usaha')
                            ->label('Bidang Usaha')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('kota')
                            ->label('Kota')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Kontak & Kuota')
                    ->schema([
                        Forms\Components\TextInput::make('kontak_person')
                            ->label('Kontak Person')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('no_hp')
                            ->label('No. HP')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('kuota')
                            ->label('Kuota Siswa')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('Lokasi GPS (untuk absensi)')
                    ->description('Klik titik di peta untuk set lokasi DUDI. Latitude & Longitude akan terisi otomatis. Atau klik "Deteksi Lokasi Saya" untuk pakai GPS browser.')
                    ->schema([
                        Forms\Components\ViewField::make('map_picker')
                            ->view('filament.forms.map-picker')
                            ->dehydrated(false)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->required()
                            ->helperText('Terisi otomatis dari peta.'),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->required()
                            ->helperText('Terisi otomatis dari peta.'),
                    ])->columns(2),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_perusahaan')
                    ->label('Nama Perusahaan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bidang_usaha')
                    ->label('Bidang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kota')
                    ->label('Kota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kuota')
                    ->label('Kuota')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn () => route('export-excel.dudi'))
                    ->openUrlInNewTab(),
                ImportAction::make()
                    ->importer(\App\Filament\Imports\DudiImporter::class)
                    ->label('Import CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning'),
                Tables\Actions\Action::make('template')
                    ->label('Template CSV')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->url(fn () => route('import-template.download', ['type' => 'dudi']))
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDudis::route('/'),
            'create' => Pages\CreateDudi::route('/create'),
            'edit' => Pages\EditDudi::route('/{record}/edit'),
        ];
    }
}