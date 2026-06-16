<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\IncidentCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates a bulk incident create payload (1..N rows in a single submit).
 *
 * The legacy app supported adding several casualty rows at once, so the
 * request always normalises input into a `rows` array of validated rows.
 */
class StoreIncidentRequest extends FormRequest
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
            'rows.*.name' => ['required', 'string', 'max:255'],
            'rows.*.condition' => ['required', 'string', Rule::in(IncidentCondition::values())],
            'rows.*.hospital' => ['required', 'string', 'max:255'],
            'rows.*.occurred_at' => ['required', 'date'],
            'rows.*.location' => ['required', 'string', 'max:255'],
            'rows.*.flight_no' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rows.*.condition.in' => 'The selected condition is invalid.',
            'rows.*.location.required' => 'The gate/location is required.',
            'rows.*.name.required' => 'The casualty name is required.',
        ];
    }

    /**
     * The validated incident rows ready for mass-assignment.
     *
     * @return list<array<string, mixed>>
     */
    public function incidentRows(): array
    {
        return array_values($this->validated('rows'));
    }
}
