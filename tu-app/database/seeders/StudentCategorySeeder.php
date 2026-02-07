<?php

namespace Database\Seeders;

use App\Models\StudentCategory;
use Illuminate\Database\Seeder;

class StudentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Reguler',
                'code' => 'REG',
                'description' => 'Siswa dengan biaya penuh',
                'is_active' => true,
            ],
            [
                'name' => 'Subsidi',
                'code' => 'SUBSIDI',
                'description' => 'Siswa dengan biaya bersubsidi',
                'is_active' => true,
            ],
            [
                'name' => 'Yatim/Piatu',
                'code' => 'YTM',
                'description' => 'Siswa yatim atau piatu',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            StudentCategory::firstOrCreate(['code' => $category['code']], $category);
        }
    }
}
