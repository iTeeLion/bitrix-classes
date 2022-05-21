<?php

namespace App\Main;


class Site
{

    public function getContactsInfo($ID){
        global $BXS;
        $arContacts = Array();
        $arOrder = Array('SORT' => 'ASC');
        $arFilter = Array('IBLOCK_ID' => $BXS->IB_CONTACTS, 'ID' => $ID);
        $arGroupBy = false;
        $arNav = false;
        $arSelect = Array('ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_EMAILS', 'PROPERTY_PHONES', 'PROPERTY_ADDRESS_PROD', 'PROPERTY_SOCIAL_VK', 'PROPERTY_SOCIAL_FB');
        $dbRes = \CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNav, $arSelect);
        while ($item = $dbRes->GetNext()) {
            foreach ($item as $key => $val) {
                if ($item[$key . '_ID']) {
                    $arContacts[$item['ID']][$key][$item[$key . '_ID']] = $val;
                } else {
                    $arContacts[$item['ID']][$key] = $val;
                }
            }
        }
        $arRes = array_shift($arContacts);
        return $arRes;
    }

}