<?php
declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;

final readonly class Quantity
{
    private function __construct(
        private int $value
    ) {
    }

    public static function fromInt(int $value): self
    {
        if ($value < 1) {
            throw new InvalidArgumentException(
                'Quantity must be greater than zero.'
            );
        }

        return new self($value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}