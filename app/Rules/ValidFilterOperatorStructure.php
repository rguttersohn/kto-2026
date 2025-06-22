<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFilterOperatorStructure implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        
        if (!is_array($value)) {

            $fail("The filter `$attribute` must use operator-based syntax (e.g., filter[$attribute][eq]=value).");
            
        }
        
    }
}
