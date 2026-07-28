<?php

namespace Modules\Api\app\Http\Requests\Api\Requests\Consumption;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsumptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type' => 'required',
            'meal_id' => 'required|exists:meals,id',
            'food_id' => 'required',
            'unit_id' => 'required|exists:food_units,id',
            'quantity' => 'required|numeric|max:3000',

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
