<?php

namespace App\Support;

use InvalidArgumentException;

class TimeParser
{
    /**
     * Parse a user time input into minutes, then round UP to nearest 15 min.
     *
     * Accepted examples:
     *  - "2h15m", "2h", "15m", "1h 5m"
     *  - "1:30" (H:MM)
     *  - "2.25" / "2,25" (decimal hours)
     *  - "0.25" (15 min)
     *
     * Returns:
     *  [
     *    'raw_minutes' => int,
     *    'rounded_minutes' => int,
     *    'hours' => float,
     *  ]
     */
    public static function parse(string $input): array
    {
        $s = trim(mb_strtolower($input));
        if ($s === '') {
            throw new InvalidArgumentException('Time input is empty.');
        }

        // normalize comma decimal to dot
        $s = str_replace(',', '.', $s);
        // normalize spaces
        $s = preg_replace('/\s+/', ' ', $s);

        $rawMinutes = null;

        // 1) H:MM format (e.g. 1:30)
        if (preg_match('/^(\d+)\s*:\s*(\d{1,2})$/', $s, $m)) {
            $h = (int) $m[1];
            $min = (int) $m[2];
            if ($min >= 60) {
                throw new InvalidArgumentException('Minutes part must be < 60 for H:MM format.');
            }
            $rawMinutes = $h * 60 + $min;
        }

        // 2) Token format: e.g. "2h15m", "2h", "45m", "1h 5m"
        if ($rawMinutes === null && preg_match('/[hm]/', $s)) {
            $hours = 0.0;
            $mins = 0.0;

            if (preg_match_all('/(\d+(?:\.\d+)?)\s*h/', $s, $hm)) {
                foreach ($hm[1] as $hStr) {
                    $hours += (float) $hStr;
                }
            }

            if (preg_match_all('/(\d+(?:\.\d+)?)\s*m/', $s, $mm)) {
                foreach ($mm[1] as $mStr) {
                    $mins += (float) $mStr;
                }
            }

            $rawMinutes = (int) round($hours * 60 + $mins);
        }

        // 3) Pure number => decimal hours (e.g. "2.25")
        if ($rawMinutes === null && preg_match('/^\d+(?:\.\d+)?$/', $s)) {
            $hours = (float) $s;
            $rawMinutes = (int) round($hours * 60);
        }

        if ($rawMinutes === null || $rawMinutes <= 0) {
            throw new InvalidArgumentException('Unrecognized or invalid time format.');
        }

        $roundedMinutes = self::roundUpToQuarterHour($rawMinutes);

        return [
            'raw_minutes' => $rawMinutes,
            'rounded_minutes' => $roundedMinutes,
            'hours' => $roundedMinutes / 60.0,
        ];
    }

    public static function roundUpToQuarterHour(int $minutes): int
    {
        return (int) (ceil($minutes / 15) * 15);
    }
}
