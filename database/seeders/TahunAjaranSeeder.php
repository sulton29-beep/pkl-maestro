<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama'            => '2025/2026',
                'tanggal_mulai'   => '2025-07-01',
                'tanggal_selesai' => '2026-06-30',
                'is_active'       => false,
                'is_arsip'        => true,
                'keterangan'      => 'Tahun ajaran lalu — arsip',
            ],
            [
                'nama'            => '2026/2027',
                'tanggal_mulai'   => '2026-07-01',
                'tanggal_selesai' => '2027-06-30',
                'is_active'       => true,
                'is_arsip'        => false,
                'keterangan'      => 'Tahun ajaran aktif',
            ],
            [
                'nama'            => '2027/2028',
                'tanggal_mulai'   => '2027-07-01',
                'tanggal_selesai' => '2028-06-30',
                'is_active'       => false,
                'is_arsip'        => false,
                'keterangan'      => 'Tahun ajaran mendatang',
            ],
        ];

        foreach ($data as $row) {
            TahunAjaran::firstOrCreate(
                ['nama' => $row['nama']],
                $row
            );
        }
    }
}