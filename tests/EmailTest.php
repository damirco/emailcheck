<?php

namespace Damirco\Emailcheck\Tests;

use Damirco\Emailcheck\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    /** @dataProvider validSyntaxProvider */
    public function testIsSyntaxValidReturnsTrue(string $email): void
    {
        $this->assertTrue((new Email($email))->isSyntaxValid());
    }

    public static function validSyntaxProvider(): array
    {
        return [
            'simple'       => ['user@example.com'],
            'with dots'    => ['first.last@example.com'],
            'with plus'    => ['user+tag@example.com'],
            'with numbers' => ['user123@example.com'],
            'subdomain'    => ['user@sub.example.com'],
            'underscore'   => ['user_name@example.com'],
            'hyphen'       => ['user-name@example.com'],
        ];
    }

    /** @dataProvider invalidSyntaxProvider */
    public function testIsSyntaxValidReturnsFalse(string $email): void
    {
        $this->assertFalse((new Email($email))->isSyntaxValid());
    }

    public static function invalidSyntaxProvider(): array
    {
        return [
            'no at sign'       => ['userexample.com'],
            'no domain'        => ['user@'],
            'no local part'    => ['@example.com'],
            'double at'        => ['user@@example.com'],
            'spaces'           => ['user @example.com'],
            'empty string'     => [''],
        ];
    }

    public function testIsMxValidReturnsTrueForKnownDomain(): void
    {
        $this->assertTrue((new Email('test@gmail.com'))->isMxValid());
    }

    public function testIsMxValidReturnsFalseForFakeDomain(): void
    {
        $this->assertFalse((new Email('test@thisdomaindoesnotexist12345.com'))->isMxValid());
    }

    public function testIsMxValidReturnsFalseWithoutAtSign(): void
    {
        $this->assertFalse((new Email('invalid'))->isMxValid());
    }

    public function testIsValidCombinesSyntaxAndMx(): void
    {
        $valid = new Email('test@gmail.com');
        $this->assertTrue($valid->isValid());

        $invalidSyntax = new Email('not-an-email');
        $this->assertFalse($invalidSyntax->isValid());
    }

    public function testToStringReturnsEmail(): void
    {
        $email = new Email('user@example.com');
        $this->assertSame('user@example.com', (string) $email);
    }
}
