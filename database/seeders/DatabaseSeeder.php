<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Package;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin User ────────────────────────────────────────
        // Change email and password here to match your credentials
        User::updateOrCreate(
            ['email' => 'ngonachi62@gmail.com'],
            [
                'name'     => 'David',
                'email'    => 'ngonachi62@gmail.com',
                'password' => Hash::make('Ngonachi'),
                'role'     => 'admin',
            ]
        );

        // ── Packages ──────────────────────────────────────────
        $packages = [

            // ── WEDDING PACKAGES ──────────────────────────────

            [
                'name'                => 'Classical Elegance Package',
                'type'                => 'wedding',
                'price'               => 900000.00,
                'currency'            => 'TZS',
                'description'         => 'Simple. Beautiful. Memorable',
                'features'            => json_encode([
                    'Album [Picha 130]',
                    'Picha Mbao [1]',
                    'Flash drive [1]',
                    'Soft copy [20]',
                    'USB with all images',
                ]),
                'hours_coverage'      => 6,
                'photographers_count' => 1,
                'is_featured'         => false,
                'is_active'           => true,
                'sort_order'          => 1,
            ],

            [
                'name'                => 'Timeless Memories Package',
                'type'                => 'wedding',
                'price'               => 1600000.00,
                'currency'            => 'TZS',
                'description'         => 'Capturing Love without Limits',
                'features'            => json_encode([
                    'Photobook A3 [Picha 170]',
                    'Picha mbao A3 [1]',
                    'Picha mbao A4 [1]',
                    'Flat screen Live [3]',
                    'Full video DVD [2]',
                    'Full video Flash DISC [2]',
                    'Pre-wedding & send off [Drone Footage]',
                    'Photo Bunner',
                ]),
                'hours_coverage'      => 10,
                'photographers_count' => 2,
                'is_featured'         => true,
                'is_active'           => true,
                'sort_order'          => 2,
            ],

            [
                'name'                => 'Royal Celebration Package',
                'type'                => 'wedding',
                'price'               => 2300000.00,
                'currency'            => 'TZS',
                'description'         => 'The ultimate luxury experience. Every moment, every emotion — flawlessly documented.',
                'features'            => json_encode([
                    'Photobook [Picha 200]',
                    'Picha Mbao A3 [1]',
                    'Flat screen Live [4]',
                    'Highlight video [2 minutes]',
                    'Drone aerial coverage',
                    'Full video DVD [3]',
                    'Full video Flash DISC [3]',
                    'Pre-wedding & Send off',
                    'Soft Copy [50]',
                    'Photo Booth',
                ]),
                'hours_coverage'      => 15,
                'photographers_count' => 3,
                'is_featured'         => false,
                'is_active'           => true,
                'sort_order'          => 3,
            ],

            // ── SEND-OFF PACKAGES ─────────────────────────────

            [
                'name'                => 'Sweet Farewell Package',
                'type'                => 'sendoff',
                'price'               => 900000.00,
                'currency'            => 'TZS',
                'description'         => 'Celebrating the New Beginnings.',
                'features'            => json_encode([
                    'Album [Picha 130]',
                    'Picha Mbao [1]',
                    'Flash drive [1]',
                    'Soft copy [20]',
                    'USB with all images',
                ]),
                'hours_coverage'      => 4,
                'photographers_count' => 1,
                'is_featured'         => false,
                'is_active'           => true,
                'sort_order'          => 4,
            ],

            [
                'name'                => 'Golden Journey Package',
                'type'                => 'sendoff',
                'price'               => 1600000.00,
                'currency'            => 'TZS',
                'description'         => 'Moments Worth remembering',
                'features'            => json_encode([
                    'Photobook A3 [Picha 170]',
                    'Picha mbao A3 [1]',
                    'Picha mbao A4 [1]',
                    'Flat screen Live [3]',
                    'Full video DVD [2]',
                    'Full video Flash DISC [2]',
                    'Pre-wedding & send off [Drone Footage]',
                    'Photo Bunner',
                ]),
                'hours_coverage'      => 8,
                'photographers_count' => 2,
                'is_featured'         => false,
                'is_active'           => true,
                'sort_order'          => 5,
            ],

            [
                'name'                => 'Grand Departure Package',
                'type'                => 'sendoff',
                'price'               => 2300000.00,
                'currency'            => 'TZS',
                'description'         => 'The ultimate send-off experience. Every detail captured with cinematic precision — from pre-wedding to the grand farewell.',
                'features'            => json_encode([
                    'Photobook [Picha 200]',
                    'Picha Mbao A3 [1]',
                    'Flat screen Live [4]',
                    'Highlight video [2 minutes]',
                    'Drone aerial coverage',
                    'Full video DVD [3]',
                    'Full video Flash DISC [3]',
                    'Pre-wedding & Send off',
                    'Soft Copy [50]',
                    'Photo Booth',
                ]),
                'hours_coverage'      => 10,
                'photographers_count' => 2,
                'is_featured'         => false,
                'is_active'           => true,
                'sort_order'          => 6,
            ],

        ];

        foreach ($packages as $pkg) {
            Package::updateOrCreate(['name' => $pkg['name']], $pkg);
        }
    }
}