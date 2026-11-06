<?php
declare(strict_types=1);

namespace App\Domain\Loans\Entities;

use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;

final class Loan
{
    private array $items = [];

    private function __construct(
        private string $id,
        private string $memberId,
        private string $loanedByUserId,
        private DateTimeImmutable $loanedAt
    ) {
    }

    public static function create(
        string $id,
        string $memberId,
        string $loanedByUserId,
        DateTimeImmutable $loanedAt
    ): self {
        return new self(
            $id,
            $memberId,
            $loanedByUserId,
            $loanedAt
        );
    }

    public function addItem(
        string $itemId,
        string $bookId,
        string $bookTitle,
        string $isbn,
        string $categoryName
    ): void {
        if (count($this->items) >= 3) {
            throw new InvalidArgumentException(
                'A loan cannot contain more than three books.'
            );
        }

        foreach ($this->items as $item) {
            if ($item->bookId() === $bookId) {
                throw new InvalidArgumentException(
                    'A book cannot be added twice to the same loan.'
                );
            }
        }

        $dueAt = $this->loanedAt->add(
            new DateInterval('P14D')
        );

        $this->items[] = LoanItem::create(
            $itemId,
            $bookId,
            $bookTitle,
            $isbn,
            $categoryName,
            $dueAt
        );
    }

    public function returnItem(
        string $itemId,
        DateTimeImmutable $returnedAt
    ): void {
        foreach ($this->items as $item) {
            if ($item->id() === $itemId) {
                $item->returnAt($returnedAt);

                return;
            }
        }

        throw new InvalidArgumentException(
            'The loan item does not exist.'
        );
    }

    public function state(DateTimeImmutable $date): string
    {
        if (count($this->items) === 0) {
            throw new InvalidArgumentException(
                'A loan must contain at least one book.'
            );
        }

        $allReturned = true;

        foreach ($this->items as $item) {
            if (!$item->isReturned()) {
                $allReturned = false;

                if ($item->isOverdue($date)) {
                    return 'overdue';
                }
            }
        }

        return $allReturned ? 'closed' : 'open';
    }

    public function id(): string
    {
        return $this->id;
    }

    public function memberId(): string
    {
        return $this->memberId;
    }

    public function loanedByUserId(): string
    {
        return $this->loanedByUserId;
    }

    public function loanedAt(): DateTimeImmutable
    {
        return $this->loanedAt;
    }

    /**
     * @return LoanItem[]
     */
    public function items(): array
    {
        return $this->items;
    }
}