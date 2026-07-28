<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.required' => 'انتخاب دوره الزامی است.',
            'course_id.exists' => 'دوره انتخاب‌شده معتبر نیست.',
        ];
    }

    public function attributes(): array
    {
        return [
            'course_id' => 'دوره',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
