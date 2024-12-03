<?php

declare(strict_types=1);

namespace Reload\Cpr;

readonly class Cpr
{
    protected string $cpr;

    public function __construct(
        #[\SensitiveParameter]
        string $cpr,
    ) {
        $cleaned = preg_replace('/[\D]/', '', $cpr);

        if (!is_string($cleaned) || (strlen($cleaned) != 10)) {
            throw new \InvalidArgumentException('Invalid CPR number');
        }

        $this->cpr = $cleaned;
    }

    public function __toString(): string
    {
        return substr($this->cpr, 0, 6) . '-' . substr($this->cpr, 6);
    }

    public function formatNumbersOnly(): string
    {
        return $this->cpr;
    }

    public function isFemale(): bool
    {
        return (intval($this->cpr) % 2) === 0;
    }

    public function isMale(): bool
    {
        return (intval($this->cpr) % 2) !== 0;
    }

    public function getDateTimeImmutable(?\DateTimeZone $timezone = null): ?\DateTimeImmutable
    {
        $year = $this->getYear();
        $month = substr($this->cpr, 2, 2);
        $day = substr($this->cpr, 0, 2);

        $datetime = \DateTimeImmutable::createFromFormat('Y-m-d', "{$year}-{$month}-{$day}", $timezone);

        return ($datetime instanceof \DateTimeImmutable) ? $datetime : null;
    }

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
