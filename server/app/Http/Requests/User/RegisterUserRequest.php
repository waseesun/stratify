<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;
use App\Rules\StrongPassword;

class RegisterUserRequest extends BaseRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')],
            'password' => ['required', 'string', 'min:8', 'confirmed', new StrongPassword()],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image_url' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg', // Allowed MIME types
                'max:2048', // Max File Size in KB
                Rule::dimensions()->maxWidth(1000)->maxHeight(1000), // Optional: Max dimensions
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'The email address is already in use.',
            'username.unique' => 'The username is already taken.',
            'password.confirmed' => 'The password confirmation does not match.',
            'image_url.image' => 'The file must be an image.',
            'image_url.max' => 'The image may not be greater than 2MB.',
            'image_url.mimes' => 'The image must be a file of type: jpeg, png, jpg.',
            'image_url.dimensions' => 'The image dimensions are too large (max 1000x1000 pixels).',
        ];
    }
}
