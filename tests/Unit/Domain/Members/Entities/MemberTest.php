<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Members\Entities;

use App\Domain\Members\Entities\Member;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MemberTest extends TestCase
{
    public function test_it_creates_a_member(): void
    {
        $member = Member::create('member-1', 'Juan Pérez', '123456');

        $this->assertSame('Juan Pérez', $member->fullName());
        $this->assertSame('123456', $member->documentNumber());
    }

    public function test_it_rejects_an_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Member::create('member-1', '  ', '123456');
    }

    public function test_it_rejects_an_empty_document(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Member::create('member-1', 'Juan Pérez', '  ');
    }
}