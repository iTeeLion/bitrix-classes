<?php
/*
 *  Класс для работы с магазином bitrix
 */

namespace App;

class Sale
{

    public function getCurrentItemQuantity(){
        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
            \Bitrix\Sale\Fuser::getId(),
            \Bitrix\Main\Context::getCurrent()->getSite()
        );

        $basketItems = $basket->getBasketItems();
        foreach($basketItems as $item){
            $qtys[$item->getProductId()] = $item->getQuantity();
        }

        return $qtys;
    }

    public function getProductOrdersCountByUser($userId, $productId, $successOrderedStatuses = ['F']){
        \Bitrix\Main\Loader::includeModule('sale');

        $fUserId = \Bitrix\Sale\Fuser::getIdByUserId($userId);

        try {
            $dbRes = \Bitrix\Sale\Basket::getList([
                'filter' => [
                    'FUSER_ID' => $fUserId,
                    'PRODUCT_ID' => $productId,
                    'ORDER.STATUS_ID' => $successOrderedStatuses,
                ],
                'select' => [
                    'ID',
                    'ORDER_ID',
                ],
                'runtime' => [
                    'ORDER' => [
                        'data_type' => '\Bitrix\Sale\Order',
                        'reference' => [
                            '=this.ORDER_ID' => 'ref.ID',
                        ],
                        'join_type' => 'left',
                    ],
                ],
            ]);
            $basketItems = $dbRes->fetchAll();
            return count($basketItems);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function addToBasket($productId, $qty){
        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
            \Bitrix\Sale\Fuser::getId(),
            \Bitrix\Main\Context::getCurrent()->getSite()
        );

        if ($item = $basket->getExistsItem('catalog', $productId)){
            $item->setField('QUANTITY', $item->getQuantity() + $qty);
        }else{
            $item = $basket->createItem('catalog', $productId);
            $item->setFields([
                'QUANTITY' => $qty,
                'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                'LID' => \Bitrix\Main\Context::getCurrent()->getSite(),
                'PRODUCT_PROVIDER_CLASS' => \Bitrix\Catalog\Product\Basket::getDefaultProviderName() ,
            ]);
        }

        $basket->save();
    }

}