<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Aksi yang TIDAK mengirim notifikasi ke admin (biar gak spam).
     */
    protected static array $skipNotifAksi = ['login', 'logout'];

    /**
     * Log aktivitas umum.
     */
    public static function log(
        string $aksi,
        string $model,
        ?int $modelId = null,
        ?string $deskripsi = null,
        ?array $dataLama = null,
        ?array $dataBaru = null
    ): void {
        try {
            $user = Auth::user();

            $log = ActivityLog::create([
                'user_id'    => $user?->id,
                'user_name'  => $user?->name ?? 'Guest',
                'user_role'  => $user ? ($user->getRoleNames()->first() ?? '-') : '-',
                'aksi'       => $aksi,
                'model'      => $model,
                'model_id'   => $modelId,
                'deskripsi'  => $deskripsi,
                'data_lama'  => $dataLama,
                'data_baru'  => $dataBaru,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Kirim database notification ke semua admin
            // (skip kalau aksi login/logout, skip ke user yang lagi login)
            self::kirimNotifKeAdmin($log, $user?->id);
        } catch (\Throwable $e) {
            Log::error('[ActivityLogger] Gagal log: ' . $e->getMessage());
        }
    }

    /**
     * Kirim database notification ke semua user role admin,
     * KECUALI admin yang sedang login (biar gak spam diri sendiri).
     */
    protected static function kirimNotifKeAdmin(ActivityLog $log, ?int $currentUserId): void
    {
        // Skip aksi yang gak perlu notif
        if (in_array($log->aksi, self::$skipNotifAksi)) {
            return;
        }

        try {
            // Ambil semua user dengan role admin, kecuali yang sedang login
            $admins = User::role('admin')
                ->when($currentUserId, fn ($q) => $q->where('id', '!=', $currentUserId))
                ->get();

            foreach ($admins as $admin) {
                FilamentNotification::make()
                    ->title(self::judulNotif($log))
                    ->body($log->deskripsi ?? 'Ada aktivitas baru di sistem.')
                    ->icon(self::iconNotif($log->aksi))
                    ->iconColor($log->aksi_color)
                    ->actions([
                        Action::make('lihat')
                            ->label('Lihat Detail')
                            ->url(\App\Filament\Resources\ActivityLogResource::getUrl('index'))
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin);
            }
        } catch (\Throwable $e) {
            Log::error('[ActivityLogger] Gagal kirim notif admin: ' . $e->getMessage());
        }
    }

    protected static function judulNotif(ActivityLog $log): string
    {
        $emoji = match ($log->aksi) {
            'created' => '🆕',
            'updated' => '✏️',
            'deleted' => '🗑️',
            default   => '🔔',
        };

        return $emoji . ' ' . ucfirst($log->aksi) . ' — ' . $log->model_short_name;
    }

    protected static function iconNotif(string $aksi): string
    {
        return match ($aksi) {
            'created' => 'heroicon-o-plus-circle',
            'updated' => 'heroicon-o-pencil-square',
            'deleted' => 'heroicon-o-trash',
            'login'   => 'heroicon-o-key',
            'logout'  => 'heroicon-o-arrow-right-on-rectangle',
            default   => 'heroicon-o-bell',
        };
    }

    /* ==========================================================
     * Helper shortcut
     * ========================================================== */

    /**
     * Log: Data baru dibuat.
     */
    public static function logCreated(string $model, int $modelId, string $label, array $dataBaru = []): void
    {
        self::log(
            aksi: 'created',
            model: $model,
            modelId: $modelId,
            deskripsi: "Membuat {$label} baru",
            dataBaru: $dataBaru
        );
    }

    /**
     * Log: Data diupdate.
     */
    public static function logUpdated(string $model, int $modelId, string $label, array $dataLama = [], array $dataBaru = []): void
    {
        self::log(
            aksi: 'updated',
            model: $model,
            modelId: $modelId,
            deskripsi: "Mengubah {$label}",
            dataLama: $dataLama,
            dataBaru: $dataBaru
        );
    }

    /**
     * Log: Data dihapus.
     */
    public static function logDeleted(string $model, int $modelId, string $label, array $dataLama = []): void
    {
        self::log(
            aksi: 'deleted',
            model: $model,
            modelId: $modelId,
            deskripsi: "Menghapus {$label}",
            dataLama: $dataLama
        );
    }

    /**
     * Log: Login.
     */
    public static function logLogin(): void
    {
        self::log(
            aksi: 'login',
            model: \App\Models\User::class,
            modelId: Auth::id(),
            deskripsi: 'Login ke sistem'
        );
    }

    /**
     * Log: Logout.
     */
    public static function logLogout(): void
    {
        self::log(
            aksi: 'logout',
            model: \App\Models\User::class,
            modelId: Auth::id(),
            deskripsi: 'Logout dari sistem'
        );
    }
}