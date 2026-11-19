<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class SendInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && in_array($user->role, [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_ADMIN,
        ], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
     public function rules(): array
    {
        $user = $this->user();

        $allowedRoles = match ($user?->role) {
            User::ROLE_SUPER_ADMIN => [User::ROLE_ADMIN],
            User::ROLE_ADMIN => [User::ROLE_ADMIN,User::ROLE_MEMBER],
            default => [],
        };

        $rules = [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'role' => ['required','string',Rule::in($allowedRoles)],
        ];

        if ($user?->role === User::ROLE_SUPER_ADMIN) {
            $rules['company_name'] = ['required','string','max:255'];
        }

        return $rules;
    }
}
