<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates a single flight update payload.
 */
class UpdateFlightRequest extends FormRequest
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
            'flight_no' => ['required', 'string', 'max:255'],
            'occurred_at' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
        ];
    }
}
