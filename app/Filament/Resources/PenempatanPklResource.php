<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenempatanPklResource\Pages;
use App\Models\PenempatanPkl;
use App\Models\Siswa;
use App\Models\Dudi;
use App\Models\Guru;
use App\Models\TahunAjaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
        return auth()->user()?->hasAnyRole([
            'admin', 'kepala_sekolah', 'waka_kurikulum', 'kaprodi',
            'guru_pembimbing', 'guru_penguji',
        ]) ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    /**
     * Cek apakah record ini tahun arsip (readonly).
     */
    public static function isArsip($record): bool
    {
        return (bool) ($record?->tahunAjaran?->is_arsip ?? false);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Utama')
                    ->schema([
                        Forms\Components\Select::make('tahun_ajaran_id')
                            ->label('Tahun Ajaran')
                            ->relationship('tahunAjaran', 'nama')
                            ->searchable()
                            ->preload()
                            ->default(fn () => TahunAjaran::getAktifId())
                            ->required(),
                        Forms\Components\Select::make('siswa_id')
                            ->label('Siswa')
                            ->relationship('siswa', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('dudi_id')
                            ->label('DUDI / Perusahaan')
                            ->relationship('dudi', 'nama_perusahaan')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('guru_pembimbing_id')
                            ->label('Guru Pembimbing')
                            ->relationship('guruPembimbing', 'nama')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make('Periode PKL')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->after('tanggal_mulai'),
                    ])->columns(2),

                Forms\Components\Section::make('Status & Catatan')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status PKL')
                            ->options([
                                'menunggu' => 'Menunggu Persetujuan',
                                'disetujui' => 'Disetujui',
                                'berjalan' => 'Sedang Berjalan',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('menunggu')
                            ->required(),
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahunAjaran.nama')
                    ->label('Tahun Ajaran')
                    ->badge()
                    ->color(fn (PenempatanPkl $record): string => $record->tahunAjaran?->is_arsip ? 'gray' : 'info')
                    ->icon(fn (PenempatanPkl $record): ?string => $record->tahunAjaran?->is_arsip ? 'heroicon-o-lock-closed' : null)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Siswa')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('siswa.kelas')
                    ->label('Kelas')->toggleable(),
                Tables\Columns\TextColumn::make('dudi.nama_perusahaan')
                    ->label('DUDI')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('guruPembimbing.nama')
                    ->label('Guru Pembimbing')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Mulai')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->label('Selesai')->date('d M Y')->sortable(),
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
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'berjalan' => 'Berjalan',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship('tahunAjaran', 'nama')
                    ->default(fn () => TahunAjaran::getAktifId())
                    ->searchable()
                    ->preload(),
                Tables\Filters\TrashedFilter::make()
                    ->label('Data Terhapus'),
                Tables\Filters\SelectFilter::make('dudi_id')
                    ->label('DUDI')
                    ->relationship('dudi', 'nama_perusahaan')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('guru_pembimbing_id')
                    ->label('Guru Pembimbing')
                    ->relationship('guruPembimbing', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'berjalan' => 'Berjalan',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->disabled(fn (PenempatanPkl $record): bool => static::isArsip($record))
                    ->tooltip(fn (PenempatanPkl $record): ?string => static::isArsip($record) ? '🔒 Data tahun arsip — readonly' : null),

                Tables\Actions\DeleteAction::make()
                    ->disabled(fn (PenempatanPkl $record): bool => static::isArsip($record))
                    ->tooltip(fn (PenempatanPkl $record): ?string => static::isArsip($record) ? '🔒 Data tahun arsip — readonly' : null),

                Tables\Actions\RestoreAction::make()
                    ->disabled(fn (PenempatanPkl $record): bool => static::isArsip($record)),

                Tables\Actions\ForceDeleteAction::make()
                    ->disabled(fn (PenempatanPkl $record): bool => static::isArsip($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->disabled(fn () => false),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}