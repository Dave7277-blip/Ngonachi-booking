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
            ['email' => 'janjarodavid@gmail.com'],
            [
                'name'     => 'David',
                'email'    => 'janjarodavid@gmail.com',
                'password' => Hash::make('mwalongo'),
                'role'     => 'admin',
            ]
        );

        // ── Packages ──────────────────────────────────────────
        $packages = [

            // ── WEDDING PACKAGES ──────────────────────────────

            [
                'name'                => 'Silver Wedding Package',
                'type'                => 'wedding',
                'price'               => 1200000.00,
                'currency'            => 'TZS',
                'description'         => 'Perfect for intimate ceremonies. Professional coverage of your most important moments.',
                'features'            => json_encode([
                    '6 hours of coverage',
                    '1 Professional Photographer',
                    '300+ edited digital images',
                    'Online gallery access',
                    'USB with all images',
                ]),
                'hours_coverage'      => 6,
                'photographers_count' => 1,
                'is_featured'         => false,
                'is_active'           => true,
                'sort_order'          => 1,
            ],

            [
                'name'                => 'Gold Wedding Package',
                'type'                => 'wedding',
                'price'               => 2500000.00,
                'currency'            => 'TZS',
                'description'         => 'Our most loved package. Complete wedding-day coverage with a dedicated team.',
                'features'            => json_encode([
                    '10 hours of coverage',
                    '2 Professional Photographers',
                    '600+ edited digital images',
                    '30-page luxury album',
                    'Engagement session included',
                    'Online gallery + USB',
                ]),
                'hours_coverage'      => 10,
                'photographers_count' => 2,
                'is_featured'         => true,
                'is_active'           => true,
                'sort_order'          => 2,
            ],

            [
                'name'                => 'Platinum Wedding Package',
                'type'                => 'wedding',
                'price'               => 4800000.00,
                'currency'            => 'TZS',
                'description'         => 'The ultimate luxury experience. Every moment, every emotion — flawlessly documented.',
                'features'            => json_encode([
                    'Full day (15 hours)',
                    '3 Photographers + Videographer',
                    '1000+ edited images',
                    '50-page heirloom album',
                    'Drone aerial coverage',
                    'Same-day highlight reel',
                ]),
                'hours_coverage'      => 15,
                'photographers_count' => 3,
                'is_featured'         => false,
                'is_active'           => true,
                'sort_order'          => 3,
            ],

            // ── SEND-OFF PACKAGES ─────────────────────────────

            [
                'name'                => 'Classic Send-off Package',
                'type'                => 'sendoff',
                'price'               => 800000.00,
                'currency'            => 'TZS',
                'description'         => 'Beautiful coverage of your send-off ceremony with vibrant, joyful imagery.',
                'features'            => json_encode([
                    '4 hours of coverage',
                    '1 Photographer',
                    '200+ edited images',
                    'Online gallery access',
                    '2 week delivery',
                ]),
                'hours_coverage'      => 4,
                'photographers_count' => 1,
                'is_featured'         => false,
                'is_active'           => true,
                'sort_order'          => 4,
            ],

            [
                'name'                => 'Premium Send-off Package',
                'type'                => 'sendoff',
                'price'               => 1600000.00,
                'currency'            => 'TZS',
                'description'         => 'Full send-off experience with extended coverage and a beautifully designed photo book.',
                'features'            => json_encode([
                    '8 hours of coverage',
                    '2 Photographers',
                    '400+ edited images',
                    '20-page photo book',
                    'Short highlight video',
                    '1 week express delivery',
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