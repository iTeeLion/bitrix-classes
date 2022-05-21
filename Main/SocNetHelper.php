<?php


namespace App\Main;

use \App\Main\FormatHelper;

class SocNetHelper
{

    public static function getPhoneLinkWhatApp($phone){
        $phone = FormatHelper::formatPhoneOnlyDigits(FormatHelper::formatPhoneInternational($phone));
        return 'https://api.whatsapp.com/send?phone=+'.$phone;
    }

    public static function getPhoneLinkViber($phone){
        $phone = FormatHelper::formatPhoneOnlyDigits(FormatHelper::formatPhoneInternational($phone));
        return 'viber://chat?number=%2B'.$phone;
    }

    public static function getYoutubeVideoIdFromUrl($link){
        if(false){
            // full link
            return '';
        }elseif(true){
            // short link
            $arLink = explode('/', $link);
            return array_pop($arLink);
        }
        return false;
    }

    public static function getYoutubePreview ($link) {
        $videoId = self::getYoutubeVideoIdFromUrl($link);
        if($videoId){
            $preview = 'https://img.youtube.com/vi/'.$videoId.'/mqdefault.jpg';
            return $preview;
        }
        return false;
    }

}