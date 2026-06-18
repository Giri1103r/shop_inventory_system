<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),

            'role' => fake()->randomElement([
                '1',
                '2',
                '3'
            ]),

            'user_type' => fake()->randomElement([1, 2]),

            'employee_id' => 'EMP' . fake()->unique()->numberBetween(1000, 9999),

            'username' => fake()->unique()->userName(),

            'email_verified_at' => now(),

            'password' => Hash::make('123456'),

            'company_id' => 1,
            'location_id' => 1,
            'unit_id' => 1,
            'department_id' => 1,

            'designation_id' => fake()->randomElement([
               1,2
            ]),

            'mobile' => fake()->numerify('9#########'),

            'otp' => null,
            'otp_token' => null,

            'profile_image' => null,
            'signature_upload' => null,

            'permission' => json_encode([
                'dashboard',
                'users',
                'reports'
            ]),

            'created_by' => 1,
            'updated_by' => null,

            'status' => 1,
            'trash' => 'NO',

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
