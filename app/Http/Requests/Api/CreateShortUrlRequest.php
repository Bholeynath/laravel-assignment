<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class CreateShortUrlRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, [
            User::ROLE_ADMIN,
            User::ROLE_MEMBER,
        ], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
   public function rules(): array
    {
        return [
            'original_url' => ['required','url','max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'original_url.required' => 'Original URL is required.',
            'original_url.url' => 'Please enter a valid URL.',
        ];
    }
}
