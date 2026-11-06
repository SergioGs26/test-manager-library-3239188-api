<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Catalog\Entities;

use App\Domain\Catalog\Entities\Book;
use App\Domain\Shared\ValueObjects\Isbn;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    private function createBook(
        int $totalCopies = 5,
        int $availableCopies = 5
    ): Book {
        return Book::create(
            '11111111-1111-4111-8111-111111111111',
            'Clean Code',
            'Robert C. Martin',
            Isbn::fromString('9780132350884'),
            '33333333-3333-4333-8333-333333333333',
            $totalCopies,
            $availableCopies
        );
    }

    public function test_it_creates_a_book_with_valid_copies(): void
    {
        $book = $this->createBook(5, 3);

        $this->assertSame(5, $book->totalCopies());
        $this->assertSame(3, $book->availableCopies());
        $this->assertFalse($book->isDeleted());
    }

    public function test_it_rejects_zero_total_copies(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createBook(0, 0);
    }

    public function test_it_rejects_available_copies_below_zero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createBook(5, -1);
    }

    public function test_it_rejects_available_copies_above_total(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createBook(5, 6);
    }

    public function test_withdraw_decreases_available_copies(): void
    {
        $book = $this->createBook(5, 5);

        $book->withdraw();

        $this->assertSame(4, $book->availableCopies());
    }

    public function test_withdraw_rejects_when_no_copies_are_available(): void
    {
        $book = $this->createBook(5, 0);

        $this->expectException(InvalidArgumentException::class);

        $book->withdraw();
    }

    public function test_restock_increases_available_copies(): void
    {
        $book = $this->createBook(5, 2);

        $book->restock(2);

        $this->assertSame(4, $book->availableCopies());
    }

    public function test_restock_rejects_zero_or_negative_quantity(): void
    {
        $book = $this->createBook(5, 2);

        $this->expectException(InvalidArgumentException::class);

        $book->restock(0);
    }

    public function test_restock_rejects_exceeding_total_copies(): void
    {
        $book = $this->createBook(5, 4);

        $this->expectException(InvalidArgumentException::class);

        $book->restock(2);
    }

    public function test_it_marks_a_book_as_deleted(): void
    {
        $book = $this->createBook();

        $book->delete();

        $this->assertTrue($book->isDeleted());
    }

    public function test_deleted_books_cannot_be_withdrawn(): void
    {
        $book = $this->createBook();

        $book->delete();

        $this->expectException(InvalidArgumentException::class);

        $book->withdraw();
    }

    public function test_deleted_books_cannot_be_restocked(): void
    {
        $book = $this->createBook();

        $book->delete();

        $this->expectException(InvalidArgumentException::class);

        $book->restock(1);
    }
}