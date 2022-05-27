<?php

namespace App\Main;

class IBlock
{

    // IBLOCK getlist D7 style throught legacy implementation
    public function getList(array $arParams): array
    {
        $arElements = [];

        if(isset($arParams['order'])){
            $arOrder = $arParams['order'];
        }else{
            $arOrder=[];
        }
        if(isset($arParams['filter'])){
            $arFilter = $arParams['filter'];
        }else{
            $arFilter=[];
        }
        if(isset($arParams['select'])){
            $arSelect = $arParams['select'];
        }else{
            $arSelect = ['*', 'PROPERTY_*'];
        }
        if(isset($arParams['group_by'])){
            $arGroupBy = $arParams['group_by'];
        }else{
            $arGroupBy = false;
        }
        if(isset($arParams['nav'])){
            $arNav = $arParams['nav'];
        }else{
            $arNav = false;
        }

        $arSelect[] = 'ID';
        $CIBlockElement = new \CIBlockElement();
        $dbRes = $CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNav, $arSelect);
        while ($item = $dbRes->GetNextElement()) {
            $arElement = $item->GetFields();
            $arElement['PROPS'] = $item->GetProperties();
            foreach($arElement['PROPS'] as $k => $v){
                $arElement['PROPERTY_' . $k . '_VALUE'] = $v['VALUE'];
                if($v['ENUM_ID']){
                    $arElement['PROPERTY_' . $k . '_ENUM_ID'] = $v['ENUM_ID'];
                }
            }
            $arElements[$arElement['ID']] = $arElement;
        }

        return $arElements;
    }

}
