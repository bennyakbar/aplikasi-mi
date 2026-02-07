<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Assets (1xxx)
            ['code' => '1000', 'name' => 'Aset', 'type' => 'asset', 'parent_id' => null],
            ['code' => '1100', 'name' => 'Kas', 'type' => 'asset', 'parent_code' => '1000'],
            ['code' => '1110', 'name' => 'Kas Tunai', 'type' => 'asset', 'parent_code' => '1100'],
            ['code' => '1120', 'name' => 'Bank', 'type' => 'asset', 'parent_code' => '1100'],
            ['code' => '1200', 'name' => 'Piutang', 'type' => 'asset', 'parent_code' => '1000'],
            ['code' => '1210', 'name' => 'Piutang SPP', 'type' => 'asset', 'parent_code' => '1200'],

            // Liabilities (2xxx)
            ['code' => '2000', 'name' => 'Kewajiban', 'type' => 'liability', 'parent_id' => null],
            ['code' => '2100', 'name' => 'Hutang Jangka Pendek', 'type' => 'liability', 'parent_code' => '2000'],

            // Equity (3xxx)
            ['code' => '3000', 'name' => 'Modal', 'type' => 'equity', 'parent_id' => null],
            ['code' => '3100', 'name' => 'Modal Yayasan', 'type' => 'equity', 'parent_code' => '3000'],
            ['code' => '3200', 'name' => 'Laba Ditahan', 'type' => 'equity', 'parent_code' => '3000'],

            // Revenue (4xxx)
            ['code' => '4000', 'name' => 'Pendapatan', 'type' => 'revenue', 'parent_id' => null],
            ['code' => '4100', 'name' => 'Pendapatan SPP', 'type' => 'revenue', 'parent_code' => '4000'],
            ['code' => '4110', 'name' => 'SPP Reguler', 'type' => 'revenue', 'parent_code' => '4100'],
            ['code' => '4120', 'name' => 'SPP Subsidi', 'type' => 'revenue', 'parent_code' => '4100'],
            ['code' => '4130', 'name' => 'SPP Yatim/Piatu', 'type' => 'revenue', 'parent_code' => '4100'],
            ['code' => '4200', 'name' => 'Pendapatan Uang Gedung', 'type' => 'revenue', 'parent_code' => '4000'],
            ['code' => '4300', 'name' => 'Pendapatan Lain-lain', 'type' => 'revenue', 'parent_code' => '4000'],

            // Expenses (5xxx)
            ['code' => '5000', 'name' => 'Beban', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5100', 'name' => 'Beban Gaji', 'type' => 'expense', 'parent_code' => '5000'],
            ['code' => '5200', 'name' => 'Beban Operasional', 'type' => 'expense', 'parent_code' => '5000'],
            ['code' => '5210', 'name' => 'Beban Listrik', 'type' => 'expense', 'parent_code' => '5200'],
            ['code' => '5220', 'name' => 'Beban Air', 'type' => 'expense', 'parent_code' => '5200'],
            ['code' => '5230', 'name' => 'Beban ATK', 'type' => 'expense', 'parent_code' => '5200'],
            ['code' => '5300', 'name' => 'Beban Pemeliharaan', 'type' => 'expense', 'parent_code' => '5000'],
        ];

        foreach ($accounts as $data) {
            $parentId = null;
            if (isset($data['parent_code'])) {
                $parent = Account::where('code', $data['parent_code'])->first();
                $parentId = $parent?->id;
                unset($data['parent_code']);
            }

            Account::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'type' => $data['type'],
                'parent_id' => $parentId ?? ($data['parent_id'] ?? null),
                'is_active' => true,
            ]);
        }
    }
}
