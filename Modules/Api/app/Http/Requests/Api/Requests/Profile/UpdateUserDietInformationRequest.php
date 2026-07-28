<?php

namespace Modules\Api\app\Http\Requests\Api\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Api\Http\Controllers\Profile\ProfileController;

class UpdateUserDietInformationRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(ProfileController::getDefinedConstants())],
            'value' => 'required',
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
