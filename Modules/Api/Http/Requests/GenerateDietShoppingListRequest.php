<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Diet\Enum\ShoppingListPeriodEnum;

class GenerateDietShoppingListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period' => [
                'required',
                'string',
                Rule::enum(ShoppingListPeriodEnum::class),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'period.required' => 'بازه سبد خرید الزامی است.',
            'period.enum' => 'بازه سبد خرید باید روزانه یا هفتگی باشد.',
        ];
    }

    public function period(): ShoppingListPeriodEnum
    {
        return ShoppingListPeriodEnum::from($this->validated('period'));
    }
}
