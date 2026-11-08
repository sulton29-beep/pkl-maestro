<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NilaiResource\Pages;
use App\Models\Nilai;
use App\Models\PenempatanPkl;
use App\Models\TahunAjaran;
use App\Services\HitungNilaiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NilaiResource extends Resource
{
    protected static ?string $model = Nilai::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Nilai PKL';
    protected static ?string $modelLabel = 'Nilai PKL';
    protected static ?string $pluralModelLabel = 'Nilai PKL';
    protected static ?string $navigationGroup = 'Penilaian PKL';
    protected static ?int $navigationSort = 2;

    /**
     * Cek apakah record ini tahun arsip (readonly).
     */
    public static function isArsip($record): bool
    {
        return (bool) ($record?->penempatanPkl?->tahunAjaran?->is_arsip ?? false);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Info Siswa')
                ->schema([
                    Forms\Components\Select::make('penempatan_pkl_id')
                        ->label('Penempatan PKL (Siswa - DUDI)')
                        ->relationship('penempatanPkl', 'id')
                        ->getOptionLabelFromRecordUsing(
                            fn (PenempatanPkl $record) =>
                                ($record->siswa?->nama ?? 'Siswa #' . $record->siswa_id)
                                . ' — '
                                . ($record->dudi?->nama_perusahaan ?? 'DUDI #' . $record->dudi_id)
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $p = PenempatanPkl::find($state);
                            $set('siswa_id', $p?->siswa_id);
                        })
                        ->disabledOn('edit'),
                    Forms\Components\Hidden::make('siswa_id'),
                ])->columns(1),

            Forms\Components\Section::make('Nilai dari DUDI (Pembimbing Perusahaan)')
                ->description('Input oleh DUDI. Admin bisa override di sini.')
                ->schema([
                    Forms\Components\TextInput::make('nilai_kinerja')
                        ->label('Nilai Kinerja')
                        ->numeric()->minValue(0)->maxValue(100)->suffix('/ 100'),
                    Forms\Components\TextInput::make('nilai_kompetensi')
                        ->label('Nilai Kompetensi')
                        ->numeric()->minValue(0)->maxValue(100)->suffix('/ 100'),
                    Forms\Components\Textarea::make('catatan_dudi')
                        ->label('Catatan DUDI')->rows(3)->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Nilai dari Guru Pembimbing')
                ->description('Input oleh guru pembimbing. Admin bisa override di sini.')
                ->schema([
                    Forms\Components\TextInput::make('nilai_laporan')
                        ->label('Nilai Laporan')
                        ->numeric()->minValue(0)->maxValue(100)->suffix('/ 100'),
                    Forms\Components\TextInput::make('nilai_presentasi')
                        ->label('Nilai Presentasi')
                        ->numeric()->minValue(0)->maxValue(100)->suffix('/ 100'),
                    Forms\Components\Textarea::make('catatan_guru')
                        ->label('Catatan Guru')->rows(3)->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Nilai Sidang')
                ->description('Input oleh guru penguji. Admin bisa override di sini.')
                ->schema([
                    Forms\Components\TextInput::make('nilai_sidang')
                        ->label('Nilai Sidang')
                        ->numeric()->minValue(0)->maxValue(100)->suffix('/ 100'),
                    Forms\Components\Textarea::make('catatan_penguji')
                        ->label('Catatan Penguji')->rows(3)->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Hasil Akhir')
                ->description('Dihitung otomatis berdasarkan bobot di menu "Bobot Nilai PKL".')
                ->schema([
                    Forms\Components\Placeholder::make('nilai_akhir_display')
                        ->label('Nilai Akhir')
                        ->content(fn ($record) => $record?->nilai_akhir ?? '—'),
                    Forms\Components\Placeholder::make('predikat_display')
                        ->label('Predikat')
                        ->content(fn ($record) => HitungNilaiService::predikat(
                            $record?->nilai_akhir !== null ? (float) $record->nilai_akhir : null
                        )),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('penempatanPkl.tahunAjaran.nama')
                    ->label('Tahun Ajaran')
                    ->badge()
                    ->color(fn ($record): string => static::isArsip($record) ? 'gray' : 'info')
                    ->icon(fn ($record): ?string => static::isArsip($record) ? 'heroicon-o-lock-closed' : null)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Siswa')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('siswa.kelas')
                    ->label('Kelas')->sortable(),
                Tables\Columns\TextColumn::make('penempatanPkl.dudi.nama_perusahaan')
                    ->label('DUDI')->searchable()->sortable()->wrap(),
                Tables\Columns\TextColumn::make('nilai_kinerja')
                    ->label('Kinerja')->numeric()->toggleable(),
                Tables\Columns\TextColumn::make('nilai_kompetensi')
                    ->label('Kompetensi')->numeric()->toggleable(),
                Tables\Columns\TextColumn::make('nilai_laporan')
                    ->label('Laporan')->numeric()->toggleable(),
                Tables\Columns\TextColumn::make('nilai_presentasi')
                    ->label('Presentasi')->numeric()->toggleable(),
                Tables\Columns\TextColumn::make('nilai_sidang')
                    ->label('Sidang')->numeric()->toggleable(),
                Tables\Columns\TextColumn::make('nilai_akhir')
                    ->label('Nilai Akhir')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === null => 'gray',
                        $state >= 90   => 'success',
                        $state >= 80   => 'info',
                        $state >= 70   => 'warning',
                        default        => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('predikat')
                    ->label('Predikat')
                    ->getStateUsing(fn ($record) => HitungNilaiService::predikat(
                        $record->nilai_akhir !== null ? (float) $record->nilai_akhir : null
                    ))
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'A' => 'success',
                        'B' => 'info',
                        'C' => 'warning',
                        'D', 'E' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(fn () => TahunAjaran::orderBy('tanggal_mulai', 'desc')->pluck('nama', 'id'))
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['value'],
                            fn ($q, $v) => $q->whereHas(
                                'penempatanPkl',
                                fn ($sub) => $sub->where('tahun_ajaran_id', $v)
                            )
                        );
                    })
                    ->default(fn () => TahunAjaran::getAktifId()),
                Tables\Filters\SelectFilter::make('jurusan')
                    ->label('Jurusan')
                    ->relationship('siswa.jurusan', 'nama'),
                Tables\Filters\SelectFilter::make('dudi')
                    ->label('DUDI')
                    ->relationship('penempatanPkl.dudi', 'nama_perusahaan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->disabled(fn ($record): bool => static::isArsip($record))
                    ->tooltip(fn ($record): ?string => static::isArsip($record) ? '🔒 Data tahun arsip — readonly' : null),
                Tables\Actions\DeleteAction::make()
                    ->disabled(fn ($record): bool => static::isArsip($record))
                    ->tooltip(fn ($record): ?string => static::isArsip($record) ? '🔒 Data tahun arsip — readonly' : null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListNilais::route('/'),
            'create' => Pages\CreateNilai::route('/create'),
            'edit'   => Pages\EditNilai::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'kepala_sekolah', 'waka_kurikulum', 'kaprodi']) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}