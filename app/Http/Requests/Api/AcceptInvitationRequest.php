<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AcceptInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'token' => ['required','string','exists:invitations,token'],
            'password' => ['required','string','min:8','confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'token.required'     => 'Invitation url token is required.',
            'token.exists'       => 'Invalid invitation Url.',
            'password.required'  => 'Password is required.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
