<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;

class UserProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Create a basic profile for each existing user that doesn't have one.
     *
     * @return void
     */
    public function run()
    {
        User::whereDoesntHave('profile')
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    UserProfile::create([
                        'user_id' => $user->id,
                        'phone' => null,
                        'mobile' => null,
                        'bio' => null,
                        'position' => 'Editor',
                        'department' => 'General',
                    ]);
                }
            });
    }
}
