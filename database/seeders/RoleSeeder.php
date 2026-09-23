<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // === DAFTAR PERMISSION ===
        $permissions = [
            // User & Role Management (khusus admin)
            'view_any_user', 'create_user', 'update_user', 'delete_user',
            'view_any_role', 'create_role', 'update_role', 'delete_role',

            // Master Data
            'view_any_jurusan', 'create_jurusan', 'update_jurusan', 'delete_jurusan',
            'view_any_guru', 'create_guru', 'update_guru', 'delete_guru',
            'view_any_siswa', 'create_siswa', 'update_siswa', 'delete_siswa',
            'view_any_dudi', 'create_dudi', 'update_dudi', 'delete_dudi',

            // PKL
            'view_any_penempatan', 'create_penempatan', 'update_penempatan', 'delete_penempatan',
            'view_any_logbook', 'create_logbook', 'update_logbook', 'delete_logbook', 'verify_logbook',
            'view_absensi', 'create_absensi',
            'view_nilai', 'input_nilai_dudi', 'input_nilai_sekolah', 'input_nilai_sidang',
            'view_sertifikat', 'generate_sertifikat',
            'view_laporan', 'approve_laporan',
            'view_audit_trail',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // === DAFTAR ROLE + PERMISSION ===
        $roles = [
            'admin' => $permissions, // admin punya semua

            'kepala_sekolah' => [
                'view_laporan', 'approve_laporan', 'view_audit_trail',
                'view_any_siswa', 'view_any_guru', 'view_any_dudi',
                'view_any_penempatan', 'view_nilai', 'view_sertifikat',
            ],

            'waka_kurikulum' => [
                'view_any_jurusan', 'view_any_siswa', 'view_any_guru',
                'view_any_penempatan', 'view_nilai', 'view_laporan',
                'view_sertifikat', 'generate_sertifikat', 'approve_laporan',
            ],

            'kaprodi' => [
                'view_any_siswa', 'create_siswa', 'update_siswa',
                'view_any_guru',
                'view_any_dudi', 'create_dudi', 'update_dudi',
                'view_any_penempatan', 'create_penempatan', 'update_penempatan',
                'view_logbook', 'verify_logbook',
                'view_nilai', 'view_laporan', 'view_sertifikat',
            ],

            'guru_pembimbing' => [
                'view_any_siswa', 'view_any_dudi',
                'view_any_penempatan',
                'view_any_logbook', 'verify_logbook',
                'view_absensi',
                'view_nilai', 'input_nilai_sekolah',
                'view_laporan',
            ],

            'guru_penguji' => [
                'view_any_siswa', 'view_any_penempatan',
                'view_nilai', 'input_nilai_sidang',
            ],

            'siswa' => [
                'view_any_logbook', 'create_logbook', 'update_logbook',
                'view_absensi', 'create_absensi',
                'view_nilai', 'view_sertifikat',
            ],

            'dudi' => [
                'view_any_siswa', 'view_any_penempatan',
                'view_any_logbook', 'verify_logbook',
                'view_absensi',
                'view_nilai', 'input_nilai_dudi',
                'view_sertifikat',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}