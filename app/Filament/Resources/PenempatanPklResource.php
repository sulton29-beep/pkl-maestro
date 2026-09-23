<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenempatanPklResource\Pages;
use App\Models\PenempatanPkl;
use App\Models\Siswa;
use App\Models\Dudi;
use App\Models\Guru;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PenempatanPklResource extends Resource
{
    protected static ?string $model = PenempatanPkl::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Penempatan PKL';

    protected static ?string $modelLabel = 'Penempatan PKL';

    protected static ?string $pluralModelLabel = 'Penempatan PKL';

    protected static ?string $navigationGroup = 'PKL';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'waka_kurikulum', 'kaprodi', 'guru_pembimbing', 'guru_penguji']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('siswa_id')
                    ->label('Siswa')
                    ->options(Siswa::pluck('nama', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('dudi_id')
                    ->label('DUDI')
                    ->options(Dudi::pluck('nama_perusahaan', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('guru_pembimbing_id')
                    ->label('Guru Pembimbing')
                    ->options(Guru::pluck('nama', 'id'))
                    ->searchable(),
                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'berjalan' => 'Berjalan',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->default('menunggu')
                    ->required(),
                Forms\Components\Textarea::make('catatan')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Siswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dudi.nama_perusahaan')
                    ->label('DUDI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guruPembimbing.nama')
                    ->label('Guru Pembimbing')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'disetujui' => 'info',
                        'berjalan' => 'primary',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    }),
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
            'index' => Pages\ListPenempatanPkls::route('/'),
            'create' => Pages\CreatePenempatanPkl::route('/create'),
            'edit' => Pages\EditPenempatanPkl::route('/{record}/edit'),
        ];
    }
}