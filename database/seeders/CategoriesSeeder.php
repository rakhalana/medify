<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Diagnostik & Monitoring',
                'description' => 'Alat pemantau kesehatan seperti tensimeter digital, termometer inframerah, pulse oximeter, dan tes glukosa.',
            ],
            [
                'name' => 'Alat Bantu Mobilitas',
                'description' => 'Peralatan penunjang mobilitas fisik seperti kursi roda manual/elektrik, kruk, tongkat jalan, dan walker.',
            ],
            [
                'name' => 'Terapi & Pernapasan',
                'description' => 'Perangkat pendukung pernapasan seperti nebulizer kompresor, konsentrator oksigen, dan suction apparatus.',
            ],
            [
                'name' => 'Sterilisasi & Sanitasi Medis',
                'description' => 'Peralatan sterilisasi instrumen seperti autoclave, lampu UV germisidal, dan desinfektan standar medis.',
            ],
            [
                'name' => 'Pertolongan Pertama (P3K)',
                'description' => 'Kotak P3K standar industri, perban elastis, plester steril, kain kassa, dan antiseptik darurat.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                [
                    'slug' => Str::slug($cat['name']),
                    'description' => $cat['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
