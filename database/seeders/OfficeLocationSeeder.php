<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'SMECLABS Head Office',
                'latitude' => 9.99419969778416,
                'longitude' => 76.2922585260837,
            ],
            [
                'name' => 'Thiruvananthapuram Branch',
                'latitude' => 8.488056172698194,
                'longitude' => 76.9514130802725,
            ],
            [
                'name' => 'Calicut Branch',
                'latitude' => 11.25965023562903,
                'longitude' => 75.79050033774399,
            ],
            [
                'name' => 'Nagarcoil Branch',
                'latitude' => 8.182829862445711,
                'longitude' => 77.43205205119453,
            ]
        ];

        foreach ($locations as $loc) {
            \App\Models\OfficeLocation::firstOrCreate(
                ['name' => $loc['name']],
                [
                    'latitude' => $loc['latitude'],
                    'longitude' => $loc['longitude'],
                    'radius' => 100,
                    'status' => true
                ]
            );
        }
    }
}
