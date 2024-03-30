<?php

namespace Tests\Stubs;

use App\Util\DomainValidator;

class StubDomainValidator implements DomainValidator
{
    private bool $valid = true;

    public function validate(string $domain): bool
    {
        return $this->valid;
    }

    public function valid(): void
    {
        $this->valid = true;
    }

    public function invalid(): void
    {
        $this->valid = false;
    }
}
