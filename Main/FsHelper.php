<?php

namespace App\Main;

class FsHelper
{

    // Recursive directory deleting
    public static function rmDirEx(string $dirPath): bool
    {
        if (!file_exists($dirPath)) return true;
        if (!is_dir($dirPath)) return unlink($dirPath);
        foreach (scandir($dirPath, SCANDIR_SORT_NONE) as $i) {
            if ($i == '.' || $i == '..') continue;
            if (!rmdirEX($dirPath . DIRECTORY_SEPARATOR . $i)) return false;
        }
        return rmdir($dirPath);
    }

    public static function rmDirR(string $dirPath): bool
    {
        self::rmdirEX($dirPath);
    }

    // Show file in viewcontent position
    public static function addViewContentFromFile($spot, $filePath, $positionName): void
    {
        global $APPLICATION;
        ob_start();
        include($filePath);
        $content = ob_get_contents();
        ob_end_clean();
        $APPLICATION->AddViewContent($spot, $content, $positionName);
    }

}