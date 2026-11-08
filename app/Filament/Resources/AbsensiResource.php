<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiResource\Pages;
use App\Models\Absensi;
use App\Models\Dudi;
use App\Models\Guru;
use App\Models\PenempatanPkl;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Rekap Absensi';
    protected static ?string $navigationGroup = 'PKL';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dudi.nama_perusahaan')
                    ->label('DUDI')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Masuk')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('jam_keluar')
                    ->label('Keluar')
                    ->time('H:i'),
                Tables\Columns\ImageColumn::make('foto_masuk')
                    ->label('Foto Masuk')
                    ->circular()
                    ->size(60)
                    ->url(fn ($record) => $record->foto_masuk 
                        ? asset('storage/' . $record->foto_masuk) 
                        : null)
                    ->openUrlInNewTab()
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('foto_keluar')
                    ->label('Foto Keluar')
                    ->circular()
                    ->size(60)
                    ->url(fn ($record) => $record->foto_keluar 
                        ? asset('storage/' . $record->foto_keluar) 
                        : null)
                    ->openUrlInNewTab()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('jarak_masuk')
                    ->label('Jarak Masuk (m)')
                    ->numeric()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status_masuk')
                    ->label('Status Masuk')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'valid' => 'success',
                        'pending' => 'warning',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status_keluar')
                    ->label('Status Keluar')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'valid' => 'success',
                        'pending' => 'warning',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('tanggal', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn () => route('export-excel.absensi'))
                    ->openUrlInNewTab(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('siswa_id')
                    ->label('Siswa')
                    ->relationship('siswa', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'], fn ($q, $d) => $q->whereDate('tanggal', '>=', $d))
                            ->when($data['sampai'], fn ($q, $s) => $q->whereDate('tanggal', '<=', $s));
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensis::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        return $user->hasAnyRole([
            'admin',
            'kepala_sekolah',
            'waka_kurikulum',
            'kaprodi',
            'guru_pembimbing',
            'dudi',
        ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole(['admin', 'kepala_sekolah', 'waka_kurikulum', 'kaprodi'])) {
            return $query;
        }

        if ($user->hasRole('guru_pembimbing')) {
            $guru = Guru::where('user_id', $user->id)->first();
            if (!$guru) {
                return $query->whereRaw('1 = 0');
            }

            $siswaIds = PenempatanPkl::where('guru_pembimbing_id', $guru->id)
                ->pluck('siswa_id')
                ->unique()
                ->toArray();

            return $query->whereIn('siswa_id', $siswaIds);
        }

        if ($user->hasRole('dudi')) {
            $dudi = Dudi::where('email', $user->email)->first();
            if (!$dudi) {
                return $query->whereRaw('1 = 0');
            }

            return $query->where('dudi_id', $dudi->id);
        }

        return $query->whereRaw('1 = 0');
    }
}