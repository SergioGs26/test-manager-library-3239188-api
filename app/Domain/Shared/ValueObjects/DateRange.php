<?php
declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class DateRange
{
    private function __construct(
        private DateTimeImmutable $from,
        private DateTimeImmutable $to
    ) {
    }

    public static function fromDates(
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): self {
        if ($from > $to) {
            throw new InvalidArgumentException(
                'The start date cannot be after the end date.'
            );
        }

        return new self($from, $to);
    }

    public function from(): DateTimeImmutable
    {
        return $this->from;
    }

    public function to(): DateTimeImmutable
    {
        return $this->to;
    }

    public function contains(DateTimeImmutable $date): bool
    {
        return $date >= $this->from && $date <= $this->to;
    }
}