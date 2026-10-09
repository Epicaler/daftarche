<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;

class Jalali
{
    public const MONTHS = [
        1 => 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند',
    ];

    public const WEEKDAYS_SHORT = ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'];

    public static function format(?CarbonInterface $date, string $format = 'Y/m/d'): string
    {
        return $date ? Jalalian::fromCarbon($date->toMutable())->format($format) : '';
    }

    /** Parse "1405/07/17" (Latin or Persian digits, / or - separators) to a Gregorian date. */
    public static function parse(?string $value): ?CarbonImmutable
    {
        $value = self::latinDigits(trim((string) $value));

        if (! preg_match('/^(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})$/', $value, $m)) {
            return null;
        }

        [$jy, $jm, $jd] = [(int) $m[1], (int) $m[2], (int) $m[3]];

        if (! CalendarUtils::checkDate($jy, $jm, $jd)) {
            return null;
        }

        [$gy, $gm, $gd] = CalendarUtils::toGregorian($jy, $jm, $jd);

        return CarbonImmutable::create($gy, $gm, $gd)->startOfDay();
    }

    public static function isValid(?string $value): bool
    {
        return self::parse($value) !== null;
    }

    public static function today(): string
    {
        return self::format(now());
    }

    /** @return array{0:int,1:int} [year, month] of the given (or current) date */
    public static function yearMonth(?CarbonInterface $date = null): array
    {
        $j = Jalalian::fromCarbon(($date ?? now())->toMutable());

        return [$j->getYear(), $j->getMonth()];
    }

    /** @return array{0:CarbonImmutable,1:CarbonImmutable} Gregorian start/end dates of a Jalali month */
    public static function monthRange(int $jy, int $jm): array
    {
        $days = $jm <= 6 ? 31 : ($jm <= 11 ? 30 : (CalendarUtils::isLeapJalaliYear($jy) ? 30 : 29));
        [$sy, $sm, $sd] = CalendarUtils::toGregorian($jy, $jm, 1);
        [$ey, $em, $ed] = CalendarUtils::toGregorian($jy, $jm, $days);

        return [CarbonImmutable::create($sy, $sm, $sd)->startOfDay(), CarbonImmutable::create($ey, $em, $ed)->startOfDay()];
    }

    /** @return array{0:CarbonImmutable,1:CarbonImmutable} */
    public static function yearRange(int $jy): array
    {
        return [self::monthRange($jy, 1)[0], self::monthRange($jy, 12)[1]];
    }

    /** @return array{0:int,1:int} */
    public static function shiftMonth(int $jy, int $jm, int $by): array
    {
        $index = $jy * 12 + ($jm - 1) + $by;

        return [intdiv($index, 12), $index % 12 + 1];
    }

    public static function monthLabel(int $jy, int $jm): string
    {
        return self::MONTHS[$jm].' '.$jy;
    }

    public static function latinDigits(string $value): string
    {
        return strtr($value, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);
    }
}
