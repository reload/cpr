<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Reload\Cpr\CprNumber;
use Reload\Cpr\Exception\InvalidCprNumberFormat;
use Reload\Cpr\Exception\NonExistingDate;

#[CoversClass(CprNumber::class)]
class CprTest extends TestCase
{
    #[Test]
    #[TestDox('Test constructing a CPR number object from a string of just digits')]
    public function constructJustDigits(): void
    {
        $cpr = new CprNumber('1204567890');
        $this->assertInstanceOf(CprNumber::class, $cpr);
    }

    #[Test]
    #[TestDox('Test constructing a CPR number object from traditional pretty printed string')]
    public function constructTraditionalFormat(): void
    {
        $cpr = new CprNumber('120456-7890');
        $this->assertInstanceOf(CprNumber::class, $cpr);
    }

    #[Test]
    #[TestDox('Test constructing an invalid CPR number object throws an exception')]
    public function constructingInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $cpr = new CprNumber('123456');
    }

    #[Test]
    #[TestDox('Test constructing a CPR number object with an invalid date throws an exception')]
    public function constructingInvalidDate(): void
    {
        $this->expectException(NonExistingDate::class);
        $cpr = new CprNumber('123456-7890');
    }

    #[Test]
    #[TestDox('Test constructing a CPR number object with a non-existing date throws an exception')]
    public function constructingNonExistingDate(): void
    {
        $this->expectException(NonExistingDate::class);
        $cpr = new CprNumber('290225-1234');
    }

    #[Test]
    #[TestDox('Test format CPR number to traditional, pretty printed string')]
    public function formatToPrettyPrintedString(): void
    {
        $cpr = new CprNumber('1204567890');
        $this->assertEquals('120456-7890', $cpr->formatPretty());
    }

    #[Test]
    #[TestDox('Test format CPR with numbers only')]
    public function formatNumbersOnly(): void
    {
        $cpr = new CprNumber('120456-7890');
        $this->assertEquals('1204567890', $cpr->formatNumbersOnly());
    }

    #[Test]
    #[TestDox('Test standard format of CPR number is traditional, pretty printed string')]
    public function formatToString(): void
    {
        $cpr = new CprNumber('1204567890');
        $this->assertEquals($cpr->formatPretty(), $cpr);
    }

    #[Test]
    #[TestDox('Test equal sequence number is female')]
    public function isFemale(): void
    {
        $cpr = new CprNumber('1204567890');
        $this->assertTrue($cpr->isFemale());

        $cpr = new CprNumber('1204567891');
        $this->assertFalse($cpr->isFemale());
    }

    #[Test]
    #[TestDox('Test odd sequence number is male')]
    public function isMale(): void
    {
        $cpr = new CprNumber('1204567890');
        $this->assertFalse($cpr->isMale());

        $cpr = new CprNumber('1204567891');
        $this->assertTrue($cpr->isMale());
    }

    #[Test]
    #[TestDox('Test CPR number $cpr is from $expectedYear')]
    #[DataProvider('yearProvider')]
    public function correctCentury(string $cpr, string $expectedYear): void
    {
        $year = (new CprNumber($cpr))->getDateTimeImmutable()?->format('Y');
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

    #[Test]
    #[TestDox('Test if $cpr has a valid modulus 11 check: $valid')]
    #[DataProvider('modulus11Provider')]
    public function validateModulus11(string $cpr, bool $valid): void
    {
        $cpr = new CprNumber($cpr);
        $this->assertEquals($valid, $cpr->validateModulus11());
    }

    /**
     * @return array<array<mixed>>
     */
    public static function modulus11Provider(): array
    {
        return [
            ['010120-0000', true],
            ['010120-1000', false],
            ['010157-3001', true],
            ['010158-8001', false],
        ];
    }
}
