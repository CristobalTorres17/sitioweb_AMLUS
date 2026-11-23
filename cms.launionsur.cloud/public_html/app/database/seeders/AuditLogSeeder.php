<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a user to associate with audit logs
        $user = User::first() ?? User::factory()->create();

        // Create audit logs for different scenarios

        // 1. Create action logs
        AuditLog::factory(5)
            ->created()
            ->forUser($user)
            ->create();

        // 2. Update action logs
        AuditLog::factory(10)
            ->updated()
            ->forUser($user)
            ->create();

        // 3. Delete action logs
        AuditLog::factory(3)
            ->deleted()
            ->forUser($user)
            ->create();

        // 4. Restore action logs
        AuditLog::factory(2)
            ->restored()
            ->forUser($user)
            ->create();

        // 5. Sensitive change logs
        AuditLog::factory(5)
            ->sensitive()
            ->updated()
            ->forUser($user)
            ->create();

        // 6. Non-restorable logs (creates don't restore)
        AuditLog::factory(3)
            ->created()
            ->notRestoreable()
            ->forUser($user)
            ->create();

        // 7. Mixed model types
        $modelTypes = [
            'App\\Models\\User',
            'App\\Models\\Role',
            'App\\Models\\Page',
            'App\\Models\\Post',
        ];

        foreach ($modelTypes as $modelType) {
            AuditLog::factory(4)
                ->forModel($modelType)
                ->forUser($user)
                ->create();
        }
    }
}
