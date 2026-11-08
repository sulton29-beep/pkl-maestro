<?php

namespace App\Filament\Widgets;

use App\Models\Jurusan;
use App\Models\Siswa;
use Filament\Widgets\ChartWidget;

class ChartSiswaPerJurusan extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Siswa per Jurusan';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole(['admin', 'kepala_sekolah', 'waka_kurikulum', 'kaprodi']);
    }

    protected function getData(): array
    {
        $jurusans = Jurusan::all();
        $labels = [];
        $data = [];
        $colors = ['#1e40af', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#06b6d4'];

        foreach ($jurusans as $idx => $jurusan) {
            $labels[] = $jurusan->nama ?? 'Jurusan ' . $jurusan->id;
            $data[] = Siswa::where('jurusan_id', $jurusan->id)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Siswa',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}