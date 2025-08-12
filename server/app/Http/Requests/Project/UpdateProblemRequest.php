<?php

namespace App\Http\Requests\Project;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProblemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'exists:categories,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'budget' => ['sometimes', 'integer', 'min:0'],
            'timeline_value' => ['sometimes', 'integer', 'min:1'],
            'timeline_unit' => ['sometimes', Rule::in(['day', 'week', 'month', 'year'])],
            'status' => ['sometimes', Rule::in(['open', 'cancelled'])],

            'skills' => ['sometimes', 'array', 'min:1'],
            'skills.*' => ['required', 'string', 'max:100'],
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
            'status.in' => 'The selected problem status is invalid.',
            'skills.array' => 'Skills must be provided as an array.',
            'skills.min' => 'At least one skill is required.',
            'skills.*.required' => 'Each skill is required.',
            'skills.*.string' => 'Each skill must be a string.',
            'skills.*.max' => 'Each skill may not be greater than :max characters.',
        ];
    }
}
