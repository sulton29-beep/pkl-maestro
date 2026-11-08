<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SertifikatTemplateResource\Pages;
use App\Models\SertifikatTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SertifikatTemplateResource extends Resource
{
    protected static ?string $model = SertifikatTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Sertifikat PKL';
    protected static ?string $navigationLabel = 'Template Sertifikat';
    protected static ?string $modelLabel = 'Template';
    protected static ?string $pluralModelLabel = 'Template Sertifikat';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Template')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Template')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Contoh: Template TBSM 2026')
                        ->columnSpanFull(),

                    Forms\Components\Select::make('jurusan_id')
                        ->label('Untuk Jurusan')
                        ->relationship('jurusan', 'nama')
                        ->searchable()
                        ->preload()
                        ->placeholder('Kosongkan untuk template default (semua jurusan)')
                        ->helperText('Pilih jurusan tertentu, atau kosongkan supaya jadi template fallback untuk semua.'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktifkan Template')
                        ->default(true)
                        ->helperText('Hanya template aktif yang dipakai sistem.'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Gambar Background')
                ->icon('heroicon-o-photo')
                ->description('Upload gambar background sertifikat (A4 landscape, 3508 x 2480 px, PNG/JPG).')
                ->schema([
                    Forms\Components\FileUpload::make('gambar_background')
                        ->label('Gambar Background Sertifikat')
                        ->image()
                        ->required()
                        ->directory('sertifikat-templates')
                        ->imageEditor()
                        ->maxSize(5120)
                        ->helperText('Max 5MB. Rasio A4 landscape (lebar x tinggi = 297:210). Pakai PNG biar tajam.')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Warna & Catatan')
                ->icon('heroicon-o-paint-brush')
                ->schema([
                    Forms\Components\ColorPicker::make('warna_teks')
                        ->label('Warna Teks Overlay')
                        ->default('#1a1a1a')
                        ->helperText('Warna teks biodata (nama, NIS, dll).'),

                    Forms\Components\ColorPicker::make('warna_aksen')
                        ->label('Warna Aksen')
                        ->default('#1e40af')
                        ->helperText('Warna nilai akhir & aksen.'),

                    Forms\Components\Textarea::make('catatan_admin')
                        ->label('Catatan (internal)')
                        ->rows(2)
                        ->placeholder('Contoh: Desain versi 2026, logo baru')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('gambar_background')
                    ->label('Preview')
                    ->height(60)
                    ->width(85),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Template')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->catatan_admin),

                Tables\Columns\TextColumn::make('jurusan.nama')
                    ->label('Jurusan')
                    ->badge()
                    ->color('info')
                    ->placeholder('Default (semua)'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jurusan_id')
                    ->label('Jurusan')
                    ->relationship('jurusan', 'nama')
                    ->placeholder('Semua Jurusan'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada template')
            ->emptyStateDescription('Klik "Buat Template" di atas. Kalau belum ada template, sistem pakai desain default.')
            ->emptyStateIcon('heroicon-o-photo');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSertifikatTemplates::route('/'),
            'create' => Pages\CreateSertifikatTemplate::route('/create'),
            'edit' => Pages\EditSertifikatTemplate::route('/{record}/edit'),
        ];
    }
}