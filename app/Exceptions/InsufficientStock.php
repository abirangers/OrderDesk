<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStock extends RuntimeException
{
    public static function forProduct(string $sku, int $requested, int $available): self
    {
        return new self(
            "Cannot reserve {$requested} unit(s) of {$sku}: {$available} in stock."
        );
    }
}