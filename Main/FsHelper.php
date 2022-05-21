<?php


namespace App\Main;


class FsHelper
{

    // recursive directory deleting
    public static function rmdirEX($d)
    {
        if (!file_exists($d)) return true;
        if (!is_dir($d)) return unlink($d);
        foreach (scandir($d, SCANDIR_SORT_NONE) as $i) {
            if ($i == '.' || $i == '..') continue;
            if (!rmdirEX($d . DIRECTORY_SEPARATOR . $i)) return false;
        }
        return rmdir($d);
    }

    public static function rmdirr($d)
    {
        self::rmdirEX($d);
    }

    //show file in viewcontent pos
    public static function AddViewContentFromFile($spot, $file, $pos): void
    {
        global $APPLICATION;
        ob_start();
        include($file);
        $content = ob_get_contents();
        ob_end_clean();
        $APPLICATION->AddViewContent($spot, $content, $pos);
    }

}