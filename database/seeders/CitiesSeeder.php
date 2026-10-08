<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            'Jakarta',
            'Surabaya',
            'Bandung',
            'Semarang',
            'Medan',
            'Makassar',
            'Yogyakarta',
            'Denpasar',
            'Palembang',
            'Malang',
            'Surakarta',
            'Tangerang',
            'Bekasi',
            'Depok',
            'Bogor',
        ];

        foreach ($cities as $cityName) {
            City::firstOrCreate(
                ['name' => $cityName],
                ['is_active' => true]
            );
        }
    }
}
