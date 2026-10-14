<?php

namespace Database\Seeders;

use App\Enums\StoreStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreAndProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $surabaya = City::where('name', 'Surabaya')->first() ?? City::first();
        $jakarta = City::where('name', 'Jakarta')->first() ?? City::first();

        // 1. Setup Vendor 1: Dr. Hendra (Alkes Sehat Abadi - Surabaya)
        $vendor1 = User::firstOrCreate(
            ['email' => 'vendor@medify.test'],
            [
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
            ]
        );

        $store1 = Store::firstOrCreate(
            ['slug' => 'alkes-sehat-abadi'],
            [
                'owner_user_id' => $vendor1->id,
                'city_id' => $surabaya?->id,
                'name' => 'Alkes Sehat Abadi',
                'description' => 'Pusat distributor alat kesehatan resmi, perlengkapan medis klinik, rumah sakit, dan perawatan mandiri di Surabaya.',
                'status' => StoreStatus::ACTIVE,
                'logo_path' => 'stores/logos/alkes-sehat-abadi.png',
                'signature_path' => 'stores/signatures/alkes-sehat-abadi.png',
            ]
        );

        // 2. Setup Vendor 2 (Multi-vendor testing): Medika Utama Mandiri - Jakarta
        $vendor2 = User::firstOrCreate(
            ['email' => 'vendor2@medify.test'],
            [
                'name' => 'Budi Santoso (Medika Utama)',
                'password' => Hash::make('password'),
                'role' => UserRole::VENDOR,
                'city_id' => $jakarta?->id,
                'address' => 'Jl. Salemba Raya No. 24, Senen, Jakarta Pusat',
                'phone' => '081122334455',
                'paypal_id' => 'budi.medika@paypal.test',
                'is_active' => true,
                'email_verified_at' => now(),
                'consent_at' => now(),
                'consent_version' => '1.0',
            ]
        );

        $store2 = Store::firstOrCreate(
            ['slug' => 'medika-utama-mandiri'],
            [
                'owner_user_id' => $vendor2->id,
                'city_id' => $jakarta?->id,
                'name' => 'Medika Utama Mandiri',
                'description' => 'Supplier perlengkapan sterilisasi dan diagnostik medis terstandar internasional di Jakarta.',
                'status' => StoreStatus::ACTIVE,
                'logo_path' => 'stores/logos/medika-utama.png',
                'signature_path' => 'stores/signatures/medika-utama.png',
            ]
        );

        // Categories
        $catDiagnostik = Category::where('slug', 'diagnostik-monitoring')->first();
        $catMobilitas = Category::where('slug', 'alat-bantu-mobilitas')->first();
        $catTerapi = Category::where('slug', 'terapi-pernapasan')->first();
        $catSterilisasi = Category::where('slug', 'sterilisasi-sanitasi-medis')->first();
        $catP3k = Category::where('slug', 'pertolongan-pertama-p3k')->first();

        $diagId = $catDiagnostik ? $catDiagnostik->id : 1;
        $mobId = $catMobilitas ? $catMobilitas->id : 2;
        $terapiId = $catTerapi ? $catTerapi->id : 3;
        $sterilId = $catSterilisasi ? $catSterilisasi->id : 4;
        $p3kId = $catP3k ? $catP3k->id : 5;

        // Sample products
        $productsData = [
            // Toko 1 (Surabaya)
            [
                'store_id' => $store1->id,
                'category_id' => $diagId,
                'code' => 'MED-TNS-012',
                'name' => 'Tensimeter Digital DrPro-012 Akurat Arm Type',
                'slug' => 'tensimeter-digital-drpro-012-akurat-arm-type',
                'description' => 'Tensimeter digital lengan atas dengan layar LCD besar, memori 2x90 pengukuran, deteksi aritmia (detak jantung tidak teratur), dan manset ukuran 22-42cm. Izin Edar Kemenkes RI AKL 20501020304.',
                'price' => 385000.00,
                'stock' => 45,
                'image' => 'products/tensimeter-digital.jpg',
            ],
            [
                'store_id' => $store1->id,
                'category_id' => $diagId,
                'code' => 'MED-OXI-005',
                'name' => 'Fingertip Pulse Oximeter OxyCare OLED Display',
                'slug' => 'fingertip-pulse-oximeter-oxycare-oled-display',
                'description' => 'Alat pengukur saturasi oksigen (SpO2) dan detak nadi jari dengan layar dual color OLED. Auto-shutdown setelah 8 detik tidak digunakan. Akurasi medis ±2%.',
                'price' => 125000.00,
                'stock' => 80,
                'image' => 'products/pulse-oximeter.jpg',
            ],
            [
                'store_id' => $store1->id,
                'category_id' => $diagId,
                'code' => 'MED-TRM-008',
                'name' => 'Termometer Inframerah Non-Contact ThermoScan Pro',
                'slug' => 'termometer-inframerah-non-contact-thermoscan-pro',
                'description' => 'Termometer dahi tanpa kontak dengan sensor inframerah presisi tinggi. Respon cepat 1 detik, indikator alarm demam tiga warna, dan memori 32 pembacaan.',
                'price' => 195000.00,
                'stock' => 60,
                'image' => 'products/termometer-inframerah.jpg',
            ],
            [
                'store_id' => $store1->id,
                'category_id' => $mobId,
                'code' => 'MED-KRS-001',
                'name' => 'Kursi Roda Manual Lipat ErgoGlide Standard Steel',
                'slug' => 'kursi-roda-manual-lipat-ergoglide-standard-steel',
                'description' => 'Kursi roda lipat rangka baja dilapisi krom anti karat. Beban maksimal 100 kg, rem ganda (pengguna dan pendorong), ban mati tidak perlu pompa, dan pijakan kaki yang dapat dilipat.',
                'price' => 1450000.00,
                'stock' => 15,
                'image' => 'products/kursi-roda-lipat.jpg',
            ],
            [
                'store_id' => $store1->id,
                'category_id' => $terapiId,
                'code' => 'MED-NEB-002',
                'name' => 'Nebulizer Kompresor RespiBreeze Silent Inhaler',
                'slug' => 'nebulizer-kompresor-respibreeze-silent-inhaler',
                'description' => 'Perangkat inhalasi uap untuk terapi asma dan saluran pernapasan anak maupun dewasa. Partikel halus MMAD 3.0 µm, tingkat kebisingan rendah < 55 dB, dilengkapi masker anak dan dewasa.',
                'price' => 420000.00,
                'stock' => 25,
                'image' => 'products/nebulizer-kompresor.jpg',
            ],
            [
                'store_id' => $store1->id,
                'category_id' => $p3kId,
                'code' => 'MED-P3K-010',
                'name' => 'Kotak P3K Dinding Metal Box Lengkap Standar K3',
                'slug' => 'kotak-p3k-dinding-metal-box-lengkap-standar-k3',
                'description' => 'Kotak pertolongan pertama pada kecelakaan (P3K) dinding dengan 21 item medis sesuai standar Kemenaker RI. Termasuk perban elastis, kain kassa steril, betadine, plester, dan gunting medis.',
                'price' => 210000.00,
                'stock' => 40,
                'image' => 'products/kotak-p3k.jpg',
            ],

            // Toko 2 (Jakarta)
            [
                'store_id' => $store2->id,
                'category_id' => $mobId,
                'code' => 'MED-TKT-003',
                'name' => 'Tongkat Jalan Kaki Empat (Quad Cane) Aluminium Ringan',
                'slug' => 'tongkat-jalan-kaki-empat-quad-cane-aluminium-ringan',
                'description' => 'Tongkat penyangga tubuh dengan 4 tumpuan karet anti-slip untuk stabilitas ekstra bagi lansia atau pemulihan stroke. Ketinggian dapat diatur 10 tingkat.',
                'price' => 135000.00,
                'stock' => 35,
                'image' => 'products/tongkat-kaki-empat.jpg',
            ],
            [
                'store_id' => $store2->id,
                'category_id' => $sterilId,
                'code' => 'MED-ATC-007',
                'name' => 'Autoclave Portable Steam Sterilizer Medis 18 Liter',
                'slug' => 'autoclave-portable-steam-sterilizer-medis-18-liter',
                'description' => 'Alat sterilisasi uap bertekanan tinggi untuk instrumen bedah, klinik gigi, dan laboratorium. Bahan stainless steel tahan korosi dengan katup pengaman otomatis.',
                'price' => 3750000.00,
                'stock' => 8,
                'image' => 'products/autoclave-portable.jpg',
            ],
        ];

        foreach ($productsData as $data) {
            $imagePath = $data['image'];
            unset($data['image']);

            $product = Product::firstOrCreate(
                ['code' => $data['code']],
                $data
            );

            // Add primary image if none exists
            if ($product->images()->count() === 0) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $imagePath,
                    'thumb_path' => str_replace('products/', 'products/thumbs/', $imagePath),
                    'sort_order' => 0,
                ]);
            }
        }
    }
}
