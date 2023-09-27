<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CustomEmailRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    public function passes($attribute, $value) {
        $value = str_replace(' ', '', $value);
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }



    public function message() {
        return ':attribute debe ser un email valido.';
    }
}
