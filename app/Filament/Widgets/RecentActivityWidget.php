<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class RecentActivityWidget extends Widget
{
    protected static string $view = 'filament.widgets.recent-activity';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 99;

    /**
     * Auto-refresh tiap 30 detik.
     */
    protected static ?string $pollingInterval = '30s';

    /**
     * Cuma admin yang bisa liat widget ini.
     */
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    /**
     * Ambil 10 log terbaru.
     */
    public function getActivities(): Collection
    {
        return ActivityLog::query()
            ->latest('created_at')
            ->limit(10)
            ->get();
    }

    /**
     * URL ke halaman Activity Log full.
     */
    public function getSeeAllUrl(): string
    {
        return \App\Filament\Resources\ActivityLogResource::getUrl('index');
    }
}