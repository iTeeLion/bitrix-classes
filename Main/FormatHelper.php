<?php

namespace App\Main;

class FormatHelper
{

    // Format int num
    public static function num2int($num): string
    {
        return (string)number_format($num, 0, '', '');
    }

    // Format float num
    public static function num2float($num): string
    {
        return (string)number_format($num, 2, '.', '');
    }

    // Format float num \w spaces
    public static function num2spacedFloat($num): string
    {
        return (string)number_format($num, 2, '.', ' ');
    }

    // Format phone for call
    public static function phone2call($phone): string
    {
        return preg_replace('/[^0-9\+]/u', '', $phone);
    }

    // Format phone, only digits
    public static function phoneOnlyDigits($phone): string
    {
        return preg_replace('/[^0-9]/u', '', $phone);
    }

    // Format phone, international standart
    public static function phoneInternational($phone): string
    {
        $phone = self::formatPhoneOnlyDigits($phone);
        if (mb_strlen($phone) > 9) {
            $firstSymbol = mb_substr($phone, 0, 1);
            if ($firstSymbol == '8') {
                $phoneLast = mb_substr($phone, 1);
                $phone = '+7' . $phoneLast;
            }
            if ($firstSymbol == '7') {
                $phone = '+' . $phone;
            }
        }
        return $phone;
    }

    // Format phone with mask (spaces, brackets, etc.)
    public static function phoneMask($phone): string
    {
        $phone = self::formatPhoneCall($phone);
        $phoneLength = mb_strlen($phone);
        $arPhone = [];
        $length = mb_strlen($phone);
        for ($i = 0; $i < $length; $i++) {
            $arPhone[] = mb_substr($phone, 0, 1);
            $phone = mb_substr($phone, 1);
        }
        $arPhone = array_reverse($arPhone);
        $arPhoneForm = [];

        $i = 1;
        foreach ($arPhone as $symbol) {
            $arPhoneForm[] = $symbol;
            if (in_array($i, [2, 4])) {
                $arPhoneForm[] = '-';
            }
            if ($phoneLength > 9) {
                if (in_array($i, [7])) {
                    $arPhoneForm[] = ' ';
                }
                if (in_array($i, [10])) {
                    $arPhoneForm[] = ' ';
                }
            } else {
                if (in_array($i, [7])) {
                    $arPhoneForm[] = '-';
                }
            }
            $i++;
        }

        $arPhoneForm = array_reverse($arPhoneForm);
        $phoneForm = implode('', $arPhoneForm);

        return $phoneForm;
    }

    // Parse url as array
    public static function urlAsArray(string $url): array
    {
        $url = explode('/', $url);
        if (!$url[0]) {
            unset($url[0]);
        }
        if (!$url[count($url)]) {
            unset($url[count($url)]);
        }
        return $url;
    }

    // Translite text
    public static function translite(string $str): string
    {
        $a = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        ];
        $str = mb_strtolower($str);
        $str = strtr($str, $a);
        $str = preg_replace('~[^-a-z0-9_]+~u', '_', $str);
        $str = trim($str, "_");
        return $str;
    }

    // Declination by number ['%d скрипт', '%d скрипта', '%d скриптов']
    public static function declinationByNumber($number, array $arOptions): string
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        $format = $arOptions[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
        return (string)sprintf($format, $number);
    }

    // Change url param
    public static function replaceUrlQueryValue(string $url, $var, $val): string
    {
        $arUrl = parse_url($url);
        if ($arUrl['query']) {
            parse_str($arUrl['query'], $arQueries);
            $arQueries[$var] = $val;
            $strQueries = http_build_query($arQueries);
            return $arUrl['path'] . '?' . $strQueries;
        } else {
            return $arUrl['path'] . '?' . $var . '=' . $val;
        }
    }

}