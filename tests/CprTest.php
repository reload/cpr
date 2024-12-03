<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Reload\Cpr\Cpr;

#[CoversClass(Cpr::class)]
class CprTest extends TestCase
{
    #[Test]
    #[TestDox('Test constructing a CPR object from a string of just digits')]
    public function constructJustDigits(): void
    {
        $cpr = new Cpr('1234567890');
        $this->assertInstanceOf(Cpr::class, $cpr);
    }

    #[Test]
    #[TestDox('Test constructing a CPR object from traditional pretty printed string')]
    public function constructTraditionalFormat(): void
    {
        $cpr = new Cpr('123456-7890');
        $this->assertInstanceOf(Cpr::class, $cpr);
    }

    #[Test]
    #[TestDox('Test constructing an invalid CPR object throws an exception')]
    public function constructingInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $cpr = new Cpr('123456');
    }

    #[Test]
    #[TestDox('Test format CPR number to traditional, prtetty prined string')]
    public function formatToPrettyPrintedString(): void
    {
        $cpr = new Cpr('1234567890');
        $this->assertEquals('123456-7890', $cpr);
    }

    #[Test]
    #[TestDox('Test format CPR with numbers only')]
    public function formatNumbersOnly(): void
    {
        $cpr = new Cpr('123456-7890');
        $this->assertEquals('1234567890', $cpr->formatNumbersOnly());
    }

    #[Test]
    #[TestDox('Test equal serial number is female')]
    public function isFemale(): void
    {
        $cpr = new Cpr('1234567890');
        $this->assertTrue($cpr->isFemale());

        $cpr = new Cpr('1234567891');
        $this->assertFalse($cpr->isFemale());
    }

    #[Test]
    #[TestDox('Test odd serial number is male')]
    public function isMale(): void
    {
        $cpr = new Cpr('1234567890');
        $this->assertFalse($cpr->isMale());

        $cpr = new Cpr('1234567891');
        $this->assertTrue($cpr->isMale());
    }

    #[Test]
    #[TestDox('Test CPR $cpr is from $expectedYear')]
    #[DataProvider('yearProvider')]
    public function correctCentury(string $cpr, string $expectedYear): void
    {
        $year = (new Cpr($cpr))->getDateTimeImmutable()?->format('Y');
        $this->assertEquals($expectedYear, $year);
    }

    /**
     * @return array<array<string>>
     */
    public static function yearProvider(): array
    {
        return [
            ['010120-0000', '1920'],
            ['010120-1000', '1920'],
            ['010120-2000', '1920'],
            ['010120-3000', '1920'],
            ['010136-4000', '2036'],
            ['010137-4000', '1937'],
            ['010157-5000', '2057'],
            ['010158-5000', '1858'],
            ['010157-6000', '2057'],
            ['010158-6000', '1858'],
            ['010157-7000', '2057'],
            ['010158-7000', '1858'],
            ['010157-8000', '2057'],
            ['010158-8000', '1858'],
            ['010136-9000', '2036'],
            ['010137-9000', '1937'],
        ];
    }
}