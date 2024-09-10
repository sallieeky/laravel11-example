<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class PasswordChangeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow any authenticated user to make the request
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'new_password' => [
                'required',
                'string',
                'min:8',                             // Minimum 8 characters
                'max:50',                            // Maximum 20 characters
                'regex:/[a-z]/',                     // At least one lowercase character
                'regex:/[A-Z]/',                     // At least one uppercase character
                'regex:/[0-9]/',                     // At least one number
                'regex:/[^a-zA-Z0-9]/',              // At least one special character (non-alphanumeric)
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'new_password.min' => 'The new password must be at least 8 characters.',
            'new_password.max' => 'The new password must not be more than 50 characters.',
            'new_password.regex' => 'The new password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
            'new_password.confirmed' => 'The new password confirmation does not match.',
        ];
    }
}
