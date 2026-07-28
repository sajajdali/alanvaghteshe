<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'mobile' => 'required_without:email',
            'email' => 'required_without:mobile|email',
            'code' => 'required|digits:4',
            'device_os' => 'required|in:android,ios,web',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required_without' => 'شماره موبایل یا ایمیل را وارد کنید',
            'code.required' => 'کد را وارد کنید',
            'code.digits' => 'کد باید ۴ رقمی باشد',
            'device_os.required' => 'سیستم عامل را وارد کنید',
            'device_os.in' => 'سیستم عامل معتبر نیست',
            'email.required_without' => 'شماره موبایل یا ایمیل را وارد کنید',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->guest();
    }
}
