<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateReportRequest extends FormRequest
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
            'report_to' => ['required', 'integer', 'exists:employees,employee_id'],
            'description' => ['required', 'string', 'max:255']
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
            'report_to.required' => config('constants.errors.report.report_to.REQUIRED'),
            'report_to.number' => config('constants.errors.report.report_to.INVALID'),
            'report_to.exists' => config('constants.errors.report.report_to.EXIST'),
            'description.required' => config('constants.errors.report.description.REQUIRED'),
            'description.string' => config('constants.errors.report.description.INVALID'),
            'description.max' => config('constants.errors.report.description.MAX'),
        ];
    }
}
