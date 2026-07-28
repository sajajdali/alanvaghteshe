<?php

namespace Modules\Api\app\Http\Requests\Api\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class DiseasesUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'diseases.*' => 'exists:diseases,id', // Ensure each disease ID exists in the diseases table
        ];
    }

    public function messages()
    {
        return [
            'diseases.*.exists' => 'دیتای ارسالی اشتباه است و در دیتابیس موجود نیست ارور ۷۷۸۸',
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
