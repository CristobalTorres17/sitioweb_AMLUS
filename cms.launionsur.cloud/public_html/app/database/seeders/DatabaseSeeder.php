<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Seed roles and a super admin first
        $this->call(RoleSeeder::class);
        $this->call(SuperAdminSeeder::class);

        // Seed root media folders used by the gallery and pickers
        $this->call(MediaCollectionSeeder::class);

        User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);

        // Seed initial categories and articles
        $this->call(ArticlesAndCategoriesSeeder::class);

        // Seed news/event categories (type='event')
        $this->call(EventCategoriesSeeder::class);

        // Seed service/procedure categories before the actual services
        $this->call(ServiceCategoriesSeeder::class);

        // Seed services, procedures and their relations (steps + requirements)
        $this->call(ServicesSeeder::class);

        // Seed the 8 districts of La Unión Sur
        $this->call(DistrictSeeder::class);
    }
}
