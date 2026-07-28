<?php

namespace Modules\Api\app\Http\Requests\Api\Requests\Consumption;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaterConsumptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|max:20',
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
