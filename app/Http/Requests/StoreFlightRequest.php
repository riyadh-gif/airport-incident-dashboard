<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates a bulk flight create payload (1..N rows in a single submit).
 */
class StoreFlightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.flight_no' => ['required', 'string', 'max:255'],
            'rows.*.occurred_at' => ['required', 'date'],
            'rows.*.location' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rows.*.flight_no.required' => 'The flight number is required.',
            'rows.*.location.required' => 'The gate/location is required.',
        ];
    }

    /**
     * The validated flight rows ready for mass-assignment.
     *
     * @return list<array<string, mixed>>
     */
    public function flightRows(): array
    {
        return array_values($this->validated('rows'));
    }
}
