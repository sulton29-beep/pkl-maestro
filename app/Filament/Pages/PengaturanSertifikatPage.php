<?php

namespace App\Filament\Pages;

use App\Models\PengaturanSertifikat;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class PengaturanSertifikatPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Sertifikat PKL';
    protected static ?string $navigationLabel = 'Pengaturan Sertifikat';
    protected static ?string $title = 'Pengaturan Sertifikat';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.pengaturan-sertifikat';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }

    public function mount(): void
    {
        $setting = PengaturanSertifikat::getActive();
        $this->form->fill($setting->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Sekolah')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Forms\Components\TextInput::make('nama_sekolah')
                            ->label('Nama Sekolah')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('npsn')
                            ->label('NPSN')
                            ->maxLength(50),

                        Forms\Components\Textarea::make('alamat_sekolah')
                            ->label('Alamat Sekolah')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Template Sertifikat')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\TextInput::make('judul_sertifikat')
                            ->label('Judul Sertifikat')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('nomor_format')
                            ->label('Format Nomor Sertifikat')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Placeholder: {no} = nomor urut, {tahun} = tahun, {bulan} = bulan. Contoh: {no}/PKL/SMKS-MST/{tahun}')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('kalimat_pembuka')
                            ->label('Kalimat Pembuka')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('kalimat_penutup')
                            ->label('Kalimat Penutup')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\ColorPicker::make('warna_utama')
                            ->label('Warna Utama')
                            ->default('#1e40af'),
                    ]),

                Forms\Components\Section::make('Tanda Tangan Digital')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Forms\Components\TextInput::make('nama_kepsek')
                            ->label('Nama Kepala Sekolah')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('nip_kepsek')
                            ->label('NIP Kepala Sekolah')
                            ->maxLength(50),

                        Forms\Components\FileUpload::make('ttd_kepsek')
                            ->label('Gambar TTD Kepala Sekolah')
                            ->image()
                            ->directory('ttd')
                            ->imageEditor()
                            ->maxSize(1024)
                            ->helperText('PNG transparan disarankan, max 1MB.'),

                        Forms\Components\FileUpload::make('ttd_dudi')
                            ->label('Gambar TTD Pimpinan DUDI (Default)')
                            ->image()
                            ->directory('ttd')
                            ->imageEditor()
                            ->maxSize(1024),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $setting = PengaturanSertifikat::getActive();
        $setting->update($data);

        Notification::make()
            ->title('Pengaturan disimpan')
            ->body('Template sertifikat berhasil diperbarui.')
            ->success()
            ->send();
    }
}