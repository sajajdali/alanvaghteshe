<?php

namespace Modules\Coupon\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_at'      => 'nullable',
            'end_at'        => 'nullable',
            'title'         => 'required|string',
            'code'          => 'required|string',
            'value'         => 'integer|required',
            'is_percent'    => 'required',
            'minimum_spend' => 'nullable',
            'maximum_spend' => 'nullable',
            'usable_count'  => 'nullable',
            'can_used_for'  => 'required',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
