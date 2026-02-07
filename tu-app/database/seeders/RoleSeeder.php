<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Based on SRS v1.0 Section 1.3 - User Types
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'system_admin', 'guard_name' => 'web'],
            ['name' => 'admin_master_data', 'guard_name' => 'web'],
            ['name' => 'petugas_transaksi', 'guard_name' => 'web'],
            ['name' => 'bendahara', 'guard_name' => 'web'],
            ['name' => 'yayasan', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
