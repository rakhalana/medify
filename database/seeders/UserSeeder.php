<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jakarta = City::where('name', 'Jakarta')->first();
        $surabaya = City::where('name', 'Surabaya')->first();

        // 1. Master Administrator
        $admin = User::firstOrNew(['email' => 'admin@medify.test']);
        $admin->forceFill([
            'name' => 'Administrator Medify',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'city_id' => $jakarta?->id,
            'address' => 'Gedung Medify Tower Lt. 12, Jl. Sudirman Kav. 50, Jakarta Pusat',
            'phone' => '081234567890',
            'is_active' => true,
            'email_verified_at' => now(),
            'consent_at' => now(),
            'consent_version' => '1.0',
        ])->save();

        // 2. Demo Vendor Toko
        $vendor = User::firstOrNew(['email' => 'vendor@medify.test']);
        $vendor->forceFill([
            'name' => 'Dr. Hendra (Alkes Sehat Abadi)',
            'password' => Hash::make('password'),
            'role' => UserRole::VENDOR,
            'city_id' => $surabaya?->id,
            'address' => 'Ruko Dharmahusada Indah No. 45, Gubeng, Surabaya',
            'phone' => '081298765432',
            'paypal_id' => 'hendra-alkes@paypal.test',
            'is_active' => true,
            'email_verified_at' => now(),
            'consent_at' => now(),
            'consent_version' => '1.0',
        ])->save();

        // 3. Demo Customer
        $customer = User::firstOrNew(['email' => 'customer@medify.test']);
        $customer->forceFill([
            'name' => 'Siti Rahmawati',
            'password' => Hash::make('password'),
            'role' => UserRole::CUSTOMER,
            'city_id' => $surabaya?->id,
            'address' => 'Jl. Manyar Kertoarjo No. 18, Mulyorejo, Surabaya',
            'phone' => '081377889900',
            'paypal_id' => 'siti.rahma@paypal.test',
            'is_active' => true,
            'email_verified_at' => now(),
            'consent_at' => now(),
            'consent_version' => '1.0',
        ])->save();
    }
}
