<?php namespace Gecche\Cupparis\App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;

class FormatValues
{
    public static function formatEuro($value, $decimals = 2)
    {
        return static::formatNumber($value, $decimals);
    }

    public static function formatEuro0($value, $decimals = 2)
    {
        if (is_null($value) || !is_numeric($value)) {
            return 0.00;
        }
        return static::formatEuro($value, $decimals);
    }

    public static function formatEuroRli($value, $decimals = 2)
    {
        if (is_null($value) || !is_numeric($value)) {
            return "0,00";
        }
        return static::formatNumber($value, $decimals, ",");
    }

    public static function formatNumber($value, $decimals, $decPoint = '.', $thousandsSeparator = '', $nullAsDefault = false)
    {
        if (is_null($value)) {
            return $value;
        }
        if (!is_numeric($value)) {
            if ($nullAsDefault) {
                return null;
            }
            throw new \InvalidArgumentException($value . ' is not numeric');
        }

        return number_format(floatval($value), $decimals, $decPoint, $thousandsSeparator);
    }

    public static function formatNumber0($value, $decimals, $decPoint = '.', $thousandsSeparator = '')
    {
        if (is_null($value) || !is_numeric($value)) {
            $value = 0.00;
        }

        return number_format(floatval($value), $decimals, $decPoint, $thousandsSeparator);
    }

    public static function formatIntNumber($value, $thousandsSeparator = '')
    {
        if (is_null($value)) {
            return $value;
        }
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException($value . ' is not numeric');
        }

        return number_format(intval($value), 0, '.', $thousandsSeparator);
    }


    public static function formatDate($value, $formatIn = 'Y-m-d', $formatOut = 'd-m-Y')
    {
        try {
            $date = Carbon::createFromFormat($formatIn, $value);
        } catch (\Exception $e) {
            return $value;
        }

        return $date->format($formatOut);
    }

    public static function formatDateRli($value)
    {
        return static::formatDate($value, 'Y-m-d', 'dmY');
    }

    public static function formatDateIta($value)
    {
        return static::formatDate($value, 'Y-m-d', 'd/m/Y');
    }

    public static function formatCsvTextValue($value, $replacePairs = null)
    {
        if (is_null($replacePairs)) {

            $replacePairs = [
                "\n\r" => "\t",
                "\r\n" => "\t",
                "\n" => "\t",
                "\r" => "\t",
                ";" => ","
            ];
        }
        $newValue = strtr($value, $replacePairs);
        return $newValue;
    }

}
