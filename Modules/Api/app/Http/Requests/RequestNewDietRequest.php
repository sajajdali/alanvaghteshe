<?php

namespace Modules\Api\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestNewDietRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'weight' => 'required|numeric|min:0',
            'plan_id' => 'required|exists:diet_plans,id',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
