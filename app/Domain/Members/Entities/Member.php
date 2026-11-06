<?php
declare(strict_types=1);

namespace App\Domain\Members\Entities;

use InvalidArgumentException;

final readonly class Member
{
    private function __construct(
        private string $id,
        private string $fullName,
        private string $documentNumber
    ) {
    }

    public static function create(
        string $id,
        string $fullName,
        string $documentNumber
    ): self {
        if (trim($fullName) === '') {
            throw new InvalidArgumentException(
                'Member name cannot be empty.'
            );
        }

        if (trim($documentNumber) === '') {
            throw new InvalidArgumentException(
                'Document number cannot be empty.'
            );
        }

        return new self(
            $id,
            trim($fullName),
            trim($documentNumber)
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function fullName(): string
    {
        return $this->fullName;
    }

    public function documentNumber(): string
    {
        return $this->documentNumber;
    }
}
