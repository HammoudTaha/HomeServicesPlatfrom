<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^09[0-9]{8}$/',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'password' => 'required|string|min:8|max:255',
            'email' => 'nullable|string|email:rfc,dns|max:255',
        ];
    }
}
