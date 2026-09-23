<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        Jurusan::updateOrCreate(
            ['kode' => 'TBSM'],
            [
                'nama' => 'Teknik Sepeda Motor',
                'deskripsi' => 'Program keahlian teknik sepeda motor',
            ]
        );

        Jurusan::updateOrCreate(
            ['kode' => 'MPLB'],
            [
                'nama' => 'Manajemen Perkantoran dan Layanan Bisnis',
                'deskripsi' => 'Program keahlian administrasi perkantoran',
            ]
        );
    }
}