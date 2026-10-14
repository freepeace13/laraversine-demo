<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Fictional places scattered around Davao City.
        $places = [
            ['Sunrise Coffee', 'cafe', 7.1281, 125.6322],
            ['Harbor Roasters', 'cafe', 7.0911, 125.6125],
            ['Bean There', 'cafe', 7.0731, 125.6128],
            ['Highland Brew', 'cafe', 7.1902, 125.4550],
            ['Fresh Mart', 'grocery', 7.1263, 125.6391],
            ['Daily Basket', 'grocery', 7.0644, 125.5964],
            ['Green Valley Market', 'grocery', 7.1511, 125.5530],
            ['Quick Stop', 'grocery', 7.1090, 125.6410],
            ['City Pharmacy', 'pharmacy', 7.1302, 125.6355],
            ['HealthPlus', 'pharmacy', 7.0560, 125.5460],
            ['Wellness Corner', 'pharmacy', 7.1655, 125.6590],
            ['Riverside Gym', 'gym', 7.1198, 125.6483],
            ['Iron Works', 'gym', 7.0870, 125.6240],
            ['Peak Fitness', 'gym', 7.2001, 125.4630],
            ['Plaza Bookshop', 'books', 7.0690, 125.6090],
            ['Page Turner', 'books', 7.1340, 125.6290],
            ['Old Town Bakery', 'bakery', 7.1240, 125.6440],
            ['Golden Crust', 'bakery', 7.0990, 125.6150],
            ['Morning Loaf', 'bakery', 7.1450, 125.6120],
            ['Central Hardware', 'hardware', 7.0805, 125.6205],
            ['Builder\'s Depot', 'hardware', 7.1750, 125.5700],
            ['Night Owl Diner', 'diner', 7.1279, 125.6378],
            ['Seaside Grill', 'diner', 7.0540, 125.5800],
            ['Corner Kitchen', 'diner', 7.1160, 125.6290],
        ];

        foreach ($places as [$name, $category, $lat, $lng]) {
            Place::create(['name' => $name, 'category' => $category, 'latitude' => $lat, 'longitude' => $lng]);
        }
    }
}
