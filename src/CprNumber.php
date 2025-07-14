<?php

declare(strict_types=1);

namespace Reload\Cpr;

use Reload\Cpr\Exception\InvalidCprNumberFormat;
use Reload\Cpr\Exception\NonExistingDate;

/**
 * A class representing Danish civil registration numbers (CPR
 * numbers).
 *
 * The CPR number is a ten digit number with the format DDMMYY-SSSS, where
 * the first six digits represent the date of birth and the last four digits
 * are a sequence number.
 *
 * The CPR number is used in Denmark to uniquely identify persons in
 * various systems, and is also used as a personal identification number
 * in many contexts.
 *
 * The class represents a CPR number as a read-only value object, and
 * provides methods for working with, formatting, and validating CPR
 * numbers.
 */
readonly class CprNumber
{
    /**
     * The CPR number as a string consisting only of numbers.
     */
    protected string $cpr;

    /**
     * Construct a CPR number readonly value object from a string.
     *
     * @param string $cpr A string with the CPR number.
     *
     * @throws InvalidCprNumberFormat If the CPR number does not
     * contain 10 digits
     * @throws NonExistingDate If the date in the CPR number doesn't
     * exist.
     */
    public function __construct(
        #[\SensitiveParameter]
        string $cpr,
    ) {
        $cleaned = preg_replace('/[\D]/', '', $cpr);

        if (!is_string($cleaned) || (strlen($cleaned) != 10)) {
            throw new InvalidCprNumberFormat('CPR number does not containg 10 digits');
        }

        $this->cpr = $cleaned;

        $year = $this->getYear();
        $month = $this->getMonth();
        $day = $this->getDay();

        if (!checkdate($month, $day, $year)) {
            throw new NonExistingDate('Date in CPR number does not exist');
        }
    }

    /**
     * Format the CPR number in the traditional format (120345-6789).
     *
     * @see CprNumber::formatPretty()
     */
    public function __toString(): string
    {
        return $this->formatPretty();
    }

    /**
     * Format the CPR number in the traditional format (120345-6789).
     */
    public function formatPretty(): string
    {
        return substr($this->cpr, 0, 6) . '-' . substr($this->cpr, 6);
    }

    /**
     * Format the CPR number using numbers only (1203456789).
     */
    public function formatNumbersOnly(): string
    {
        return $this->cpr;
    }

    /**
     * Check if the CPR number represents a female person.
     */
    public function isFemale(): bool
    {
        return (intval($this->cpr) % 2) === 0;
    }

    /**
     * Check if the CPR number represents a male person.
     */
    public function isMale(): bool
    {
        return (intval($this->cpr) % 2) !== 0;
    }

    /**
     * Get a DateTimeImmutable object from the CPR number.
     *
     * @param ?\DateTimeZone $timezone A DateTimeZone object for the desired time zone. Defaults: current timezone.
     */
    public function getDateTimeImmutable(?\DateTimeZone $timezone = null): ?\DateTimeImmutable
    {
        $year = $this->getYear();
        $month = $this->getMonth();
        $day = $this->getDay();

        $datetime = \DateTimeImmutable::createFromFormat('Y-n-j', "{$year}-{$month}-{$day}", $timezone);

        return ($datetime instanceof \DateTimeImmutable) ? $datetime : null;
    }

    /**
     * Validate the CPR number using the modulus 11 algorithm.
     *
     * NOTICE: CPR numbers are no longer required to fulfill the modulus
     * 11 check. You should NOT use this method to validate or dismiss
     * CPR numbers.
     */
    public function validateModulus11(): bool
    {
        $weights = [
            0 => 4,
            1 => 3,
            2 => 2,
            3 => 7,
            4 => 6,
            5 => 5,
            6 => 4,
            7 => 3,
            8 => 2,
            9 => 1,
        ];

        $sum = 0;
        foreach (str_split($this->cpr) as $i => $digit) {
            $sum += intval($digit) * $weights[$i];
        }

        return ($sum % 11) === 0;
    }

    /**
     * Get the day of the CPR number.
     */
    protected function getDay(): int
    {
        return intval(substr($this->cpr, 0, 2));
    }

    /**
     * Get the month of the CPR number.
     */
    protected function getMonth(): int
    {
        return intval(substr($this->cpr, 2, 2));
    }

    /**
     * Get the year of the CPR number.
     */
    protected function getYear(): int
    {
        $twoDigiYear = intval(substr($this->cpr, 4, 2));
        $seventh = intval(substr($this->cpr, 6, 1));

        switch ($seventh) {
            case 0:
            case 1:
            case 2:
            case 3:
                return 1900 + $twoDigiYear;

            case 4:
            case 9:
                if ($twoDigiYear <= 36) {
                    return 2000 + $twoDigiYear;
                }
                return 1900 + $twoDigiYear;

            case 5:
            case 6:
            case 7:
            case 8:
            default:
                if ($twoDigiYear <= 57) {
                    return 2000 + $twoDigiYear;
                }
                return 1800 + $twoDigiYear;
        }
    }
}
