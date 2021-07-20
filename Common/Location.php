<?php

namespace App;


class Location
{

    public $currentLocationCookieName = 'CURRENT_LOCATION';
    public $defaultLocation = 'moscow';

    public function getCurrentLocation(){
        global $APPLICATION;

        $location = $APPLICATION->get_cookie($this->currentLocationCookieName);
        if(!$location){
            $location = $this->defaultLocation;
            $this->setCurrentLocation($location);
        }

        return $location;
    }

    public function setCurrentLocation($location){
        global $APPLICATION;

        $APPLICATION->set_cookie($this->currentLocationCookieName, $location);
    }

}