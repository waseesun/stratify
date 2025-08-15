<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\BaseRequest;

class RegisterProjectRequest extends BaseRequest
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
            'problem_id' => ['required', 'exists:problems,id'],
            'proposal_id' => ['required', 'exists:proposals,id'],
            'fee' => ['required', 'integer', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ];
    }

    public function messages()
    {
        return [
            'fee.min' => 'The fee must be at least 0.',
        ];
    }
}