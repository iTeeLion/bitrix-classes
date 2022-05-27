<?php

namespace App\Main;

use \App\Main\FormatHelper;

class SocNetHelper
{

    // Get link to start whatsapp dialog
    public static function getPhoneLinkWhatApp($phone): string
    {
        $phone = FormatHelper::formatPhoneOnlyDigits(FormatHelper::formatPhoneInternational($phone));
        return (string)'https://api.whatsapp.com/send?phone=+' . $phone;
    }

    // Get link to start viber dialog
    public static function getPhoneLinkViber($phone): string
    {
        $phone = FormatHelper::formatPhoneOnlyDigits(FormatHelper::formatPhoneInternational($phone));
        return (string)'viber://chat?number=%2B' . $phone;
    }

    // Get link to youtube video preview
    public static function getYoutubePreviewLink(string $link): string
    {
        $videoId = self::getYoutubeVideoIdFromUrl($link);
        if($videoId){
            $preview = 'https://img.youtube.com/vi/'.$videoId.'/mqdefault.jpg';
            return $preview;
        }
        return '';
    }

    // Get youtube video id
    public static function getYoutubeVideoIdFromUrl(string $link, bool $full = true): string
    {
        if($full){
            return '';
        }else{
            $arLink = explode('/', $link);
            return array_pop($arLink);
        }
        return '';
    }

}