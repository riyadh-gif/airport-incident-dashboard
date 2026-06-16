<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incident>
 */
class IncidentFactory extends Factory
{
    /**
     * Realistic synthetic Indonesian hospital names (no real PII).
     *
     * @var list<string>
     */
    private const HOSPITALS = [
        'RSUD Dr. Soetomo',
        'RS Darmo',
        'RSAL Dr. Ramelan',
        'RSUD Dr. Saiful Anwar',
        'RS Premier Surabaya',
        'RSUP Dr. Sardjito',
        'RS Pondok Indah',
        'RSUP Persahabatan',
        'RSUD Dr. Soedono',
        'RS Siloam',
    ];

    /**
     * Casualty conditions used by the legacy app.
     *
     * @var list<string>
     */
    private const CONDITIONS = ['meninggal', 'ringan', 'sedang', 'berat'];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake('id_ID')->name(),
            'condition' => fake()->randomElement(self::CONDITIONS),
            'hospital' => fake()->randomElement(self::HOSPITALS),
            'occurred_at' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'location' => $this->gateCode(),
            'flight_no' => $this->flightNo(),
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
