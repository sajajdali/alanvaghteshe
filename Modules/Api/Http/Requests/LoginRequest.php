<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * User should send mobile or email.
     *
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'mobile' => 'required_without:mobile_or_email',
            'mobile_or_email' => 'required_without:mobile',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required_without' => 'شماره موبایل یا ایمیل را وارد کنید',
            'mobile_or_email.required_without' => 'شماره موبایل یا ایمیل را وارد کنید',
        ];
    }

    /**
     * Only guests to make this request.
     */
    public function authorize(): bool
    {
        return auth()->guest();
    }
}
