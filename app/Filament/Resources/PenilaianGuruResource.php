<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenilaianGuruResource\Pages;
use App\Models\Guru;
use App\Models\Nilai;
use App\Models\PenempatanPkl;
use App\Services\HitungNilaiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PenilaianGuruResource extends Resource
{
    protected static ?string $model = Nilai::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Penilaian Laporan';
    protected static ?string $modelLabel = 'Penilaian Laporan';
    protected static ?string $pluralModelLabel = 'Penilaian Laporan Siswa';
    protected static ?string $navigationGroup = 'Penilaian PKL';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Siswa Bimbingan')
                ->schema([
                    Forms\Components\Select::make('penempatan_pkl_id')
                        ->label('Siswa — DUDI')
                        ->options(function () {
                            $userId = auth()->id();

                            return PenempatanPkl::query()
                                ->whereHas('guruPembimbing', fn ($q) => $q->where('user_id', $userId))
                                ->with(['siswa', 'dudi'])
                                ->get()
                                ->mapWithKeys(fn ($p) => [
                                    $p->id => ($p->siswa?->nama ?? 'Siswa #' . $p->siswa_id)
                                        . ' — ' . ($p->siswa?->kelas ?? '-')
                                        . ' (' . ($p->dudi?->nama_perusahaan ?? '-') . ')',
                                ]);
                        })
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $p = PenempatanPkl::find($state);
                            $set('siswa_id', $p?->siswa_id);
                        })
                        ->disabledOn('edit'),
                    Forms\Components\Hidden::make('siswa_id'),
                ]),

            Forms\Components\Section::make('Nilai Laporan & Presentasi')
                ->description('Nilai laporan PKL dan presentasi siswa bimbingan Anda.')
                ->schema([
                    Forms\Components\TextInput::make('nilai_laporan')
                        ->label('Nilai Laporan')
                        ->numeric()->minValue(0)->maxValue(100)->suffix('/ 100')
                        ->required()
                        ->helperText('Kualitas isi laporan, kelengkapan, sistematika, tata bahasa'),
                    Forms\Components\TextInput::make('nilai_presentasi')
                        ->label('Nilai Presentasi')
                        ->numeric()->minValue(0)->maxValue(100)->suffix('/ 100')
                        ->required()
                        ->helperText('Penguasaan materi, penyampaian, kemampuan menjawab'),
                    Forms\Components\Textarea::make('catatan_guru')
                        ->label('Catatan / Masukan')
                        ->rows(4)
                        ->columnSpanFull()
                        ->placeholder('Catatan untuk siswa (opsional)'),
                ])->columns(2),

            Forms\Components\Section::make('Hasil Akhir (Otomatis)')
                ->description('Dihitung berdasarkan bobot dari admin.')
                ->schema([
                    Forms\Components\Placeholder::make('nilai_akhir_display')
                        ->label('Nilai Akhir')
                        ->content(fn ($record) => $record?->nilai_akhir ?? '—'),
                    Forms\Components\Placeholder::make('predikat_display')
                        ->label('Predikat')
                        ->content(fn ($record) => HitungNilaiService::predikat(
                            $record?->nilai_akhir !== null ? (float) $record->nilai_akhir : null
                        )),
                ])
                ->columns(2)
                ->visibleOn('edit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('siswa.kelas')
                    ->label('Kelas')->sortable(),
                Tables\Columns\TextColumn::make('siswa.jurusan.nama')
                    ->label('Jurusan')->toggleable(),
                Tables\Columns\TextColumn::make('penempatanPkl.dudi.nama_perusahaan')
                    ->label('DUDI')->toggleable()->wrap(),
                Tables\Columns\TextColumn::make('nilai_laporan')
                    ->label('Laporan')->numeric()->badge()->color('info'),
                Tables\Columns\TextColumn::make('nilai_presentasi')
                    ->label('Presentasi')->numeric()->badge()->color('info'),
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
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Nilai / Ubah'),
            ])
            ->bulkActions([])
            ->emptyStateHeading('Belum ada siswa bimbingan yang dinilai')
            ->emptyStateDescription('Klik tombol "Isi Nilai Siswa" di kanan atas untuk mulai memberikan penilaian laporan.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->defaultSort('updated_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Guru pembimbing hanya lihat siswa bimbingannya
        if (auth()->user()?->hasRole('guru_pembimbing')) {
            $userId = auth()->id();
            $query->whereHas('penempatanPkl.guruPembimbing', fn ($q) => $q->where('user_id', $userId));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPenilaianGurus::route('/'),
            'create' => Pages\CreatePenilaianGuru::route('/create'),
            'edit'   => Pages\EditPenilaianGuru::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('guru_pembimbing') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('guru_pembimbing') ?? false;
    }
}