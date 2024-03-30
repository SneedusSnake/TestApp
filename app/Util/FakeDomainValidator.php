<?php

namespace App\Util;

class FakeDomainValidator implements DomainValidator
{
    public function validate(string $domain): bool
    {
        return true;
    }
}
