<?php
declare(strict_types=1);

namespace App\Domain\Catalog\Entities;

use App\Domain\Shared\ValueObjects\Isbn;
use InvalidArgumentException;

final class Book
{
    private bool $deleted = false;

    private function __construct(
        private string $id,
        private string $title,
        private string $author,
        private Isbn $isbn,
        private string $categoryId,
        private int $totalCopies,
        private int $availableCopies
    ) {
        if ($totalCopies < 1) {
            throw new InvalidArgumentException(
                'Total copies must be at least one.'
            );
        }

        if ($availableCopies < 0 || $availableCopies > $totalCopies) {
            throw new InvalidArgumentException(
                'Available copies must be between zero and total copies.'
            );
        }
    }

    public static function create(
        string $id,
        string $title,
        string $author,
        Isbn $isbn,
        string $categoryId,
        int $totalCopies,
        int $availableCopies
    ): self {
        return new self(
            $id,
            $title,
            $author,
            $isbn,
            $categoryId,
            $totalCopies,
            $availableCopies
        );
    }

    public function withdraw(): void
    {
        $this->ensureNotDeleted();

        if ($this->availableCopies <= 0) {
            throw new InvalidArgumentException(
                'No available copies to withdraw.'
            );
        }

        $this->availableCopies--;
    }

    public function restock(int $quantity): void
    {
        $this->ensureNotDeleted();

        if ($quantity < 1) {
            throw new InvalidArgumentException(
                'Restock quantity must be greater than zero.'
            );
        }

        if ($this->availableCopies + $quantity > $this->totalCopies) {
            throw new InvalidArgumentException(
                'Available copies cannot exceed total copies.'
            );
        }

        $this->availableCopies += $quantity;
    }

    public function delete(): void
    {
        $this->deleted = true;
    }

    private function ensureNotDeleted(): void
    {
        if ($this->deleted) {
            throw new InvalidArgumentException(
                'A deleted book cannot be modified.'
            );
        }
    }

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function author(): string
    {
        return $this->author;
    }

    public function isbn(): Isbn
    {
        return $this->isbn;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function totalCopies(): int
    {
        return $this->totalCopies;
    }

    public function availableCopies(): int
    {
        return $this->availableCopies;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }
}