<?php

namespace App\Filament\Pages;

use App\Models\TahunAjaran;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PengaturanTahunAktif extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationLabel = 'Tahun Aktif';

    protected static ?string $title = 'Pengaturan Tahun Aktif';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.pengaturan-tahun-aktif';

    /**
     * Cuma admin yang bisa akses.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    /**
     * Data yang dikirim ke blade view.
     */
    protected function getViewData(): array
    {
        return [
            'tahunAktif'  => TahunAjaran::where('is_active', true)->first(),
            'semuaTahun'  => TahunAjaran::orderBy('tanggal_mulai', 'desc')->get(),
        ];
    }

    /**
     * Action: jadikan tahun ini sebagai tahun aktif.
     */
    public function jadikanAktif($id): void
    {
        $tahun = TahunAjaran::find($id);
        if (! $tahun) {
            Notification::make()
                ->title('❌ Tahun ajaran gak ditemukan')
                ->danger()
                ->send();
            return;
        }

        // Non-aktifkan semua tahun lain
        TahunAjaran::where('id', '!=', $id)->update(['is_active' => false]);

        // Set tahun ini sebagai aktif
        $tahun->update(['is_active' => true]);

        Notification::make()
            ->title('✅ Tahun aktif diubah')
            ->body('Tahun ajaran aktif sekarang: ' . $tahun->nama)
            ->success()
            ->send();
    }

    /**
     * Action: toggle arsip.
     */
    public function toggleArsip($id): void
    {
        $tahun = TahunAjaran::find($id);
        if (! $tahun) {
            Notification::make()
                ->title('❌ Tahun ajaran gak ditemukan')
                ->danger()
                ->send();
            return;
        }

        if ($tahun->is_active) {
            Notification::make()
                ->title('⚠️ Tidak bisa arsipkan tahun aktif')
                ->body('Non-aktifkan dulu sebelum di-arsip.')
                ->warning()
                ->send();
            return;
        }

        $tahun->update(['is_arsip' => ! $tahun->is_arsip]);

        Notification::make()
            ->title($tahun->is_arsip ? '🔒 Tahun di-arsipkan' : '🔓 Arsip dibuka')
            ->body('Tahun: ' . $tahun->nama)
            ->success()
            ->send();
    }
}