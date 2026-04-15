<?php

namespace Database\Seeders;

use App\Models\CustomServiceItem;
use Illuminate\Database\Seeder;

class CustomServiceItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Ganti oli',
                'slug' => 'ganti-oli',
                'category' => 'Perawatan rutin',
                'description' => 'Penggantian oli mesin untuk menjaga performa motor harian.',
                'price' => 45000,
                'unit_label' => 'layanan',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Ganti filter oli',
                'slug' => 'ganti-filter-oli',
                'category' => 'Perawatan rutin',
                'description' => 'Penggantian filter oli pada motor yang menggunakan filter terpisah.',
                'price' => 30000,
                'unit_label' => 'layanan',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Ganti filter udara',
                'slug' => 'ganti-filter-udara',
                'category' => 'Perawatan rutin',
                'description' => 'Pembersihan atau penggantian filter udara untuk menjaga suplai udara pembakaran.',
                'price' => 35000,
                'unit_label' => 'layanan',
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'name' => 'Cek busi',
                'slug' => 'cek-busi',
                'category' => 'Pemeriksaan',
                'description' => 'Pemeriksaan kondisi busi, pembersihan ringan, dan rekomendasi penggantian bila perlu.',
                'price' => 20000,
                'unit_label' => 'layanan',
                'is_active' => true,
                'display_order' => 4,
            ],
            [
                'name' => 'Maintenance bulanan',
                'slug' => 'maintenance-bulanan',
                'category' => 'Paket custom',
                'description' => 'Pemeriksaan ringan bulanan meliputi oli, rem, tekanan ban, dan kondisi umum motor.',
                'price' => 95000,
                'unit_label' => 'kunjungan',
                'is_active' => true,
                'display_order' => 5,
            ],
        ];

        foreach ($items as $item) {
            CustomServiceItem::query()->updateOrCreate(
                ['slug' => $item['slug']],
                $item,
            );
        }
    }
}
