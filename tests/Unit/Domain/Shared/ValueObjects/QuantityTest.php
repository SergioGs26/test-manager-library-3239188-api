<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObjects;

use App\Domain\Shared\ValueObjects\Quantity;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class QuantityTest extends TestCase
{
    public function test_it_accepts_a_positive_quantity(): void
    {
        $quantity = Quantity::fromInt(5);

        $this->assertSame(5, $quantity->value());
    }

    public function test_it_accepts_quantity_of_one(): void
    {
        $quantity = Quantity::fromInt(1);

        $this->assertSame(1, $quantity->value());
    }

    public function test_it_rejects_zero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Quantity::fromInt(0);
    }

    public function test_it_rejects_negative_quantities(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Quantity::fromInt(-3);
    }

    public function test_it_compares_two_quantities(): void
    {
        $first = Quantity::fromInt(5);
        $second = Quantity::fromInt(5);

        $this->assertTrue($first->equals($second));
    }
}