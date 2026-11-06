<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Identity\Entities;

use App\Domain\Identity\Entities\User;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_it_creates_an_admin_user(): void
    {
        $user = User::create('user-1', 'Admin', 'hashed-password', 'admin');

        $this->assertSame('admin', $user->username());
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isLibrarian());
    }

    public function test_it_normalizes_username(): void
    {
        $user = User::create('user-1', '  SERGIO  ', 'hashed-password', 'librarian');

        $this->assertSame('sergio', $user->username());
    }

    public function test_it_accepts_librarian_role(): void
    {
        $user = User::create('user-1', 'sergio', 'hashed-password', 'librarian');

        $this->assertTrue($user->isLibrarian());
    }

    public function test_it_rejects_invalid_role(): void
    {
        $this->expectException(InvalidArgumentException::class);

        User::create('user-1', 'sergio', 'hashed-password', 'superadmin');
    }

    public function test_it_rejects_empty_username(): void
    {
        $this->expectException(InvalidArgumentException::class);

        User::create('user-1', '  ', 'hashed-password', 'admin');
    }

    public function test_it_rejects_empty_password_hash(): void
    {
        $this->expectException(InvalidArgumentException::class);

        User::create('user-1', 'sergio', '', 'admin');
    }
}