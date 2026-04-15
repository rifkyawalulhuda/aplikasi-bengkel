<?php

namespace Database\Seeders;

use App\Models\ServicePackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ServicePackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Paket A',
                'slug' => 'paket-a',
                'short_description' => 'Servis ringan untuk motor harian dengan fokus pemeriksaan dasar dan penggantian oli.',
                'description' => 'Paket entry-level untuk pengguna motor harian yang ingin servis ringan di rumah dengan estimasi biaya yang jelas.',
                'price' => 85000,
                'duration_estimate_minutes' => 60,
                'is_active' => true,
                'display_order' => 1,
                'items' => [
                    ['name' => 'Ganti oli mesin', 'description' => 'Penggantian oli mesin standar sesuai kebutuhan motor.', 'display_order' => 1],
                    ['name' => 'Cek busi', 'description' => 'Pemeriksaan kondisi busi dan pembersihan ringan.', 'display_order' => 2],
                    ['name' => 'Cek rem depan belakang', 'description' => 'Pemeriksaan fungsi dan ketebalan kampas rem secara visual.', 'display_order' => 3],
                    ['name' => 'Cek tekanan ban', 'description' => 'Pengecekan tekanan ban agar aman dipakai harian.', 'display_order' => 4],
                ],
            ],
            [
                'name' => 'Paket B',
                'slug' => 'paket-b',
                'short_description' => 'Perawatan berkala lebih lengkap untuk motor yang rutin dipakai kerja atau antar jemput.',
                'description' => 'Paket servis ringan yang lebih lengkap, cocok untuk maintenance bulanan dengan beberapa item pengecekan tambahan.',
                'price' => 145000,
                'duration_estimate_minutes' => 90,
                'is_active' => true,
                'display_order' => 2,
                'items' => [
                    ['name' => 'Ganti oli mesin', 'description' => 'Penggantian oli mesin dengan rekomendasi viskositas sesuai tipe motor.', 'display_order' => 1],
                    ['name' => 'Ganti filter udara', 'description' => 'Pembersihan atau penggantian filter udara bila diperlukan.', 'display_order' => 2],
                    ['name' => 'Cek busi dan pengapian', 'description' => 'Pemeriksaan busi dan gejala pengapian lemah.', 'display_order' => 3],
                    ['name' => 'Cek CVT atau rantai', 'description' => 'Pemeriksaan area penggerak sesuai jenis motor.', 'display_order' => 4],
                    ['name' => 'Cek rem dan throttle', 'description' => 'Pemeriksaan rem serta respon gas untuk pemakaian harian.', 'display_order' => 5],
                ],
            ],
        ];

        foreach ($packages as $packageData) {
            $package = ServicePackage::query()->updateOrCreate(
                ['slug' => $packageData['slug']],
                Arr::except($packageData, ['items']),
            );

            $package->items()->delete();
            $package->items()->createMany($packageData['items']);
        }
    }
}

