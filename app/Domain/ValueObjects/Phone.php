<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class Phone
{
    private string $value;

    public function __construct(string $value)
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $value);
        if (strlen($cleaned) < 9 || strlen($cleaned) > 15) {
            throw new InvalidArgumentException("Invalid phone number: {$value}");
        }
        $this->value = $cleaned;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function formatted(): string
    {
        $len = strlen($this->value);
        if ($len === 9) {
            return substr($this->value, 0, 3) . ' ' . substr($this->value, 3, 3) . ' ' . substr($this->value, 6);
        }
        return $this->value;
    }
}
