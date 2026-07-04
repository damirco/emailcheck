<?php
namespace Damirco\Emailcheck\Tests;

use Damirco\Emailcheck\EmailSet;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class EmailSetTest extends TestCase
{
    public function testCheckSyntaxSeparatesValidAndInvalid(): void
    {
        $set = new EmailSet([
            'valid@example.com',
            'not-valid',
            'another@example.com',
            '',
        ]);

        $result = $set->checkSyntax();

        $this->assertContains('valid@example.com', $result['valid']);
        $this->assertContains('another@example.com', $result['valid']);
        $this->assertContains('not-valid', $result['invalid']);
        $this->assertContains('', $result['invalid']);
    }

    #[Group('integration')]
    public function testCheckMxRecordsSeparatesByDns(): void
    {
        $set = new EmailSet([
            'test@gmail.com',
            'test@thisdomaindoesnotexist12345.com',
        ]);

        $result = $set->checkMxRecords();

        $this->assertContains('test@gmail.com', $result['valid']);
        $this->assertContains('test@thisdomaindoesnotexist12345.com', $result['invalid']);
    }

    #[Group('integration')]
    public function testCheckWithBothFlagsEnabled(): void
    {
        $set = new EmailSet([
            'test@gmail.com',
            'not-valid',
        ]);

        $result = $set->check(true, true);

        $this->assertContains('test@gmail.com', $result['valid']);
        $this->assertContains('not-valid', $result['invalid']);
    }

    public function testCheckWithBothFlagsDisabled(): void
    {
        $set = new EmailSet(['anything']);

        $result = $set->check(false, false);

        $this->assertContains('anything', $result['valid']);
        $this->assertEmpty($result['invalid']);
    }

    public function testConstructorDeduplicatesEmails(): void
    {
        $set = new EmailSet([
            'user@example.com',
            'user@example.com',
            'other@example.com',
        ]);

        $result = $set->checkSyntax();

        $this->assertCount(2, $result['valid']);
    }

    public function testConstructorFiltersNonStrings(): void
    {
        $set = new EmailSet([
            'valid@example.com',
            123,
            null,
            true,
        ]);

        $result = $set->checkSyntax();

        $this->assertCount(1, $result['valid']);
        $this->assertContains('valid@example.com', $result['valid']);
    }
}
