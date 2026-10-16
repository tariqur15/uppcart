<?php

namespace Database\Factories;

use App\Models\FlashSale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FlashSale>
 */
class FlashSaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true).' Flash Sale',
            'description' => $this->faker->sentence(),
            'status' => FlashSale::STATUS_DRAFT,
            'created_by' => User::factory(),
        ];
    }

    public function scheduled(?string $start = null, ?string $end = null): static
    {
        return $this->state(fn () => [
            'starts_at' => $start ?? now()->subHour(),
            'ends_at' => $end ?? now()->addDay(),
            'status' => FlashSale::STATUS_SCHEDULED,
        ]);
    }

    public function active(?string $start = null, ?string $end = null): static
    {
        return $this->state(fn () => [
            'starts_at' => $start ?? now()->subHour(),
            'ends_at' => $end ?? now()->addDay(),
            'status' => FlashSale::STATUS_ACTIVE,
        ]);
    }
}
