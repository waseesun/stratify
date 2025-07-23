<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends BaseRequest
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
        $userId = $this->route('user');

        return [
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
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
            'username.unique' => 'The username is already taken by another user.',
            'image_url.image' => 'The file must be an image.',
            'image_url.max' => 'The image may not be greater than 2MB.',
            'image_url.mimes' => 'The image must be a file of type: jpeg, png, jpg.',
            'image_url.dimensions' => 'The image dimensions are too large (max 1000x1000 pixels).',
        ];
    }
}