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

    // Vardump with <pre>
    public static function pre($var): void
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }

    // Show content only for admin
    public static function admPre($var): void
    {
        if ($GLOBALS['USER']->IsAdmin()) {
            self::pre($var);
        }
    }

    // Dump var to file
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

    // Log row to print
    public static function log2print(string $str): void
    {
        $row = '[' . date('Y-m-d H:i:s') . '] ' . $str . '<br>';
        echo $row;
    }

    // Log row date + backtrace
    public static function logBackTraceHeader()
    {
        $backTrace = debug_backtrace();
        return '[' . date('Y-m-d H:i:s') . '] ' . $backTrace[1]['file'] . ' [' . $backTrace[1]['line'] . ']';
    }

    // Log to file
    public static function log2file($var, string $logname = 'common'): void
    {
        $row = self::logBackTraceHeader();
        ob_start();
        var_dump($var);
        $row .= PHP_EOL . ob_get_clean() . PHP_EOL . PHP_EOL;
        $logDirPath = $_SERVER['DOCUMENT_ROOT'] . '/upload/logs';
        if (!is_dir($logDirPath)) {
            mkdir($logDirPath);
        }
        $logFilePath = $logDirPath . '/' . $logname . '.log';
        file_put_contents($logFilePath, $row, FILE_APPEND);
    }

    // Log row to file
    public static function logRow2file(string $str, string $logname = 'common'): void
    {
        $row = self::logBackTraceHeader();
        $row .= PHP_EOL . $str . PHP_EOL . PHP_EOL;
        $logDirPath = $_SERVER['DOCUMENT_ROOT'] . '/upload/logs';
        if (!is_dir($logDirPath)) {
            mkdir($logDirPath);
        }
        $logFilePath = $logDirPath . '/' . $logname . '.log';
        file_put_contents($logFilePath, $row, FILE_APPEND);
    }

    // Make log row
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
