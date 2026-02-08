<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates test users for each role defined in the system.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'System Admin',
                'email' => 'admin@tusd.test',
                'password' => Hash::make('password'),
                'role' => 'system_admin',
            ],
            [
                'name' => 'Bendahara TU',
                'email' => 'bendahara@tusd.test',
                'password' => Hash::make('password'),
                'role' => 'bendahara',
            ],
            [
                'name' => 'Petugas Transaksi',
                'email' => 'petugas@tusd.test',
                'password' => Hash::make('password'),
                'role' => 'petugas_transaksi',
            ],
            [
                'name' => 'Admin Master Data',
                'email' => 'masterdata@tusd.test',
                'password' => Hash::make('password'),
                'role' => 'admin_master_data',
            ],
            [
                'name' => 'Ketua Yayasan',
                'email' => 'yayasan@tusd.test',
                'password' => Hash::make('password'),
                'role' => 'yayasan',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Assign role if not already assigned
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }
        }

        $this->command->info('Test users created successfully!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['System Admin', 'admin@tusd.test', 'password'],
                ['Bendahara', 'bendahara@tusd.test', 'password'],
                ['Petugas Transaksi', 'petugas@tusd.test', 'password'],
                ['Admin Master Data', 'masterdata@tusd.test', 'password'],
                ['Yayasan', 'yayasan@tusd.test', 'password'],
            ]
        );
    }
}
