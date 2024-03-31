<?php

namespace App\Util;

class FakeDomainValidator implements DomainValidator
{
    public function validate(string $url): bool
    {
        return true;
    }
}
