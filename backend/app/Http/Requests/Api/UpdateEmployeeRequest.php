<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
        $employeeId = $this->route("id");

        return [
            'employee_name' => ['required','string','max:255'],
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('employees', 'email')
                    ->ignore($employeeId, 'employee_id')
                    ->withoutTrashed(),
                'max:255'
            ],
            'profile' => ['nullable', 'file', 'mimes:png,jpg', 'max:5120'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'numeric', 'digits_between:7,11'],
            'dob' => ['nullable', 'date', 'before:today'],
            'position' => ['required', 'string', 'max:1', 'in:0,1'],
            'old_password' => ['nullable', 'string', 'current_password:api'],
            'new_password' => ['nullable', 'string', 'confirmed']
        ];
    }

    /**
     * To show custom validation messages in employee create process
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'employee_name.required' => config('constants.errors.name.REQUIRED'),
            'employee_name.string' => config('constants.errors.name.INVALID'),
            'employee_name.max' => config('constants.errors.name.MAX'),
            'email.required' => config('constants.errors.email.REQUIRED'),
            'email.string' => config('constants.errors.email.INVALID'),
            'email.email' => config('constants.errors.email.INVALID'),
            'email.unique' => config('constants.errors.email.UNIQUE'),
            'email.max' => config('constants.errors.email.MAX'),
            'profile.file' => config('constants.errors.profile.FILE'),
            'profile.mimes' => config('constants.errors.profile.MIMES'),
            'profile.max' => config('constants.errors.profile.MAX'),
            'address.string' => config('constants.errors.address.INVALID'),
            'address.max' => config('constants.errors.address.MAX'),
            'phone.numeric' => config('constants.errors.phone.NUMERIC'),
            'phone.digits_between' => config('constants.errors.phone.DIGITS_BETWEEN'),
            'dob.date' => config('constants.errors.dob.INVALID'),
            'dob.before' => config('constants.errors.dob.INVALID'),
            'position.required' => config('constants.errors.position.REQUIRED'),
            'position.string' => config('constants.errors.position.INVALID'),
            'position.max' => config('constants.errors.position.INVALID'),
            'position.in' => config('constants.errors.position.LIMIT'),
            'old_password.string' => config('constants.errors.old_password.INVALID'),
            'old_password.current_password' => config('constants.errors.old_password.INCORRECT'),
            'new_password.string' => config('constants.new_password.INVALID'),
            'new_password.confirmed' => config('constants.new_password.CONFIRM')
        ];
    }
}
