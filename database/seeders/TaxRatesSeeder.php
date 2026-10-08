<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class TaxRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaxRate::firstOrCreate(
            ['name' => 'PPN 11% (Standar Nasional)'],
            [
                'rate_percent' => 11.00,
                'effective_from' => '2024-01-01',
                'is_active' => true,
            ]
        );

        TaxRate::firstOrCreate(
            ['name' => 'PPN 12% (Rencana Penyesuaian)'],
            [
                'rate_percent' => 12.00,
                'effective_from' => '2027-01-01',
                'is_active' => false,
            ]
        );
    }
}
