<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\IsCompanyUser;

class RegisterProblemRequest extends FormRequest
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
            'company_id' => ['required', 'exists:users,id', new IsCompanyUser],
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'budget' => ['required', 'integer', 'min:0'],
            'timeline_value' => ['required', 'integer', 'min:1'],
            'timeline_unit' => ['required', Rule::in(['day', 'week', 'month', 'year'])],

            'skills' => ['required', 'array', 'min:1'],
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
            'skills.required' => 'At least one skill is required for the problem.',
            'skills.array' => 'Skills must be provided as an array.',
            'skills.min' => 'At least one skill is required.',
            'skills.*.required' => 'Each skill cannot be empty.',
            'skills.*.string' => 'Each skill must be a string.',
            'skills.*.max' => 'Each skill may not be greater than :max characters.',
        ];
    }
}