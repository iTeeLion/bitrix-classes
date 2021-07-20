<?php


namespace App;


class UtilsSocial
{

    public function getPhoneLinkWhatApp($phone){
        $U = new \App\Utils();
        $phone = $U->formatPhoneOnlyDigits($U->formatPhoneInternational($phone));
        return 'https://api.whatsapp.com/send?phone=+'.$phone;
    }

    public function getPhoneLinkViber($phone){
        $U = new \App\Utils();
        $phone = $U->formatPhoneOnlyDigits($U->formatPhoneInternational($phone));
        return 'viber://chat?number=%2B'.$phone;
    }

    public function getYoutubeVideoIdFromUrl($link){
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

    public function getYoutubePreview ($link) {
        $videoId = $this->getYoutubeVideoIdFromUrl($link);
        if($videoId){
            $preview = 'https://img.youtube.com/vi/'.$videoId.'/mqdefault.jpg';
            return $preview;
        }
        return false;
    }

}