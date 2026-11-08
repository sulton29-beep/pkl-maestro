<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenilaianDudiResource\Pages;
use App\Models\Nilai;
use App\Models\PenempatanPkl;
use App\Services\HitungNilaiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PenilaianDudiResource extends Resource
{
    protected static ?string $model = Nilai::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Penilaian Saya';
    protected static ?string $modelLabel = 'Penilaian Siswa';
    protected static ?string $pluralModelLabel = 'Penilaian Siswa PKL';
    protected static ?string $navigationGroup = 'Penilaian PKL';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Siswa PKL')
                ->schema([
                    Forms\Components\Select::make('penempatan_pkl_id')
                        ->label('Siswa — DUDI')
                        ->options(function () {
                            $email = auth()->user()->email;
                            return PenempatanPkl::query()
                                ->whereHas('dudi', fn ($q) => $q->where('email', $email))
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

            Forms\Components\Section::make('Nilai Kinerja & Kompetensi')
                ->description('Berikan penilaian sesuai performa siswa selama PKL di perusahaan Anda.')
                ->schema([
                    Forms\Components\TextInput::make('nilai_kinerja')
                        ->label('Nilai Kinerja')
                        ->numeric()->minValue(0)->maxValue(100)->suffix('/ 100')
                        ->required()
                        ->helperText('Kedisiplinan, kehadiran, sikap kerja, kerja sama'),
                    Forms\Components\TextInput::make('nilai_kompetensi')
                        ->label('Nilai Kompetensi')
                        ->numeric()->minValue(0)->maxValue(100)->suffix('/ 100')
                        ->required()
                        ->helperText('Kemampuan teknis, kecepatan kerja, kualitas hasil kerja'),
                    Forms\Components\Textarea::make('catatan_dudi')
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
                    ->label('Perusahaan')->toggleable(),
                Tables\Columns\TextColumn::make('nilai_kinerja')
                    ->label('Kinerja')->numeric()->badge()->color('info'),
                Tables\Columns\TextColumn::make('nilai_kompetensi')
                    ->label('Kompetensi')->numeric()->badge()->color('info'),
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
            ->emptyStateHeading('Belum ada siswa yang dinilai')
            ->emptyStateDescription('Klik tombol "Isi Nilai Siswa" di kanan atas untuk mulai memberikan penilaian.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check')
            ->defaultSort('updated_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // DUDI hanya lihat nilai siswa di perusahaannya
        if (auth()->user()?->hasRole('dudi')) {
            $email = auth()->user()->email;
            $query->whereHas('penempatanPkl.dudi', fn ($q) => $q->where('email', $email));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPenilaianDudis::route('/'),
            'create' => Pages\CreatePenilaianDudi::route('/create'),
            'edit'   => Pages\EditPenilaianDudi::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('dudi') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('dudi') ?? false;
    }
}