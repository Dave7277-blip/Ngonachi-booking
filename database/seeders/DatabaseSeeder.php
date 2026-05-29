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
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@lumiere.co.tz'],
            [
                'name'     => 'Kwame Oduya',
                'email'    => 'admin@lumiere.co.tz',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        // Create packages
        $packages = [
            [
                'name'               => 'Silver Wedding Package',
                'type'               => 'wedding',
                'price'              => 1200000.00,
                'currency'           => 'TZS',
                'description'        => 'Perfect for intimate ceremonies. Professional coverage of your most important moments.',
                'features'           => json_encode([
                    '6 hours of coverage',
                    '1 Professional Photographer',
                    '300+ edited digital images',
                    'Online gallery access',
                    'USB with all images',
                ]),
                'hours_coverage'     => 6,
                'photographers_count'=> 1,
                'is_featured'        => false,
                'is_active'          => true,
                'sort_order'         => 1,
            ],
            [
                'name'               => 'Gold Wedding Package',
                'type'               => 'wedding',
                'price'              => 2500000.00,
                'currency'           => 'TZS',
                'description'        => 'Our most loved package. Complete wedding-day coverage with a dedicated team.',
                'features'           => json_encode([
                    '10 hours of coverage',
                    '2 Professional Photographers',
                    '600+ edited digital images',
                    '30-page luxury album',
                    'Engagement session included',
                    'Online gallery + USB',
                ]),
                'hours_coverage'     => 10,
                'photographers_count'=> 2,
                'is_featured'        => true,
                'is_active'          => true,
                'sort_order'         => 2,
            ],
            [
                'name'               => 'Platinum Wedding Package',
                'type'               => 'wedding',
                'price'              => 4800000.00,
                'currency'           => 'TZS',
                'description'        => 'The ultimate luxury experience. Every moment, every emotion — flawlessly documented.',
                'features'           => json_encode([
                    'Full day (15 hours)',
                    '3 Photographers + Videographer',
                    '1000+ edited images',
                    '50-page heirloom album',
                    'Drone aerial coverage',
                    'Same-day highlight reel',
                ]),
                'hours_coverage'     => 15,
                'photographers_count'=> 3,
                'is_featured'        => false,
                'is_active'          => true,
                'sort_order'         => 3,
            ],
            [
                'name'               => 'Classic Send-off Package',
                'type'               => 'sendoff',
                'price'              => 800000.00,
                'currency'           => 'TZS',
                'description'        => 'Beautiful coverage of your send-off ceremony with vibrant, joyful imagery.',
                'features'           => json_encode([
                    '4 hours of coverage',
                    '1 Photographer',
                    '200+ edited images',
                    'Online gallery access',
                    '2 week delivery',
                ]),
                'hours_coverage'     => 4,
                'photographers_count'=> 1,
                'is_featured'        => false,
                'is_active'          => true,
                'sort_order'         => 4,
            ],
            [
                'name'               => 'Premium Send-off Package',
                'type'               => 'sendoff',
                'price'              => 1600000.00,
                'currency'           => 'TZS',
                'description'        => 'Full send-off experience with extended coverage and a beautifully designed photo book.',
                'features'           => json_encode([
                    '8 hours of coverage',
                    '2 Photographers',
                    '400+ edited images',
                    '20-page photo book',
                    'Short highlight video',
                    '1 week express delivery',
                ]),
                'hours_coverage'     => 8,
                'photographers_count'=> 2,
                'is_featured'        => true,
                'is_active'          => true,
                'sort_order'         => 5,
            ],
        ];

        foreach ($packages as $pkg) {
            Package::updateOrCreate(['name' => $pkg['name']], $pkg);
        }
    }
}