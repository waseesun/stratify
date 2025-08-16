<?php

namespace App\Http\Requests\Project;

use App\Rules\IsCompanyUser;
use App\Http\Requests\BaseRequest;

class RegisterTransactionRequest extends BaseRequest
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
            'project_id' => ['required', 'exists:projects,id'],
            'milestone_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'amount.min' => 'The amount must be at least 1.',
        ];
    }
}
