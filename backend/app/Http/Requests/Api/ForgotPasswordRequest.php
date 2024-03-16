<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ForgotPasswordRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'exists:employees,email']
        ];
    }

    /**
     * To show custom validation messages in forgot password process
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.required' => config('constants.errors.email.REQUIRED'),
            'email.string' => config('constants.errors.email.INVALID'),
            'email.email' => config('constants.errors.email.INVALID'),
            'email.exists' => config('constants.errors.email.INCORRECT')
        ];
    }
}
