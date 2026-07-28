<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseDiscountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'discount_code' => ['required', 'string'],
            'course_id' => ['required', 'exists:courses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'discount_code.required' => 'وارد کردن کد تخفیف الزامی است.',
            'course_id.required' => 'انتخاب دوره الزامی است.',
            'course_id.exists' => 'دوره انتخاب‌شده معتبر نیست.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
