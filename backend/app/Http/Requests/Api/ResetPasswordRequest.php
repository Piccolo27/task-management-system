<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed'],
            'token' => ['required', 'string'],
        ];
    }

    /**
     * To show custom validation messages in reset password process
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.required' => config('constants.errors.email.REQUIRED'),
            'email.string' => config('constants.errors.email.INVALID'),
            'email.email' => config('constants.errors.email.INVALID'),
            'password.required' => config('constants.errors.password.REQUIRED'),
            'password.string' => config('constants.errors.password.INVALID'),
            'password.confirmed' => config('constants.errors.password.CONFIRMED'),
            'token.required' => config('constants.errors.token.REQUIRED'),
            'token.string' => config('constants.errors.token.INVALID')
        ];
    }
}
