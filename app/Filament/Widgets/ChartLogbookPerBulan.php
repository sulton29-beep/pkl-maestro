<?php

namespace App\Filament\Widgets;

use App\Models\Logbook;
use Filament\Widgets\ChartWidget;

class ChartLogbookPerBulan extends ChartWidget
{
    protected static ?string $heading = 'Logbook per Bulan (12 bulan terakhir)';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole(['admin', 'kepala_sekolah', 'waka_kurikulum', 'kaprodi']);
    }

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');
            $data[] = Logbook::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Entri Logbook',
                    'data' => $data,
                    'borderColor' => '#1e40af',
                    'backgroundColor' => 'rgba(30, 64, 175, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}