<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'project_id' => ['required', 'integer', 'exists:projects,project_id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'assigned_member_id' => ['required', 'integer', 'exists:employees,employee_id'],
            'estimate_hr' => ['required', 'integer', 'min:0'],
            'actual_hr' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'integer', 'min:0'],
            'estimate_start_date' => ['nullable', 'date'],
            'estimate_finish_date' => ['nullable', 'date', 'after:estimate_start_date'],
            'actual_start_date' => ['nullable', 'date'],
            'actual_finish_date' => ['nullable', 'date', 'after:actual_start_date']
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
            'project_id.required' => config('constants.errors.task.project_id.REQUIRED'),
            'project_id.integer' => config('constants.errors.task.project_id.INVALID'),
            'project_id.exists' => config('constants.errors.task.project_id.EXIST'),
            'title.required' => config('constants.errors.task.title.REQUIRED'),
            'title.string' => config('constants.errors.task.title.INVALID'),
            'title.max' => config('constants.errors.task.title.MAX'),
            'description.required' => config('constants.errors.task.description.REQUIRED'),
            'description.string' => config('constants.errors.task.description.INVALID'),
            'description.max' => config('constants.errors.task.description.MAX'),
            'assigned_member_id.required' => config('constants.errors.task.assigned_member_id.REQUIRED'),
            'assigned_member_id.integer' => config('constants.errors.task.assigned_member_id.INVALID'),
            'assigned_member_id.exists' => config('constants.errors.task.assigned_member_id.EXIST'),
            'estimate_hr.required' => config('constants.errors.task.estimate_hr.REQUIRED'),
            'estimate_hr.integer' => config('constants.errors.task.estimate_hr.INVALID'),
            'estimate_hr.min' => config('constants.errors.task.estimate_hr.INVALID'),
            'actual_hr.integer' => config('constants.errors.task.actual_hr.INVALID'),
            'actual_hr.min' => config('constants.errors.task.actual_hr.INVALID'),
            'status.required' => config('constants.errors.task.status.REQUIRED'),
            'status.integer' => config('constants.errors.task.status.INVALID'),
            'status.min' => config('constants.errors.task.status.INVALID'),
            'estimate_start_date.date' => config('constants.errors.task.estimate_start_date.INVALID'),
            'estimate_finish_date.date' => config('constants.errors.task.estimate_finish_date.INVALID'),
            'estimate_finish_date.after' => config('constants.errors.task.estimate_finish_date.INVALID'),
            'actual_start_date.date' => config('constants.errors.task.actual_start_date.INVALID'),
            'actual_finish_date.date' => config('constants.errors.task.actual_finish_date.INVALID'),
            'actual_finish_date.after' => config('constants.errors.task.actual_finish_date.INVALID')
        ];
    }
}
