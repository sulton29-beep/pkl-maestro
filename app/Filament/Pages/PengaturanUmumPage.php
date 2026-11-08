<?php

namespace App\Filament\Pages;

use App\Models\PengaturanUmum;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PengaturanUmumPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan Umum';
    protected static ?string $title = 'Pengaturan Umum';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.pengaturan-umum';

    public ?array $data = [];

    /**
     * Hanya admin yang bisa akses.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function mount(): void
    {
        $row = PengaturanUmum::first();
        $this->form->fill($row?->toArray() ?? []);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Sekolah')
                    ->description('Informasi dasar sekolah yang tampil di sertifikat & landing page.')
                    ->schema([
                        Forms\Components\TextInput::make('nama_sekolah')
                            ->label('Nama Sekolah')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('npsn')
                            ->label('NPSN')
                            ->maxLength(20),
                        Forms\Components\Textarea::make('alamat_sekolah')
                            ->label('Alamat')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('telepon')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(30),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(100),
                    ])->columns(2),

                Forms\Components\Section::make('Identitas Aplikasi')
                    ->description('Nama aplikasi, logo, dan favicon.')
                    ->schema([
                        Forms\Components\TextInput::make('nama_aplikasi')
                            ->label('Nama Aplikasi')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->directory('pengaturan')
                            ->imagePreviewHeight('100')
                            ->maxSize(2048),
                        Forms\Components\FileUpload::make('favicon')
                            ->label('Favicon')
                            ->image()
                            ->directory('pengaturan')
                            ->imagePreviewHeight('50')
                            ->maxSize(512),
                    ])->columns(3),

                Forms\Components\Section::make('Tema Warna')
                    ->description('Warna utama & sekunder buat panel admin.')
                    ->schema([
                        Forms\Components\ColorPicker::make('warna_utama')
                            ->label('Warna Utama')
                            ->required(),
                        Forms\Components\ColorPicker::make('warna_sekunder')
                            ->label('Warna Sekunder')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('SEO & Footer')
                    ->description('Meta untuk landing page dan teks footer.')
                    ->schema([
                        Forms\Components\Textarea::make('meta_deskripsi')
                            ->label('Meta Deskripsi')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('footer_text')
                            ->label('Footer Text')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $row = PengaturanUmum::first();

        if ($row) {
            $row->update($data);
        } else {
            PengaturanUmum::create($data);
        }

        Notification::make()
            ->title('✅ Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }
}