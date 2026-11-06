<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObjects;

use App\Domain\Shared\ValueObjects\Isbn;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class IsbnTest extends TestCase
{
    public function test_it_accepts_a_valid_isbn(): void
    {
        $isbn = Isbn::fromString('9780306406157');

        $this->assertSame('9780306406157', $isbn->value());
    }

    public function test_it_rejects_an_isbn_with_less_than_13_digits(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Isbn::fromString('978030640615');
    }

    public function test_it_rejects_an_isbn_with_letters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Isbn::fromString('978030640615A');
    }

    public function test_it_rejects_an_invalid_check_digit(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Isbn::fromString('9780306406158');
    }

    public function test_it_compares_two_isbns(): void
    {
        $first = Isbn::fromString('9780306406157');
        $second = Isbn::fromString('9780306406157');

        $this->assertTrue($first->equals($second));
    }
}