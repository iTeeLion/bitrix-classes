<?php


namespace App\Main;


class FormatHelper
{

    // format int num
    public static function intNum($num)
    {
        return number_format($num, 0, '', '');
    }

    // format float num
    public static function floatNum($num)
    {
        return number_format($num, 2, '.', '');
    }

    // format float num \w spaces
    public static function floatNumForm($num)
    {
        return number_format($num, 2, '.', ' ');
    }

    // format phone
    public static function formatPhoneCall($phone): string
    {
        return preg_replace('/[^0-9\+]/u', '', $phone);
    }

    public static function formatPhoneOnlyDigits($phone): string
    {
        return preg_replace('/[^0-9]/u', '', $phone);
    }

    public static function formatPhoneInternational($phone): string
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

    public static function formatPhoneMask($phone): string
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

    // prepare url as array
    public static function urlAsArray($url): array
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

    // translite text
    public static function translite($s): string
    {
        $a = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        );
        $s = mb_strtolower($s);
        $s = strtr($s, $a);
        $s = preg_replace('~[^-a-z0-9_]+~u', '_', $s);
        $s = trim($s, "_");
        return $s;
    }

    // declination by number ['%d скрипт', '%d скрипта', '%d скриптов']
    public static function declinationByNumber($number, $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        $format = $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
        return sprintf($format, $number);
    }

    // change url param
    public static function replaceUrlQueryValue($url, $var, $val)
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