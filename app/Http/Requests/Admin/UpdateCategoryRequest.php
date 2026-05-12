<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:service_categories,id',
            'name' => 'sometimes|string|max:255|min:0',
            'commission' => 'sometimes|numeric|min:0',
            'image' => 'sometimes|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ];
    }
}
