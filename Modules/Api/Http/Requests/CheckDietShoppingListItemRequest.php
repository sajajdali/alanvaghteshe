<?php

namespace Modules\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckDietShoppingListItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_key' => ['required', 'string', 'max:120'],
            'is_checked' => ['required', 'boolean'],
        ];
    }
}
