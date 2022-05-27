<?php

namespace App\Main;

class Location
{

    public $currentLocationCookieName = 'CURRENT_LOCATION';
    public $defaultLocation = 'moscow';

    // Get location from cookie
    public function getCurrentLocation(): string
    {
        $location = $GLOBALS['APPLICATION']->get_cookie($this->currentLocationCookieName);
        if(!$location){
            $location = $this->defaultLocation;
            $this->setCurrentLocation($location);
        }
        return (string)$location;
    }

    // Set location to cookie
    public function setCurrentLocation($location): void
    {
        $GLOBALS['APPLICATION']->set_cookie($this->currentLocationCookieName, $location);
    }

}