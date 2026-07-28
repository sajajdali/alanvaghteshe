<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'package_id' => 'required|exists:packages,id',
            'diet_plan_id' => 'required|exists:diet_plans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'package_id.required' => 'انتخاب پکیج الزامی است.',
            'package_id.exists'   => 'پکیج انتخاب‌شده معتبر نیست.',
            'diet_plan_id.required' => 'انتخاب پلن رژیم الزامی است.',
            'diet_plan_id.exists'   => 'پلن رژیم انتخاب‌شده معتبر نیست.',
        ];
    }

    public function attributes(): array
    {
        return [
            'package_id' => 'پکیج',
            'diet_plan_id' => 'پلن رژیم',
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
