<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GrantSuperAdminToAdminEmailSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['display_name' => 'Super Admin', 'description' => 'Full access']
        );

        $user = User::where('email', 'admin@example.com')->first();

        if (! $user) {
            $this->command?->info('User admin@example.com not found.');
            return;
        }

        $user->role_id = $role->id;
        $user->is_active = $user->is_active ?? true;
        $user->save();

        $this->command?->info('Assigned role super_admin to admin@example.com');
    }
}
