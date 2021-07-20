<?php


namespace App;


class CatalogPrice
{

    public function savePrice($catalogGroupId, $price, $productId, $currency = 'RUB')
    {

        if (!$catalogGroupId || !$productId || !$currency) {
            return array("Неверно заданы параметры");
        }

        $rsP = \Bitrix\Catalog\PriceTable::getList(array(
            'filter' => array('CATALOG_GROUP_ID' => $catalogGroupId, 'PRODUCT_ID' => $productId),
        ));

        if ($arP = $rsP->fetch()) {
            if ($price) {
                $result = \Bitrix\Catalog\PriceTable::update($arP['ID'], array(
                    'PRICE' => $price,
                    'PRICE_SCALE' => $price,
                    'CURRENCY' => $currency,
                ));
            } else {
                $result = \Bitrix\Catalog\PriceTable::delete($arP['ID']);
            }
        } else {
            $result = \Bitrix\Catalog\PriceTable::add(array(
                'CATALOG_GROUP_ID' => $catalogGroupId,
                'PRODUCT_ID' => $productId,
                'PRICE' => $price,
                'PRICE_SCALE' => $price,
                'CURRENCY' => $currency,
            ));
        }

        if ($result->isSuccess()) {
            return true;
        } else {
            return $result->getErrorMessages();
        }
    }


}