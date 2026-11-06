<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObjects;

use App\Domain\Shared\ValueObjects\DateRange;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DateRangeTest extends TestCase
{
    public function test_it_accepts_a_valid_date_range(): void
    {
        $from = new DateTimeImmutable('2026-01-01');
        $to = new DateTimeImmutable('2026-01-31');

        $range = DateRange::fromDates($from, $to);

        $this->assertSame($from, $range->from());
        $this->assertSame($to, $range->to());
    }

    public function test_it_accepts_the_same_start_and_end_date(): void
    {
        $date = new DateTimeImmutable('2026-01-15');

        $range = DateRange::fromDates($date, $date);

        $this->assertSame($date, $range->from());
        $this->assertSame($date, $range->to());
    }

    public function test_it_rejects_a_start_date_after_the_end_date(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DateRange::fromDates(
            new DateTimeImmutable('2026-02-01'),
            new DateTimeImmutable('2026-01-01')
        );
    }

    public function test_it_contains_a_date_inside_the_range(): void
    {
        $range = DateRange::fromDates(
            new DateTimeImmutable('2026-01-01'),
            new DateTimeImmutable('2026-01-31')
        );

        $this->assertTrue(
            $range->contains(new DateTimeImmutable('2026-01-15'))
        );
    }

    public function test_it_includes_the_start_and_end_dates(): void
    {
        $from = new DateTimeImmutable('2026-01-01');
        $to = new DateTimeImmutable('2026-01-31');

        $range = DateRange::fromDates($from, $to);

        $this->assertTrue($range->contains($from));
        $this->assertTrue($range->contains($to));
    }

    public function test_it_rejects_a_date_outside_the_range(): void
    {
        $range = DateRange::fromDates(
            new DateTimeImmutable('2026-01-01'),
            new DateTimeImmutable('2026-01-31')
        );

        $this->assertFalse(
            $range->contains(new DateTimeImmutable('2026-02-01'))
        );
    }
}