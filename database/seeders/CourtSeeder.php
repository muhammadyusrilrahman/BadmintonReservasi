<?php

namespace Database\Seeders;

use App\Models\Court;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    public function run(): void
    {
        $courts = [
            [
                'name'           => 'Lapangan A',
                'type'           => 'synthetic',
                'description'    => 'Lapangan sintetis premium dengan pencahayaan LED full. Cocok untuk latihan dan turnamen.',
                'price_per_hour' => 70000,
                'is_active'      => true,
            ],
            [
                'name'           => 'Lapangan B',
                'type'           => 'synthetic',
                'description'    => 'Lapangan sintetis standar dengan pencahayaan yang baik dan sirkulasi udara optimal.',
                'price_per_hour' => 60000,
                'is_active'      => true,
            ],
            [
                'name'           => 'Lapangan C',
                'type'           => 'rubber',
                'description'    => 'Lapangan karet anti-slip yang nyaman digunakan. Cocok untuk semua level pemain.',
                'price_per_hour' => 50000,
                'is_active'      => true,
            ],
            [
                'name'           => 'Lapangan D',
                'type'           => 'wood',
                'description'    => 'Lapangan kayu klasik berstandar internasional. Khusus untuk kompetisi dan pemain profesional.',
                'price_per_hour' => 85000,
                'is_active'      => false,
            ],
        ];

        foreach ($courts as $courtData) {
            Court::firstOrCreate(['name' => $courtData['name']], $courtData);
        }
    }
}
