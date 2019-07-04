<?php

if (!function_exists('make_date_range')) {
    function make_date_range($year, $month)
    {
        $month = str_pad('' . $month, 2, '0', STR_PAD_LEFT);
        $startDate = $year . '-' . $month . '-01 00:00:00';
        $lastDay = date('t', strtotime($startDate));
        $endDate = $year . '-' . $month . '-' . $lastDay . ' 23:59:59';
        return [$startDate,$endDate];
    }
}

if (!function_exists('guessDataType')) {
    function guessDataType($string)
    {
        if (is_numeric($string)) {
            return 'number';
        }
        $format = 'Y-m-d H:i:s';
        $format2 = 'Y-m-d H:i:s.000';
        $d = DateTime::createFromFormat($format, $string);
        if ($d && $d->format($format) == $string) {
            return 'timestamp';
        }
        $d2 = DateTime::createFromFormat($format2, $string);
        if ($d2 && $d2->format($format2) == $string) {
            return 'timestamp';
        }
        return 'string';
    }
}
