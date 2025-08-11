<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;

class UpdateUserCategoryRequest extends BaseRequest
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
            'categories' => [
                'required',
                'array',
            ],
            'categories.*' => [
                'required',
                'integer',
                Rule::exists('categories', 'id'),
            ],
        ];
    }
}
