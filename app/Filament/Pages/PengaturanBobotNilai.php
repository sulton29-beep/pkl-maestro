<?php

namespace App\Filament\Pages;

use App\Models\ConfigBobot;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PengaturanBobotNilai extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationLabel = 'Bobot Nilai PKL';
    protected static ?string $title = 'Pengaturan Bobot Nilai PKL';
    protected static ?string $navigationGroup = 'Penilaian PKL';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.pengaturan-bobot-nilai';

    public ?array $data = [];

    public function mount(): void
    {
        $config = ConfigBobot::getActive();
        $this->form->fill([
            'bobot_dudi'   => $config->bobot_dudi,
            'bobot_guru'   => $config->bobot_guru,
            'bobot_sidang' => $config->bobot_sidang,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bobot Penilaian PKL')
                    ->description('Total bobot harus = 100%. Bobot menentukan kontribusi tiap penilai terhadap nilai akhir siswa.')
                    ->schema([
                        Forms\Components\TextInput::make('bobot_dudi')
                            ->label('Bobot DUDI (%)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->helperText('Nilai dari pembimbing perusahaan (kinerja + kompetensi)'),
                        Forms\Components\TextInput::make('bobot_guru')
                            ->label('Bobot Guru Pembimbing (%)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->helperText('Nilai dari guru pembimbing (laporan + presentasi)'),
                        Forms\Components\TextInput::make('bobot_sidang')
                            ->label('Bobot Sidang (%)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->helperText('Nilai dari guru penguji sidang'),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $total = (float) $data['bobot_dudi'] + (float) $data['bobot_guru'] + (float) $data['bobot_sidang'];

        if (abs($total - 100) > 0.01) {
            Notification::make()
                ->title('Total bobot harus 100%')
                ->body("Total bobot saat ini: {$total}%. Mohon sesuaikan.")
                ->danger()
                ->send();
            return;
        }

        $config = ConfigBobot::getActive();
        $config->update([
            'bobot_dudi'   => $data['bobot_dudi'],
            'bobot_guru'   => $data['bobot_guru'],
            'bobot_sidang' => $data['bobot_sidang'],
            'updated_by'   => auth()->id(),
        ]);

        Notification::make()
            ->title('Bobot berhasil disimpan')
            ->body("DUDI: {$data['bobot_dudi']}% | Guru: {$data['bobot_guru']}% | Sidang: {$data['bobot_sidang']}%")
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}