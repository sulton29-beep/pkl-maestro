<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Sekolah',
                'email' => 'stafftatausahamst@gmail.com',
                'password' => 'adminsmk123',
                'roles' => ['admin'],
            ],
            [
                'name' => 'Kepala Sekolah',
                'email' => 'kepsekmst@gmail.com',
                'password' => 'kepsekmst321',
                'roles' => ['kepala_sekolah'],
            ],
            [
                'name' => 'Waka Kurikulum',
                'email' => 'wakakurikulummst@gmail.com',
                'password' => 'waka.kurikulummst321',
                'roles' => ['waka_kurikulum'],
            ],
            [
                'name' => 'Kaprodi TBSM',
                'email' => 'kaproditbsmmst@gmail.com',
                'password' => 'kaproditbsmmst321',
                'roles' => ['kaprodi'],
            ],
            [
                'name' => 'Kaprodi MPLB',
                'email' => 'kaprodimplbmst@gmail.com',
                'password' => 'kaprodi.mplbmst321',
                'roles' => ['kaprodi'],
            ],
            [
                'name' => 'Guru Pembimbing',
                'email' => 'gurupembimbingmst@gmail.com',
                'password' => 'guru.pembimbingmst321',
                'roles' => ['guru_pembimbing'],
            ],
            [
                'name' => 'Guru Penguji',
                'email' => 'gurupengujimst@gmail.com',
                'password' => 'guru.pengujimst321',
                'roles' => ['guru_penguji'],
            ],
            [
                'name' => 'Siswa Contoh',
                'email' => 'siswamst@gmail.com',
                'password' => 'siswamst321',
                'roles' => ['siswa'],
            ],
            [
                'name' => 'Pembimbing DUDI',
                'email' => 'dudimst5@gmail.com',
                'password' => 'dudimst5321',
                'roles' => ['dudi'],
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles($data['roles']);
        }
    }
}