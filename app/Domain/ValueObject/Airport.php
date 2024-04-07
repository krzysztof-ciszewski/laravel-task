<?php

namespace App\Domain\ValueObject;

class Airport
{
    public function __construct(public readonly string $code)
    {
    }
}
