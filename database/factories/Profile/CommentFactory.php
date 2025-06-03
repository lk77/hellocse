<?php

namespace Database\Factories\Profile;

use App\Models\Profile\Profile;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content'    => $this->faker->text(500),
            'profile_id' => Profile::factory(),
            'user_id'    => User::factory(),
        ];
    }
}
