<?php

namespace Database\Factories;

use App\Enums\ItemStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['name' => "string", 'description' => "string", 'completed' => "bool"])] public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'completed' => fake()->randomElement(ItemStatusEnum::cases())->value,
        ];
    }
}
