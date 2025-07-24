<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePortfolioLinksRequest extends FormRequest
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
            'links' => ['required', 'array'],
            'links.*' => ['required', 'string', 'url', 'max:2048'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'links.required' => 'Portfolio links are required.',
            'links.array' => 'Portfolio links must be provided as an array.',
            'links.*.required' => 'Each portfolio link cannot be empty.',
            'links.*.url' => 'Each portfolio link must be a valid URL.',
            'links.*.max' => 'Each portfolio link may not be greater than 2048 characters.',
        ];
    }
}

