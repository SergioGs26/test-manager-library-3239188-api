<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Loans\Entities;

use App\Domain\Loans\Entities\Loan;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class LoanTest extends TestCase
{
    private function createLoan(): Loan
    {
        return Loan::create(
            'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa',
            'bbbbbbbb-bbbb-4bbb-8bbb-bbbbbbbbbbbb',
            'cccccccc-cccc-4ccc-8ccc-cccccccccccc',
            new DateTimeImmutable('2026-11-01 10:00:00')
        );
    }

    private function addBook(
        Loan $loan,
        string $itemId,
        string $bookId
    ): void {
        $loan->addItem(
            $itemId,
            $bookId,
            'Clean Code',
            '9780132350884',
            'Tecnología'
        );
    }

    public function test_it_creates_a_loan(): void
    {
        $loan = $this->createLoan();

        $this->assertSame(
            'aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa',
            $loan->id()
        );

        $this->assertCount(0, $loan->items());
    }

    public function test_it_adds_a_book_to_the_loan(): void
    {
        $loan = $this->createLoan();

        $this->addBook(
            $loan,
            'dddddddd-dddd-4ddd-8ddd-dddddddddddd',
            'eeeeeeee-eeee-4eee-8eee-eeeeeeeeeeee'
        );

        $this->assertCount(1, $loan->items());
    }

    public function test_it_rejects_more_than_three_books(): void
    {
        $loan = $this->createLoan();

        $this->addBook($loan, 'item-1', 'book-1');
        $this->addBook($loan, 'item-2', 'book-2');
        $this->addBook($loan, 'item-3', 'book-3');

        $this->expectException(InvalidArgumentException::class);

        $this->addBook($loan, 'item-4', 'book-4');
    }

    public function test_it_rejects_the_same_book_twice(): void
    {
        $loan = $this->createLoan();

        $this->addBook($loan, 'item-1', 'book-1');

        $this->expectException(InvalidArgumentException::class);

        $this->addBook($loan, 'item-2', 'book-1');
    }

    public function test_it_sets_a_due_date_fourteen_days_after_loan(): void
    {
        $loan = $this->createLoan();

        $this->addBook($loan, 'item-1', 'book-1');

        $this->assertSame(
            '2026-11-15 10:00:00',
            $loan->items()[0]->dueAt()->format('Y-m-d H:i:s')
        );
    }

    public function test_it_returns_a_loan_item(): void
    {
        $loan = $this->createLoan();

        $this->addBook($loan, 'item-1', 'book-1');

        $loan->returnItem(
            'item-1',
            new DateTimeImmutable('2026-11-10 12:00:00')
        );

        $this->assertTrue($loan->items()[0]->isReturned());
    }

    public function test_it_rejects_returning_an_item_twice(): void
    {
        $loan = $this->createLoan();

        $this->addBook($loan, 'item-1', 'book-1');

        $returnedAt = new DateTimeImmutable('2026-11-10 12:00:00');

        $loan->returnItem('item-1', $returnedAt);

        $this->expectException(InvalidArgumentException::class);

        $loan->returnItem('item-1', $returnedAt);
    }

    public function test_it_rejects_returning_an_unknown_item(): void
    {
        $loan = $this->createLoan();

        $this->expectException(InvalidArgumentException::class);

        $loan->returnItem(
            'unknown-item',
            new DateTimeImmutable('2026-11-10 12:00:00')
        );
    }

    public function test_it_has_open_state_when_not_overdue(): void
    {
        $loan = $this->createLoan();

        $this->addBook($loan, 'item-1', 'book-1');

        $this->assertSame(
            'open',
            $loan->state(new DateTimeImmutable('2026-11-10'))
        );
    }

    public function test_it_has_overdue_state_when_a_book_is_overdue(): void
    {
        $loan = $this->createLoan();

        $this->addBook($loan, 'item-1', 'book-1');

        $this->assertSame(
            'overdue',
            $loan->state(new DateTimeImmutable('2026-11-16'))
        );
    }

    public function test_it_has_closed_state_when_all_books_are_returned(): void
    {
        $loan = $this->createLoan();

        $this->addBook($loan, 'item-1', 'book-1');

        $loan->returnItem(
            'item-1',
            new DateTimeImmutable('2026-11-10')
        );

        $this->assertSame(
            'closed',
            $loan->state(new DateTimeImmutable('2026-11-16'))
        );
    }

    public function test_it_rejects_state_calculation_without_items(): void
    {
        $loan = $this->createLoan();

        $this->expectException(InvalidArgumentException::class);

        $loan->state(new DateTimeImmutable('2026-11-10'));
    }
}