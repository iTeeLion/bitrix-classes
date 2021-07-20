<?php

namespace App;


class Utils
{

    /*
     *  FORMATING CONTENT
     */

    // format int num
    public function intNum($num)
    {
        return number_format($num, 0, '', '');
    }

    // format float num
    public function floatNum($num)
    {
        return number_format($num, 2, '.', '');
    }

    // format float num \w spaces
    public function floatNumForm($num)
    {
        return number_format($num, 2, '.', ' ');
    }

    // format phone
    public function formatPhoneCall($phone)
    {
        return preg_replace('/[^0-9\+]/u', '', $phone);
    }

    public function formatPhoneOnlyDigits($phone)
    {
        return preg_replace('/[^0-9]/u', '', $phone);
    }

    public function formatPhoneInternational($phone)
    {
        $phone = $this->formatPhoneOnlyDigits($phone);
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

    public function formatPhoneMask($phone)
    {
        $phone = $this->formatPhoneCall($phone);
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
    public function urlAsArray($url)
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
    public function translite($s)
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
    function declinationByNumber($number, $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        $format = $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
        return sprintf($format, $number);
    }

    // change url param
    public function replaceUrlQueryValue($url, $var, $val)
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

    /*
     *  WORK WITH DATES
     */

    // get day of week by number
    public function getDayOfWeek($d, $l = 'ru', $s = 0)
    {
        $a = array(
            'en' => array(
                array('Sun', 'Sunday'), array('Mon', 'Monday'), array('Tue', 'Tuesday'),
                array('Wed', 'Wednesday'), array('Thur', 'Thursday'), array('Fri', 'Friday'),
                array('Sat', 'Saturday'),
            ),
            'ru' => array(
                array('Вс', 'Воскресенье'), array('Пн', 'Понедельник'), array('Вт', 'Вторник'),
                array('Ср', 'Среда'), array('Чт', 'Четверг'), array('Пт', 'Пятница'),
                array('Сб', 'Суббота'),
            ),
        );
        return $a[$l][$d][$s];
    }

    // get month name by number
    public function getMonthName($m, $p = [])
    {
        if (!isset($p['lang'])) {
            $p['lang'] = 'ru';
        }
        if (!isset($p['form'])) {
            $p['form'] = 1;
        }
        $a = array(
            'en' => array(
                array('Jan', 'January'),
                array('Feb', 'February'),
                array('Mar', 'March'),
                array('Apr', 'April'),
                array('May', 'May'),
                array('Jun', 'June'),
                array('Jul', 'July'),
                array('Aug', 'August'),
                array('Sep', 'September'),
                array('Oct', 'October'),
                array('Nov', 'November'),
                array('Dec', 'December'),
            ),
            'ru' => array(
                array('Янв', 'Январь', 'Января', 'Январе'),
                array('Фев', 'Февраль', 'Февраля', 'Феврале'),
                array('Мар', 'Март', 'Марта', 'Марте'),
                array('Апр', 'Апрель', 'Апреля', 'Апреле'),
                array('Май', 'Май', 'Мая', 'Мае'),
                array('Июн', 'Июнь', 'Июня', 'Июне'),
                array('Июл', 'Июль', 'Июля', 'Июле'),
                array('Авг', 'Август', 'Августа', 'Августе'),
                array('Сен', 'Сентябрь', 'Сентября', 'Сентябре'),
                array('Окт', 'Октябрь', 'Октября', 'Октябре'),
                array('Ноя', 'Ноябрь', 'Ноября', 'Ноябре'),
                array('Дек', 'Декабрь', 'Декабря', 'Декабре'),
            ),
        );
        return $a[$p['lang']][$m][$p['form']];
    }

    /*
     *  WORK WITH FILES
     */

    // recursive directory deleting
    public function rmdirEX($d)
    {
        if (!file_exists($d)) return true;
        if (!is_dir($d)) return unlink($d);
        foreach (scandir($d, SCANDIR_SORT_NONE) as $i) {
            if ($i == '.' || $i == '..') continue;
            if (!rmdirEX($d . DIRECTORY_SEPARATOR . $i)) return false;
        }
        return rmdir($d);
    }

    public function rmdirr($d)
    {
        $this->rmdirEX($d);
    }

    //show file in viewcontent pos
    public function AddViewContentFromFile($spot, $file, $pos)
    {
        global $APPLICATION;
        ob_start();
        include($file);
        $content = ob_get_contents();
        ob_end_clean();
        $APPLICATION->AddViewContent($spot, $content, $pos);
    }

    /*
     *  DEBUGGING
     */

    // vardump with <pre>
    public function vardmp($v)
    {
        echo '<pre>';
        var_dump($v);
        echo '</pre>';
    }

    public function vdmp($v)
    {
        $this->vardmp($v);
    }

    // show content only for admin
    public function adebug($v)
    {
        global $USER;
        if ($USER->IsAdmin()) {
            echo "<pre>";
            var_dump($v);
            echo "</pre>";
        }
    }

    public function adbg($v)
    {
        $this->adebug($v);
    }

    // dump var to file
    public function dump2file($v, $f)
    {
        ob_start();
        var_dump($v);
        $ob = ob_get_clean();
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . $f . '.log', $ob, FILE_APPEND);
    }

    public function d2f($v, $f)
    {
        $this->dump2file($v, $f);
    }

    // log row to print
    public function log2out($str)
    {
        $row = '[' . date('Y-m-d H:i:s') . '] ' . $str . '<br>';
        echo $row;
    }

    // log row to file
    public function log2file($str, $logname = 'common')
    {
        $row = '[' . date('Y-m-d H:i:s') . '] ' . $str . PHP_EOL;
        $logPath = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/' . $logname . '.log';
        if (!is_dir($logPath)) {
            mkdir($logPath);
        }
        file_put_contents($logPath, $row, FILE_APPEND);
    }

    // do log row
    public function log($str, $params = [])
    {
        if (!$params['logname']) {
            $params['logname'] = 'common';
        }
        switch ($params['dst']) {
            case 'out':
                $this->log2out($str);
                break;
            case 'file':
                $this->log2file($str, $params['logname']);
                break;
            default:
                $this->log2out($str);
                $this->log2file($str, $params['logname']);
        }
    }

}