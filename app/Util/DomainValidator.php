<?php

namespace App\Util;

interface DomainValidator
{
    public function validate(string $domain): bool;
}
