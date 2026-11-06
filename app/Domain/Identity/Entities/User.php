<?php
declare(strict_types=1);

namespace App\Domain\Identity\Entities;

use InvalidArgumentException;

final readonly class User
{
    private function __construct(
        private string $id,
        private string $username,
        private string $passwordHash,
        private string $role
    ) {
    }

    public static function create(
        string $id,
        string $username,
        string $passwordHash,
        string $role
    ): self {
        $username = strtolower(trim($username));

        if ($username === '') {
            throw new InvalidArgumentException(
                'Username cannot be empty.'
            );
        }

        if ($passwordHash === '') {
            throw new InvalidArgumentException(
                'Password hash cannot be empty.'
            );
        }

        if (!in_array($role, ['admin', 'librarian'], true)) {
            throw new InvalidArgumentException(
                'Invalid user role.'
            );
        }

        return new self(
            $id,
            $username,
            $passwordHash,
            $role
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isLibrarian(): bool
    {
        return $this->role === 'librarian';
    }
}