<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CitiesSeeder::class,
            CategoriesSeeder::class,
            TaxRatesSeeder::class,
            PaymentMethodsSeeder::class,
            UserSeeder::class,
            StoreAndProductSeeder::class,
        ]);

        // Default platform configuration settings
        Setting::set('session_timeout_minutes', '15', 'Batas waktu tidak aktif sesi pengguna sebelum auto-logout');
        Setting::set('prepaid_checkout_timeout_minutes', '30', 'Batas waktu penyelesaian pembayaran checkout Prepaid');
        Setting::set('chatbot_rate_per_minute', '20', 'Batas frekuensi pertanyaan chatbot AI per menit');
        Setting::set('chatbot_max_rows', '50', 'Batas maksimal baris hasil query SQL chatbot yang ditampilkan');
    }
}
