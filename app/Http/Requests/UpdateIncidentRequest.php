<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\IncidentCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates a single incident update payload.
 */
class UpdateIncidentRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'condition' => ['required', 'string', Rule::in(IncidentCondition::values())],
            'hospital' => ['required', 'string', 'max:255'],
            'occurred_at' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'flight_no' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'condition.in' => 'The selected condition is invalid.',
            'location.required' => 'The gate/location is required.',
        ];
    }
}
