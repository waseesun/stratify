<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Validation\Validator;

class CategoryRequest extends BaseRequest
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
            'name' => 'required|string|max:255|unique:categories,name',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
                $validator->errors()->add(
                    'permission_denied', 'You are not authorized to create a category'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'The category name must be unique.',
        ];
    }
}
