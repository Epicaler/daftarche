<?php

namespace App\Rules;

use App\Support\Jalali;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class JalaliDate implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Jalali::isValid($value)) {
            $fail('تاریخ وارد شده معتبر نیست (مثال: 1405/07/17).');
        }
    }
}
