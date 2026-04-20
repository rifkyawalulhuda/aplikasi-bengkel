<?php

return [
    'brand_name' => env('APP_NAME', 'ASM MOTOR'),
    'tagline' => 'Servis ringan motor panggilan untuk matic, bebek, dan sport.',
    'contact_phone' => env('WORKSHOP_CONTACT_PHONE', env('WORKSHOP_CONTACT_WHATSAPP', '')),
    'contact_whatsapp' => env('WORKSHOP_CONTACT_WHATSAPP', env('WORKSHOP_CONTACT_PHONE', '')),
    'footer_location' => [
        'address' => env(
            'WORKSHOP_FOOTER_ADDRESS',
            'Jl. Badami Ciherang, Telukjambe Barat, Kab. Karawang',
        ),
        'latitude' => env('WORKSHOP_FOOTER_LATITUDE', '-6.3025000'),
        'longitude' => env('WORKSHOP_FOOTER_LONGITUDE', '107.3035000'),
    ],
    'transport_charge' => [
        'free_radius_km' => env('WORKSHOP_TRANSPORT_FREE_RADIUS_KM', '10'),
        'fee_per_km' => env('WORKSHOP_TRANSPORT_FEE_PER_KM', '5000'),
    ],
    'service_areas' => [
        'Karawang bagian barat',
        'karawang bagian timur',
        'Karawang Kota',
        'Dan Sekitarnya yang masih masuk dalam radius operasional',
    ],
    'coverage' => [
        'bounding_box' => [
            'min_latitude' => -6.75,
            'max_latitude' => -6.05,
            'min_longitude' => 106.55,
            'max_longitude' => 107.05,
        ],
    ],
    'landing' => [
        'seo' => [
            'title' => 'Servis Motor ke Rumah dengan Harga Transparan',
            'description' => 'Booking servis motor home service untuk ganti oli, tune up ringan, cek busi, dan perawatan berkala tanpa perlu ke bengkel.',
            'keywords' => [
                'servis motor ke rumah',
                'home service motor',
                'bengkel panggilan motor',
                'ganti oli motor di rumah',
                'servis motor bogor',
            ],
        ],
        'highlights' => [
            [
                'title' => 'SERVIS RUTIN',
                'description' => 'Pengecekan 25 titik vital kendaraan untuk memastikan performa tetap optimal dan aman digunakan.',
            ],
            [
                'title' => 'GANTI OLI',
                'description' => 'Layanan ganti oli cepat dengan berbagai pilihan brand oli premium sesuai spesifikasi motor Anda.',
            ],
            [
                'title' => 'PERBAIKAN DARURAT',
                'description' => 'Ban bocor? Mesin mati mendadak? Tim respons cepat kami siap meluncur ke lokasi Anda kapan saja.',
            ],
        ],
        'how_it_works' => [
            [
                'title' => 'Pilih paket yang sesuai',
                'description' => 'Pelanggan bisa mulai dari paket servis tetap atau nanti menyusun kebutuhan custom sesuai kondisi motor.',
            ],
            [
                'title' => 'Tentukan lokasi dan jadwal',
                'description' => 'Alamat, patokan rumah, koordinat, tanggal, dan jam servis menjadi dasar validasi slot dan coverage area di backend.',
            ],
            [
                'title' => 'Admin konfirmasi booking',
                'description' => 'Booking yang masuk akan diverifikasi admin, terutama jika perlu review manual untuk lokasi atau detail servis tertentu.',
            ],
        ],
        'coverage' => [
            'title' => 'Area layanan awal untuk home service',
            'description' => 'Layanan difokuskan ke area yang masih efisien untuk operasional mekanik keliling agar waktu tempuh dan jadwal tetap realistis.',
            'note' => 'Jika titik lokasi berada di luar coverage otomatis, booking tetap bisa masuk namun akan ditandai untuk review manual oleh admin.',
        ],
        'faqs' => [
            [
                'question' => 'Apakah semua kerusakan motor bisa dikerjakan di rumah?',
                'answer' => 'Tidak. Layanan ini diprioritaskan untuk servis ringan dan perawatan berkala yang aman dikerjakan di lokasi pelanggan tanpa alat bengkel besar.',
            ],
            [
                'question' => 'Apakah alamat tetap perlu ditulis walau sudah pin lokasi?',
                'answer' => 'Ya. Alamat lengkap dan patokan rumah tetap penting supaya mekanik lebih cepat menemukan lokasi dan admin lebih mudah memverifikasi area layanan.',
            ],
            [
                'question' => 'Bagaimana jika lokasi saya di luar coverage?',
                'answer' => 'Booking tetap bisa diterima oleh sistem, tetapi statusnya akan masuk review manual sebelum admin memberikan konfirmasi final.',
            ],
            [
                'question' => 'Apakah harga akhir selalu sama dengan harga awal di landing page?',
                'answer' => 'Harga paket aktif di landing page menjadi acuan awal. Untuk paket custom atau kondisi khusus, detail item dan snapshot harga akan disimpan saat booking dibuat.',
            ],
        ],
        'testimonials' => [
            [
                'name' => 'Rizky',
                'vehicle' => 'Honda Beat 2022',
                'quote' => 'Yang paling saya cari itu praktis. Tidak perlu antre ke bengkel dan saya sudah punya gambaran harga dari awal.',
            ],
            [
                'name' => 'Sinta',
                'vehicle' => 'Yamaha NMAX',
                'quote' => 'Model home service seperti ini cocok buat servis rutin bulanan karena jadwal saya padat dan motornya dipakai harian.',
            ],
            [
                'name' => 'Fajar',
                'vehicle' => 'Suzuki Address',
                'quote' => 'Saya suka kalau item servisnya dijelaskan jelas. Jadi lebih tenang sebelum booking dan lebih enak diskusi dengan admin.',
            ],
        ],
        'cta' => [
            'title' => 'Siap booking servis motor tanpa antre bengkel?',
            'description' => 'Landing page ini sudah membaca paket aktif dari database. Form booking penuh akan menjadi langkah berikutnya, tetapi CTA dan struktur data publiknya sudah siap.',
            'points' => [
                'Paket aktif ditarik langsung dari database',
                'Mendukung fixed package dan custom package',
                'Siap lanjut ke form booking di langkah berikutnya',
            ],
        ],
    ],
];
