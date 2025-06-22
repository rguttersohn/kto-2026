<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFilterOperator implements ValidationRule
{

    protected array $allowedOperators = [
        'eq', 'neq', 'gt', 'gte', 'lt', 'lte', 'in', 'nin', 'null', 'notnull'
    ];

    protected function passes($value):bool
    {
        return in_array($value, $this->allowedOperators);
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {   

        
        $operator = explode('.', $attribute)[2];
        
        if (!in_array($operator, $this->allowedOperators, true)) {

            $fail("The operator `{$operator}` in `$attribute` is not allowed.");
            
        }
        
    }
}
