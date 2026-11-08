<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Activity Log';

    protected static ?string $modelLabel = 'Activity Log';

    protected static ?string $pluralModelLabel = 'Activity Log';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 4;

    /**
     * Cuma admin yang bisa akses menu ini.
     */
    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    /**
     * Read-only — log gak boleh dibikin manual.
     */
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
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function form(Form $form): Form
    {
        // Read-only — gak ada form (create/edit disabled)
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->description(fn (ActivityLog $record) => $record->created_at?->diffForHumans()),

                Tables\Columns\TextColumn::make('user_name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->description(fn (ActivityLog $record) => $record->user_role ?? '-'),

                Tables\Columns\TextColumn::make('aksi')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (ActivityLog $record): string => $record->aksi_color)
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => '🆕 Created',
                        'updated' => '✏️ Updated',
                        'deleted' => '🗑️ Deleted',
                        'login'   => '🔑 Login',
                        'logout'  => '🚪 Logout',
                        default   => $state,
                    }),

                Tables\Columns\TextColumn::make('model_short_name')
                    ->label('Model')
                    ->badge()
                    ->color('primary')
                    ->searchable(query: function (Builder $query, string $search) {
                        return $query->where('model', 'like', "%{$search}%");
                    }),

                Tables\Columns\TextColumn::make('model_id')
                    ->label('ID')
                    ->numeric()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->wrap()
                    ->limit(80)
                    ->searchable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Browser')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('aksi')
                    ->label('Aksi')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'login'   => 'Login',
                        'logout'  => 'Logout',
                    ]),
                Tables\Filters\SelectFilter::make('model')
                    ->label('Model')
                    ->options([
                        'App\Models\Siswa'      => 'Siswa',
                        'App\Models\Guru'       => 'Guru',
                        'App\Models\Dudi'       => 'DUDI',
                        'App\Models\Logbook'    => 'Logbook',
                        'App\Models\Absensi'    => 'Absensi',
                        'App\Models\Nilai'      => 'Nilai',
                        'App\Models\Sertifikat' => 'Sertifikat',
                        'App\Models\User'       => 'User',
                    ]),
                Tables\Filters\SelectFilter::make('user_role')
                    ->label('Role')
                    ->options([
                        'admin'           => 'Admin Sekolah',
                        'kepala_sekolah'  => 'Kepala Sekolah',
                        'waka_kurikulum'  => 'Waka Kurikulum',
                        'kaprodi'         => 'Kaprodi',
                        'guru_pembimbing' => 'Guru Pembimbing',
                        'guru_penguji'    => 'Guru Penguji',
                        'siswa'           => 'Siswa',
                        'dudi'            => 'DUDI',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->native(false),
                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari'], fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['sampai'], fn ($q, $d) => $q->whereDate('created_at', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail'),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasRole('admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('admin')),
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
            'index' => Pages\ListActivityLogs::route('/'),
            'view'  => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
}