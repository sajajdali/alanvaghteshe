<?php

namespace App\Http\Requests\api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetTrainingDayRequest extends FormRequest
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
            'date' => 'required|date',
            'diet_request_id' => [
                'required',
                Rule::exists('diet_requests', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->id());
                }),
            ],
        ];
    }
}
