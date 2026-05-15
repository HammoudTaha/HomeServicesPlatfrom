<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateProviderRequest extends FormRequest
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
            'first_name' => 'required|string|max:20',
            'last_name' => 'required|string|max:20',
            'email' => 'sometimes|string|regex:/^[\w\.-]+@gmail\.com$/',
            'phone' => 'required|string|regex:/^09[0-9]{8}$/',
            'address' => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'service_category_id' => 'required|integer|exists:service_categories,id'
        ];
    }
}
