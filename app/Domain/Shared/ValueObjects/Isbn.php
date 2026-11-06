<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;

final readonly class Isbn
{
    private function __construct(
        private string $value
    ) {
    }

    public static function fromString(string $value): self
    {
        if (!preg_match('/^[0-9]{13}$/D', $value)) {
            throw new InvalidArgumentException(
                'ISBN must contain exactly 13 digits.'
            );
        }

        if (!self::hasValidChecksum($value)) {
            throw new InvalidArgumentException(
                'ISBN has an invalid check digit.'
            );
        }

        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function hasValidChecksum(string $isbn): bool
    {
        $sum = 0;

        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $isbn[$i];

            $sum += $digit * ($i % 2 === 0 ? 1 : 3);
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return $checkDigit === (int) $isbn[12];
    }
}