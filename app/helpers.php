<?php

use App\Support\Jalali;
use Carbon\CarbonInterface;

if (! function_exists('money')) {
    /** 1250000 → "1,250,000 تومان" (digits render Persian through the FD font). */
    function money(int|float|null $amount, bool $unit = true): string
    {
        $formatted = number_format(abs((int) $amount));

        return ($amount < 0 ? '−' : '').$formatted.($unit ? ' '.config('app.currency') : '');
    }
}

if (! function_exists('money_short')) {
    /** 1250000 → "1.25 میلیون" */
    function money_short(int|float|null $amount): string
    {
        $abs = abs((int) $amount);
        $sign = $amount < 0 ? '−' : '';

        foreach ([1_000_000_000 => 'میلیارد', 1_000_000 => 'میلیون', 1_000 => 'هزار'] as $size => $label) {
            if ($abs >= $size) {
                return $sign.rtrim(rtrim(number_format($abs / $size, 2, '.', ''), '0'), '.').' '.$label;
            }
        }

        return $sign.$abs;
    }
}

if (! function_exists('jdate_format')) {
    function jdate_format(?CarbonInterface $date, string $format = 'Y/m/d'): string
    {
        return Jalali::format($date, $format);
    }
}

if (! function_exists('percent_change')) {
    /** Percentage change from $previous to $current, or null when there is no baseline. */
    function percent_change(int|float $current, int|float $previous): ?int
    {
        return $previous > 0 ? (int) round(($current - $previous) / $previous * 100) : null;
    }
}
