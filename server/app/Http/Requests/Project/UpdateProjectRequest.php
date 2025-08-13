<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fee' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', Rule::in(['completed', 'cancelled'])],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', Rule::after('start_date')],
        ];
    }

    public function messages()
    {
        return [
            'fee.min' => 'The fee must be at least 0.',
        ];
    }
}
