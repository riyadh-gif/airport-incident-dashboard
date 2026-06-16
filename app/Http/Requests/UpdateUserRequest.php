<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Validates updating an existing user (admin-only).
 *
 * Password is optional on update — left blank means "keep the current password".
 */
class UpdateUserRequest extends FormRequest
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
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:255', Rule::unique(User::class, 'nip')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'operator'])],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }
}
