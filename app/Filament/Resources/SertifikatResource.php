<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SertifikatResource\Pages;
use App\Models\Sertifikat;
use App\Models\TahunAjaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SertifikatResource extends Resource
{
    protected static ?string $model = Sertifikat::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationGroup = 'Sertifikat PKL';
    protected static ?string $navigationLabel = 'Kelola Sertifikat';
    protected static ?string $modelLabel = 'Sertifikat';
    protected static ?string $pluralModelLabel = 'Sertifikat PKL';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return Auth::user()?->hasAnyRole(['admin', 'kepala_sekolah', 'waka_kurikulum', 'kaprodi']) ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }

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
            Forms\Components\Section::make('Data Sertifikat')
                ->schema([
                    Forms\Components\Select::make('penempatan_pkl_id')
                        ->label('Penempatan PKL')
                        ->relationship('penempatanPkl', 'id')
                        ->getOptionLabelFromRecordUsing(
                            fn ($record) => ($record->siswa->nama ?? 'Siswa #' . $record->siswa_id)
                                . ' — ' . ($record->dudi->nama_perusahaan ?? 'DUDI #' . $record->dudi_id)
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->disabledOn('edit')
                        ->dehydrated()
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('nomor_sertifikat')
                        ->label('Nomor Sertifikat')
                        ->maxLength(255)
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Otomatis digenerate oleh sistem')
                        ->helperText('Nomor otomatis digenerate saat simpan. Tidak perlu diisi manual.'),

                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options(Sertifikat::statusList())
                        ->default(Sertifikat::STATUS_DRAFT)
                        ->required(),

                    Forms\Components\DatePicker::make('tanggal_terbit')
                        ->label('Tanggal Terbit')
                        ->native(false),

                    Forms\Components\FileUpload::make('ttd_kepsek')
                        ->label('TTD Kepala Sekolah')
                        ->image()
                        ->directory('ttd')
                        ->imageEditor()
                        ->maxSize(1024)
                        ->helperText('Upload gambar TTD (PNG transparan disarankan, max 1MB).'),

                    Forms\Components\FileUpload::make('ttd_dudi')
                        ->label('TTD Pimpinan DUDI')
                        ->image()
                        ->directory('ttd')
                        ->imageEditor()
                        ->maxSize(1024),

                    Forms\Components\Textarea::make('catatan_revisi')
                        ->label('Catatan')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
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

                Tables\Columns\TextColumn::make('nomor_sertifikat')
                    ->label('No. Sertifikat')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Siswa')
                    ->searchable()
                    ->description(fn ($record) => ($record->siswa->nis ?? '-') . ' — ' . ($record->siswa->kelas ?? '-')),

                Tables\Columns\TextColumn::make('penempatanPkl.dudi.nama_perusahaan')
                    ->label('DUDI')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => Sertifikat::statusList()[$state] ?? $state)
                    ->colors([
                        'gray' => Sertifikat::STATUS_DRAFT,
                        'warning' => Sertifikat::STATUS_VALIDASI_KAPRODI,
                        'info' => Sertifikat::STATUS_DISETUJUI_WAKA,
                        'success' => Sertifikat::STATUS_TERBIT,
                        'danger' => Sertifikat::STATUS_DITOLAK,
                    ]),

                Tables\Columns\TextColumn::make('tanggal_terbit')
                    ->label('Terbit')
                    ->date('d M Y')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(Sertifikat::statusList()),
            ])
            ->actions([
                Tables\Actions\Action::make('preview_pdf')
                    ->label('Preview PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->url(fn (Sertifikat $record) => route('sertifikat.download', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => Auth::user()?->hasRole('admin'))
                    ->disabled(fn ($record): bool => static::isArsip($record))
                    ->tooltip(fn ($record): ?string => static::isArsip($record) ? '🔒 Data tahun arsip — readonly' : null),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => Auth::user()?->hasRole('admin'))
                    ->disabled(fn ($record): bool => static::isArsip($record))
                    ->tooltip(fn ($record): ?string => static::isArsip($record) ? '🔒 Data tahun arsip — readonly' : null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->hasRole('admin')),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada sertifikat')
            ->emptyStateDescription('Klik tombol "Buat Sertifikat" di atas untuk mulai.')
            ->emptyStateIcon('heroicon-o-document-check');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['siswa', 'penempatanPkl.dudi', 'penempatanPkl.tahunAjaran']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSertifikats::route('/'),
            'create' => Pages\CreateSertifikat::route('/create'),
            'edit' => Pages\EditSertifikat::route('/{record}/edit'),
        ];
    }
}