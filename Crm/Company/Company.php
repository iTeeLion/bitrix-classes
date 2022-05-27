<?php

namespace App\Crm\Company;

class Company
{

    // Get companies list to select use
    function getCompaniesSelectList(){
        $CCrmCompany = new \CCrmCompany();
        $arOrder = ['ID' => 'DESC'];
        $arSelect = ['ID', 'TITLE'];
        $dbRes = $CCrmCompany::GetList($arOrder, [], $arSelect);
        $arCompanies = [];
        while($item = $dbRes->GetNext()) {
            $arCompanies[$item['ID']] = '[' . $item['ID'] . '] ' . $item['TITLE'];
        }
        return $arCompanies;
    }

    // Get companies by ids array
    function getCompaniesInfoById($arCompaniesIDs){
        $CCrmCompany = new \CCrmCompany();
        if(!is_array($arCompaniesIDs)){ $arCompaniesIDs = [$arCompaniesIDs]; }
        $res = [];
        $arOrder = ['ID'=>'DESC'];
        $arFilter = ['ID'=>$arCompaniesIDs];
        $arSelect = ['ID', 'TITLE', 'UF_*'];
        $dbRes = $CCrmCompany::GetList($arOrder, $arFilter, $arSelect);
        while($item = $dbRes->GetNext()) {
            $res[$item['ID']] = $item;
        }
        return $res;
    }

}