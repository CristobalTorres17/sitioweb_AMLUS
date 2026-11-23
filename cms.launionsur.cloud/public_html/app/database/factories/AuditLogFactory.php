<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = ['create', 'update', 'delete', 'restore'];
        $action = $this->faker->randomElement($actions);

        // Determine old/new values based on action
        $oldValues = [];
        $newValues = [];

        if ($action === 'create') {
            $newValues = $this->generateModelData();
        } elseif ($action === 'delete') {
            $oldValues = $this->generateModelData();
        } elseif (in_array($action, ['update', 'restore'])) {
            $oldValues = $this->generateModelData();
            $newValues = $this->generateModelData();
        }

        return [
            'model_type' => $this->faker->randomElement([
                'App\\Models\\User',
                'App\\Models\\Role',
                'App\\Models\\Page',
                'App\\Models\\Post',
            ]),
            'model_id' => $this->faker->numberBetween(1, 1000),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => User::inRandomOrder()->value('id') ?? User::withoutEvents(function () {
                return User::factory()->create()->id;
            }),
            'ip_address' => $this->faker->ipv4(),
            'is_sensitive' => false,
            'is_restoreable' => in_array($action, ['create', 'update', 'delete']),
            'reason' => null,
        ];
    }

    /**
     * Indicate that the audit log is for a sensitive change.
     *
     * @return static
     */
    public function sensitive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_sensitive' => true,
                'reason' => 'Sensitive field change detected',
            ];
        });
    }

    /**
     * Indicate that the audit log is restor able.
     *
     * @return static
     */
    public function restoreable(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_restoreable' => true,
            ];
        });
    }

    /**
     * Indicate that the audit log is NOT restorable.
     *
     * @return static
     */
    public function notRestoreable(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_restoreable' => false,
            ];
        });
    }

    /**
     * Set the action to 'create'.
     *
     * @return static
     */
    public function created(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'action' => 'create',
                'old_values' => [],
                'new_values' => $this->generateModelData(),
            ];
        });
    }

    /**
     * Set the action to 'update'.
     *
     * @return static
     */
    public function updated(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'action' => 'update',
                'old_values' => $this->generateModelData(),
                'new_values' => $this->generateModelData(),
            ];
        });
    }

    /**
     * Set the action to 'delete'.
     *
     * @return static
     */
    public function deleted(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'action' => 'delete',
                'old_values' => $this->generateModelData(),
                'new_values' => [],
            ];
        });
    }

    /**
     * Set the action to 'restore'.
     *
     * @return static
     */
    public function restored(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'action' => 'restore',
                'old_values' => [],
                'new_values' => $this->generateModelData(),
            ];
        });
    }

    /**
     * Set a specific model type.
     *
     * @param string $modelType
     * @return static
     */
    public function forModel(string $modelType): static
    {
        return $this->state(function (array $attributes) use ($modelType) {
            return [
                'model_type' => $modelType,
            ];
        });
    }

    /**
     * Set a specific model ID.
     *
     * @param int $modelId
     * @return static
     */
    public function forModelId(int $modelId): static
    {
        return $this->state(function (array $attributes) use ($modelId) {
            return [
                'model_id' => $modelId,
            ];
        });
    }

    /**
     * Set a specific user.
     *
     * @param User $user
     * @return static
     */
    public function forUser(User $user): static
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }

    /**
     * Generate realistic model data for old/new values.
     *
     * @return array
     */
    protected function generateModelData(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'status' => $this->faker->randomElement(['active', 'inactive', 'pending']),
            'description' => $this->faker->sentence(),
        ];
    }
}
