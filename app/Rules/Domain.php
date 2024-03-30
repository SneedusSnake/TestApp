<?php

namespace App\Rules;

use App\Util\DomainValidator;
use Illuminate\Contracts\Validation\InvokableRule;

class Domain implements InvokableRule
{
    public function __construct(private DomainValidator $domainValidator)
    {

    }
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        if (!$this->domainValidator->validate($value)) {
            $fail(':attribute must be a valid domain');
        }
    }
}
