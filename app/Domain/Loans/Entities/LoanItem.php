<?php
declare(strict_types=1);

namespace App\Domain\Loans\Entities;

use DateTimeImmutable;
use InvalidArgumentException;

final class LoanItem
{
    private ?DateTimeImmutable $returnedAt = null;

    private function __construct(
        private string $id,
        private string $bookId,
        private string $bookTitle,
        private string $isbn,
        private string $categoryName,
        private DateTimeImmutable $dueAt
    ) {
    }

    public static function create(
        string $id,
        string $bookId,
        string $bookTitle,
        string $isbn,
        string $categoryName,
        DateTimeImmutable $dueAt
    ): self {
        return new self(
            $id,
            $bookId,
            $bookTitle,
            $isbn,
            $categoryName,
            $dueAt
        );
    }

    public function returnAt(DateTimeImmutable $returnedAt): void
    {
        if ($this->returnedAt !== null) {
            throw new InvalidArgumentException(
                'This loan item has already been returned.'
            );
        }

        $this->returnedAt = $returnedAt;
    }

    public function isOverdue(DateTimeImmutable $date): bool
    {
        return $this->returnedAt === null && $date > $this->dueAt;
    }

    public function isReturned(): bool
    {
        return $this->returnedAt !== null;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function bookId(): string
    {
        return $this->bookId;
    }

    public function bookTitle(): string
    {
        return $this->bookTitle;
    }

    public function isbn(): string
    {
        return $this->isbn;
    }

    public function categoryName(): string
    {
        return $this->categoryName;
    }

    public function dueAt(): DateTimeImmutable
    {
        return $this->dueAt;
    }

    public function returnedAt(): ?DateTimeImmutable
    {
        return $this->returnedAt;
    }
}