<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'admin',
            'kepala_sekolah',
            'waka_kurikulum',
            'kaprodi',
            'guru_pembimbing',
            'guru_penguji',
            'siswa',
            'pembimbing_dudi',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}