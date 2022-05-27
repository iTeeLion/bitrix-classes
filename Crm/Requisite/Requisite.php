<?php

namespace App\Crm\Requisite;

class Requisite
{

    // Get string from address array
    function address2string($arAddressSrc, $additional = [])
    {
        // Sort
        if($arAddressSrc['POSTAL_CODE']){
            $arAddress['POSTAL_CODE'] = $arAddressSrc['POSTAL_CODE'];
        }
        if($arAddressSrc['COUNTRY']){
            $arAddress['COUNTRY'] = $arAddressSrc['COUNTRY'];
        }
        if($arAddressSrc['PROVINCE']){
            $arAddress['PROVINCE'] = $arAddressSrc['PROVINCE'];
        }
        if($arAddressSrc['REGION']){
            $arAddress['REGION'] = $arAddressSrc['REGION'];
        }
        if($arAddressSrc['CITY']){
            $arAddress['CITY'] = $arAddressSrc['CITY'];
        }
        if($arAddressSrc['ADDRESS_1']){
            $arAddress['ADDRESS_1'] = $arAddressSrc['ADDRESS_1'];
        }
        if($arAddressSrc['ADDRESS_2']){
            $arAddress['ADDRESS_2'] = $arAddressSrc['ADDRESS_2'];
        }

        // Prepare additional
        if(!isset($additional['PROVINCE'])){
            $additional['PROVINCE'] = ' обл.';
        }
        if(!isset($additional['REGION'])){
            $additional['REGION'] = ' район';
        }
        if(!isset($additional['CITY'])){
            $additional['CITY'] = ' г.';
        }

        // Additional text
        if($arAddress['PROVINCE']){
            $arAddress['PROVINCE'] = $arAddress['PROVINCE'] . $additional['PROVINCE'];
        }
        if($arAddress['REGION']){
            $arAddress['REGION'] = $arAddress['REGION'] . $additional['REGION'];
        }
        if($arAddress['CITY']){
            $arAddress['CITY'] = $arAddress['CITY'] . $additional['CITY'];
        }

        return implode(", ", $arAddress);
    }

}