<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flight>
 */
class FlightFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'flight_no' => $this->flightNo(),
            'occurred_at' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'location' => $this->gateCode(),
        ];
    }

    /**
     * Generate an airport gate code: letter A–K + number 1–29 (e.g. "A12", "K3").
     */
    private function gateCode(): string
    {
        return fake()->randomElement(range('A', 'K')) . fake()->numberBetween(1, 29);
    }

    /**
     * Generate a synthetic flight number (e.g. "GA-431").
     */
    private function flightNo(): string
    {
        $airline = fake()->randomElement(['GA', 'JT', 'QZ', 'ID']);

        return sprintf('%s-%d', $airline, fake()->numberBetween(100, 999));
    }
}
