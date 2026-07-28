<?php

namespace Modules\Api\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Base64JsonRule implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //check value is base64 and json
        if (!is_string($value) || !is_array(json_decode(base64_decode($value), true))) {
            $fail("The :attribute must be base64 json.");
        }
    }
}
