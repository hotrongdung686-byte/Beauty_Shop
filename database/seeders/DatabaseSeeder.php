<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * All child seeders use firstOrCreate/updateOrCreate keyed by a natural
     * unique column (email, slug, code...), so this is safe to run again on
     * the existing `beauty_shop` database without creating duplicates.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CatalogSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
