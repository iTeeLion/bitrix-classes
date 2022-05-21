<?php

namespace App\Main;


class Helper
{

    public static function setTitle($title)
    {
        $newTitle = $title . ' (iteelion.ru)';
        $GLOBALS['APPLICATION']->SetTitle($newTitle);
    }

    // vardump with <pre>
    public static function pre($v): void
    {
        echo '<pre>';
        var_dump($v);
        echo '</pre>';
    }

    // show content only for admin
    public static function admpre($v): void
    {
        if ($GLOBALS['USER']->IsAdmin()) {
            self::pre($v);
        }
    }

    // dump var to file
    public static function dump2file($v, $f): void
    {
        ob_start();
        var_dump($v);
        $ob = ob_get_clean();
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . $f . '.log', $ob, FILE_APPEND);
    }

    public static function d2f($v, $f): void
    {
        self::dump2file($v, $f);
    }

    // log row to print
    public static function log2print($str): void
    {
        $row = '[' . date('Y-m-d H:i:s') . '] ' . $str . '<br>';
        echo $row;
    }

    // log row to file
    public static function log2file($str, $logname = 'common'): void
    {
        $row = '[' . date('Y-m-d H:i:s') . '] ' . $str . PHP_EOL;
        $logPath = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/' . $logname . '.log';
        if (!is_dir($logPath)) {
            mkdir($logPath);
        }
        file_put_contents($logPath, $row, FILE_APPEND);
    }

    // do log row
    public static function log($str, $params = []): void
    {
        if (!$params['logname']) {
            $params['logname'] = 'common';
        }
        switch ($params['dst']) {
            case 'out':
                self::log2print($str);
                break;
            case 'file':
                self::log2file($str, $params['logname']);
                break;
            default:
                self::log2print($str);
                self::log2file($str, $params['logname']);
        }
    }

}
