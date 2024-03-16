<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateProjectRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'project_name' => ['required', 'string', 'max:255'],
            'language' => ['required', 'string', 'max:255'],
            'description'=> ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
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
            'project_name.required' => config('constants.errors.project.name.REQUIRED'),
            'project_name.string' => config('constants.errors.project.name.INVALID'),
            'project_name.max' => config('constants.errors.project.name.MAX'),
            'language.required' => config('constants.errors.project.language.REQUIRED'),
            'language.string' => config('constants.errors.project.language.INVALID'),
            'language.max' => config('constants.errors.project.language.MAX'),
            'description.string' => config('constants.errors.project.description.INVALID'),
            'description.max' => config('constants.errors.project.description.MAX'),
            'start_date.date' => config('constants.errors.project.startDate.INVALID'),
            'end_date.date' => config('constants.errors.project.endDate.INVALID'),
            'end_date.after' => config('constants.errors.project.endDate.INVALID'),
        ];
    }
}
