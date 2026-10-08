<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentMethod::firstOrCreate(
            ['code' => 'prepaid_simulated'],
            [
                'name' => 'Simulasi Pembayaran (Prepaid)',
                'type' => 'prepaid',
                'same_city_only' => false,
                'is_active' => true,
                'instructions' => 'Simulasi pembayaran kartu kredit, transfer bank, atau dompet digital dengan batas waktu konfirmasi 30 menit.',
            ]
        );

        PaymentMethod::firstOrCreate(
            ['code' => 'postpaid_cod'],
            [
                'name' => 'Bayar di Tempat / COD (Postpaid)',
                'type' => 'postpaid',
                'same_city_only' => true,
                'is_active' => true,
                'instructions' => 'Bayar tunai langsung saat kurir mengantarkan barang. Hanya berlaku jika alamat pengiriman berada di kota yang sama dengan seluruh toko penjual.',
            ]
        );
    }
}
