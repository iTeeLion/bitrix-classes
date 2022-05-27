<?php

namespace App\Main;


class Helper
{

    // Set title with prefix or suffix (change right here!)
    public static function setTitle(string $title): void
    {
        $newTitle = 'prefix ' . $title . ' suffix';
        $GLOBALS['APPLICATION']->SetTitle($newTitle);
    }

    // vardump with <pre>
    public static function pre($var): void
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }

    // show content only for admin
    public static function admPre($var): void
    {
        if ($GLOBALS['USER']->IsAdmin()) {
            self::pre($var);
        }
    }

    // dump var to file
    public static function dump2file($var, $filePath): void
    {
        ob_start();
        var_dump($v);
        $ob = ob_get_clean();
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . $filePath . '.log', $ob, FILE_APPEND);
    }

    public static function d2f($var, $filePath): void
    {
        self::dump2file($var, $filePath);
    }

    // log row to print
    public static function log2print(string $str): void
    {
        $row = '[' . date('Y-m-d H:i:s') . '] ' . $str . '<br>';
        echo $row;
    }

    // log row to file
    public static function log2file(string $str, string $logname = 'common'): void
    {
        $row = '[' . date('Y-m-d H:i:s') . '] ' . $str . PHP_EOL;
        $logPath = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/' . $logname . '.log';
        if (!is_dir($logPath)) {
            mkdir($logPath);
        }
        file_put_contents($logPath, $row, FILE_APPEND);
    }

    // do log row
    public static function log(string $str, array $arParams = []): void
    {
        if (!$arParams['logname']) {
            $arParams['logname'] = 'common';
        }
        switch ($arParams['dst']) {
            case 'out':
                self::log2print($str);
                break;
            case 'file':
                self::log2file($str, $arParams['logname']);
                break;
            default:
                self::log2print($str);
                self::log2file($str, $arParams['logname']);
        }
    }

}
