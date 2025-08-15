<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\BaseRequest;
use App\Rules\IsProviderUser;


class RegisterProposalRequest extends BaseRequest
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
            'provider_id' => ['required', 'exists:users,id', new IsProviderUser],
            'problem_id' => ['required', 'exists:problems,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],

            'docs' => ['nullable', 'array', 'min:1'],
            'docs.*' => ['required', 'file', 'mimes:pdf', 'max:5120'], // 5MB
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
            'docs.array' => 'Documents must be provided as an array.',
            'docs.min' => 'At least one document is required.',
            'docs.*.required' => 'Each document file is required.',
            'docs.*.file' => 'Each document must be a file.',
            'docs.*.mimes' => 'Each document must be a PDF file.',
            'docs.*.max' => 'Each document may not be greater than 5MB.',
        ];
    }
}