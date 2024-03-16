<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateDmRequest extends FormRequest
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
            "title" => ['required', 'string', 'max:100'],
            "text" => ["required", "string", "max:30000"],
            "selectedPerson" => ["required", "array"],
            "reservationAndTransmissionDT" => ["nullable", "date", "after_or_equal:now"],
            "replyable" => ["required", "boolean"]
        ];
    }

    /**
     * To show custom validation messages in dm create process
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            "title.required" => config('constants.errors.dm_title.REQUIRED'),
            "title.string" => config("constants.errors.dm_title.INVALID"),
            "title.max" => config("constants.errors.dm_title.MAX"),
            "text.required" => config('constants.errors.dm_text.REQUIRED'),
            "text.string" => config("constants.errors.dm_text.INVALID"),
            "text.max" => config("constants.errors.dm_text.MAX"),
            "selectedPerson.required" => config("constants.errors.dm_selected_person.REQUIRED"),
            "selectedPerson.array" => config("constants.errors.dm_selected_person.INVALID"),
            "reservationAndTransmissionDT.date" => config("constants.errors.dm_reservation_transmission_dt.INVALID"),
            "reservationAndTransmissionDT.after_or_equal" => config("constants.errors.dm_reservation_transmission_dt.INVALID"),
        ];
    }
}
