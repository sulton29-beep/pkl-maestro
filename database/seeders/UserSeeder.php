<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin utama
        $admin = User::updateOrCreate(
            ['email' => 'stafftatausahamst@gmail.com'],
            [
                'name' => 'Admin Sekolah',
                'password' => Hash::make('adminsmk123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);
    }
}