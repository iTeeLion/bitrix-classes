<?php

namespace App;


class BridgeSaleCrm
{

    private $defaultAssignedById = 86;

    private $arRelationShopAndPortalStages = [
        'N' => 'NEW',
        'P' => 'PREPAYMENT_INVOICE',
        'C' => 'PREPARATION',
        'S' => 'EXECUTING',
        'F' => 'FINAL_INVOICE',
        'D' => 'LOSE',
        'F' => 'WON',
    ];

    private $deliveryProductId = 21714;

    public function getRelationShopAndPortalStages(){
        return $this->arRelationShopAndPortalStages;
    }

    public function OrderDealDeliveryRel($deliveryId) {
        switch ($deliveryId) {
            case '64':
                return 27;
                break;
            default:
                return 26;
                break;
        }
    }

    public function OrderDealPaymentRel($paymentId) {
        switch ($paymentId) {
            case '9':
                return 29;
                break;
            default:
                return 28;
                break;
        }
    }

    /**
     * Main
     *
     * @param $trigger
     * @param $data
     */
    public function UpdateSaleAndCrmRelatedEntities($trigger, $data){
        if(!$GLOBALS['UPDATE_SALE_AND_CRM_RELATED_ENTITIES']){
            $GLOBALS['UPDATE_SALE_AND_CRM_RELATED_ENTITIES'] = true;
            //ob_start(); vardmp($trigger); vardmp($data); $ob=ob_get_clean();
            //file_put_contents($_SERVER["DOCUMENT_ROOT"].'/!CRM_BRIDGE.log', $ob, FILE_APPEND);

            switch($trigger){
                case 'order':
                    $this->UpdateSaleAndCrmRelatedEntities_byOrder($data);
                    break;
                case 'deal':
                    $this->UpdateSaleAndCrmRelatedEntities_byDeal($data);
                    break;
                case 'dealProducts':
                    $this->UpdateSaleAndCrmRelatedEntities_byDealProducts($data);
                    break;
                case 'invoice':
                    $this->UpdateSaleAndCrmRelatedEntities_byInvoice($data);
                    break;
            }

            $GLOBALS['UPDATE_SALE_AND_CRM_RELATED_ENTITIES'] = false;
        }
    }

    /**
     * Update related by order
     *
     * @param $data
     * @throws \Bitrix\Main\LoaderException
     */
    private function UpdateSaleAndCrmRelatedEntities_byOrder($data){
        // Prepare
        \Bitrix\Main\Loader::includeModule('sale');
        \Bitrix\Main\Loader::includeModule('crm');

        $bCheckPermission = false;
        $CCrmDeal = new \CCrmDeal($bCheckPermission);

        // Set data
        $event = $data;
        $order = $event->getParameter("ENTITY");
        $ORT = $order::getRegistryType();

        if($ORT != 'CRM_INVOICE'){
            $orderId = $order->getId();
            $rsUser = \CUser::GetByID($order->getUserId());
            $arUser = $rsUser->Fetch();
            $companyId = $arUser['UF_CRM_COMPANY'];

            // DB search
            $arOrder = ['ID' => 'ASC'];
            $arFilter = ['UF_CRM_1545926256870' => $orderId, 'CHECK_PERMISSIONS' => 'N'];
            $arSelect = ['ID', 'UF_CRM_1545926256870'];
            $dbRes = $CCrmDeal::GetList($arOrder, $arFilter, $arSelect, false);
            while ($item = $dbRes->GetNext()) {
                $dealId = $item['ID'];
            }

            // Payment and delivery to deal
            $propertyCollection = $order->getPropertyCollection();
            $deliveryProp = $propertyCollection->getItemByOrderPropertyId(26);
            if($deliveryProp){
                $deliveryAddr = $deliveryProp->getValue();
            }
            $deliveryProp = $propertyCollection->getItemByOrderPropertyId(38);
            if($deliveryProp){
                $deliveryAddr = $deliveryProp->getValue();
            }

            $paymentIds = $order->getPaymentSystemId();
            $paymentId = array_shift($paymentIds);
            $dealPaymentId = $this->OrderDealPaymentRel($paymentId);

            $deliveryIds = $order->getDeliverySystemId();
            $deliveryId = array_shift($deliveryIds);
            $dealDeliveryId = $this->OrderDealDeliveryRel($deliveryId);

            $deliveryPrice = $order->getDeliveryPrice();

            // DB write
            $arFields = [
                //'TITLE' => 'Сделка #' . $orderId,
                'STAGE_ID' => $this->arRelationShopAndPortalStages[$order->getField("STATUS_ID")],
                'COMPANY_ID' => $companyId,
                'OPPORTUNITY' => $order->getPrice() - $order->getDiscountPrice() - $order->getDeliveryPrice(),
                'UF_CRM_1545926256870' => $orderId, // Order id
                'UF_CRM_1565340990504' => $dealPaymentId, // Payment type
                'UF_CRM_1565340972896' => $dealDeliveryId, // Delivery type
                'UF_CRM_1565343234650' => $deliveryAddr, //Delivery address
            ];
            $bUpdateSearch = true;
            $bCompare = false;
            if ($dealId) {
                $dbRes = $CCrmDeal->Update($dealId, $arFields, $bCompare, $bUpdateSearch, []);
            } else {
                $arFields['ASSIGNED_BY_ID'] = $this->defaultAssignedById;
                $dbRes = $CCrmDeal->Add($arFields, $bUpdateSearch, []);
                $dealId = (int)$dbRes;
            }

            // Update goods in deal
            if($dealId){
                // Get order basket
                $basket = $order->getBasket();
                $arGoodsDeal = $arGoodsInvoice = [];
                foreach ($basket as $basketItem) {
                    $arGood = [
                        'PRODUCT_ID' => $basketItem->getField('PRODUCT_ID'),
                        'PRODUCT_NAME' => $basketItem->getField('NAME'),
                        'PRICE' => $basketItem->getField('PRICE'),
                        'QUANTITY' => $basketItem->getField('QUANTITY'),
                        'TAX_RATE' => 20,
                        'TAX_INCLUDED' => 'Y',
                    ];
                    $arGoodsDeal[] = $arGood;
                    unset($arGood['TAX_RATE']);
                    unset($arGood['TAX_INCLUDED']);
                    $arGood['ID'] = 0;
                    $arGood['VAT_RATE'] = 0.20;
                    $arGood["VAT_INCLUDED"] = 'Y';
                    $arGood['DISCOUNT_PRICE'] = 0;
                    $arGood['CUSTOMIZED'] = 'Y';
                    $arGoodsInvoice[] = $arGood;
                }

                if($deliveryId != 3){
                    $arGood = [
                        'PRODUCT_ID' => $this->deliveryProductId,
                        'PRODUCT_NAME' => 'Доставка',
                        'PRICE' => $deliveryPrice,
                        'QUANTITY' => 1,
                        'TAX_RATE' => 20,
                        'TAX_INCLUDED' => 'Y',
                    ];
                    $arGoodsDeal[] = $arGood;
                    unset($arGood['TAX_RATE']);
                    unset($arGood['TAX_INCLUDED']);
                    $arGood['ID'] = 0;
                    $arGood['VAT_RATE'] = 0.20;
                    $arGood["VAT_INCLUDED"] = 'Y';
                    $arGood['DISCOUNT_PRICE'] = 0;
                    $arGood['CUSTOMIZED'] = 'Y';
                    $arGoodsInvoice[] = $arGood;
                }

                // Prepare deal
                $checkPerms = false;
                $regEvent = true;
                $syncOwner = true;
                $dbResProd = $CCrmDeal::SaveProductRows($dealId, $arGoodsDeal, $checkPerms, $regEvent, $syncOwner);
            }

            // Update invoice
            $updateInvoiceParams = [
                'dealId' => $dealId,
                'orderId' => $orderId,
                'companyId' => $companyId,
                'arGoodsInvoice' => $arGoodsInvoice,
            ];

            if(in_array($order->getField("STATUS_ID"), ['S', 'F'])){
                $updateInvoiceParams['makeshipped'] = true;
            }

            if($order->getField("PAYED") == 'Y'){
                $updateInvoiceParams['status'] = 'P';
            }else{
                if($order->getField("STATUS_ID") == 'D'){
                    $updateInvoiceParams['status'] = 'D';
                }
            }

            $this->UpdateOrderAndDealRelatedInvoice($updateInvoiceParams);
        }
    }

    /**
     * Prepare deal products list for md
     *
     * @param $arProducts
     * @return array
     */
    public function prepareDealProductsToMd($arProducts) {
        $arProductsMD = [];
        foreach ($arProducts as $arProduct) {
            $arProductsMD[] = [
                'ID' => 0,
                'PRODUCT_ID' => $arProduct['PRODUCT_ID'],
                'PRODUCT_NAME' => $arProduct['PRODUCT_NAME'],
                'PRICE' => $arProduct['PRICE'],
                'QUANTITY' => $arProduct['QUANTITY'],
                'VAT_RATE' => 0.20,
                'VAT_INCLUDED' => 'Y',
                'DISCOUNT_PRICE' => 0,
                'CUSTOMIZED' => 'Y',
            ];
        }
        return $arProductsMD;
    }

    /**
     * Update related by Deal
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    private function UpdateSaleAndCrmRelatedEntities_byDeal($arFields){
        // Prepare
        \Bitrix\Main\Loader::includeModule('sale');
        \Bitrix\Main\Loader::includeModule('crm');
        $bCheckPermission = false;
        $CCrmDeal = new \CCrmDeal($bCheckPermission);
        $arDealProducts = $CCrmDeal->LoadProductRows($arFields['ID']);
        $arInvoiceProducts = $this->prepareDealProductsToMd($arDealProducts);

        // Get data
        $arFilter = ['ID' => $arFields['ID'], 'CHECK_PERMISSIONS' => 'N'];
        $arSelect = ['ID', 'STAGE_ID', 'COMPANY_ID', 'UF_CRM_1545926256870'];
        $dbRes = $CCrmDeal::GetList([], $arFilter, $arSelect, false);
        while($item = $dbRes->GetNext()){
            $orderId = $item['UF_CRM_1545926256870'];
        }

        // Update order
        if($orderId) {
            $order = \Bitrix\Sale\Order::load($orderId);
            $statusIdNew = array_search($arFields['STAGE_ID'], $this->arRelationShopAndPortalStages);
            if ($statusIdNew) {
                $order->setField('STATUS_ID', $statusIdNew);
            }
            $order->save();
        }

        // Prepare for invoice
        $updateInvoiceParams = [
            'dealId' => $arFields['ID'],
            'orderId' => $orderId,
            'companyId' => $arFields['COMPANY_ID'],
            'arGoodsInvoice' => $arInvoiceProducts,
        ];
        if($arFields['STAGE_ID'] == 'LOSE'){
            $updateInvoiceParams['status'] = 'D';
        }
        if(in_array($arFields['STAGE_ID'], ['EXECUTING', 'FINAL_INVOICE', 'WON'])) {
            $updateInvoiceParams['makeshipped'] = true;
        }

        $this->UpdateOrderAndDealRelatedInvoice($updateInvoiceParams);

    }

    /**
     * Update related by Deal products
     *
     * @param $arFields
     */
    private function UpdateSaleAndCrmRelatedEntities_byDealProducts($arFields){
        // Prepare
        \Bitrix\Main\Loader::includeModule('sale');
        \Bitrix\Main\Loader::includeModule('crm');
        $bCheckPermission = false;
        $CCrmDeal = new \CCrmDeal($bCheckPermission);

        // Get data
        $arFilter = ['ID' => $arFields['ID'], 'CHECK_PERMISSIONS' => 'N'];
        $arSelect = ['ID', 'STAGE_ID', 'COMPANY_ID', 'UF_CRM_1545926256870'];
        $dbRes = $CCrmDeal::GetList([], $arFilter, $arSelect, false);
        while($item = $dbRes->GetNext()){
            $orderId = $item['UF_CRM_1545926256870'];
            $companyId = $item['COMPANY_ID'];
            $dealId = $item['ID'];
        }

        // Get products
        $arDealProductsSrc = $arFields['PRODUCTS_ROWS'];
        $arDealProducts = [];
        foreach($arDealProductsSrc as $product){
            $arDealProducts[$product['PRODUCT_ID']] = $product;
        }
        unset($arDealProductsSrc);
        $arDealProductsIDs = array_keys($arDealProducts);

        // Update order products list
        if($orderId){
            $order = \Bitrix\Sale\Order::load($orderId);
            $shipmentCollection = $order->getShipmentCollection();
            $basket = $order->getBasket();
            foreach($basket as $product){
                if(!in_array($product->getProductId(), $arDealProductsIDs)){
                    $product->delete();
                }
            }
            foreach($arDealProducts as $product){
                if ($item = $basket->getExistsItem('catalog', $product['PRODUCT_ID'])) {
                    $item->setFields([
                        'QUANTITY' => $product['QUANTITY'],
                    ]);
                } else {
                    $item = $basket->createItem('catalog', $product['PRODUCT_ID']);
                    $item->setFields([
                        'QUANTITY' => $product['QUANTITY'],
                        'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                        'LID' => 's2',
                        'PRODUCT_PROVIDER_CLASS' => \Bitrix\Catalog\Product\Basket::getDefaultProviderName(),
                    ]);
                    $item->save();
                }
            }
            foreach($shipmentCollection as $shipment){
                if (!$shipment->isSystem()){
                    $shipmentItemCollection = $shipment->getShipmentItemCollection();
                    foreach($basket as $basketItem){
                        $shipmentItem = $shipmentItemCollection->createItem($basketItem);
                        $shipmentItem->setQuantity($basketItem->getQuantity());
                    }
                }
            }

            $basket->refreshData();
            $basket->save();
            $shipmentCollection->refreshData();
            $shipmentCollection->save();
            $order->save();
        }

        // Update invoice
        $updateInvoiceParams = [
            'dealId' => $dealId,
            'orderId' => $orderId,
            'companyId' => $companyId,
        ];
        $updateInvoiceParams['arGoodsInvoice'] = $this->prepareDealProductsToMd($arDealProducts);

        $this->UpdateOrderAndDealRelatedInvoice($updateInvoiceParams);
    }



    /**
     * Update related by invoice
     *
     */
    private function UpdateSaleAndCrmRelatedEntities_byInvoice($arFields){
        if($arFields['STATUS_ID']){
            // Get invoice
            $orderIdPropName = 'UF_CRM_1547635648';
            $bCheckPermission = false;
            $CCrmInvoices = new \CCrmInvoice($bCheckPermission);
            $CCrmDeal = new \CCrmDeal($bCheckPermission);

            $arFilter = ['CHECK_PERMISSIONS' => 'N', 'ID' => $arFields['ID']];
            $arSelect = ['ID', 'TITLE', 'STATUS_ID', 'UF_DEAL_ID', $orderIdPropName];
            $dbRes = $CCrmInvoices::GetList([], $arFilter, false, false, $arSelect, []);
            $arInvoice = $dbRes->GetNext();

            // Prepare and save order
            if($arInvoice[$orderIdPropName]){
                $order = \Bitrix\Sale\Order::load($arInvoice[$orderIdPropName]);
                if($order){
                    $paymentCollection = $order->getPaymentCollection();
                    if($arFields['STATUS_ID'] === 'P') {
                        //if(!$order->isPaid()){
                        if($order->getField("STATUS_ID") === 'N'){
                            $order->setField("STATUS_ID", 'P');
                            //$dealStage = 'PREPAYMENT_INVOICE';
                        }
                        $paymentCollection[0]->setPaid("Y");
                    }else{
                        //if($order->isPaid()){
                        if($order->getField("STATUS_ID") === 'P'){
                            $order->setField("STATUS_ID",  'N');
                            //$dealStage = 'NEW';
                        }
                        $paymentCollection[0]->setPaid("N");
                    }
                    $order->save();
                }else{
                    echo 'Invoice ' . $arInvoice[$orderIdPropName] . ' not found<br>';
                }
            }

            // Prepare and save deal
            if($arInvoice['UF_DEAL_ID']){
                $arFilter = [
                    'CHECK_PERMISSIONS' => 'N',
                    'ID' => $arInvoice['UF_DEAL_ID'],
                ];
                $arSelect = ['ID', 'TITLE', 'STAGE_ID'];
                $dbResDeal = $CCrmDeal::GetList(['ID' => 'ASC'], $arFilter, $arSelect, false);
                $arDeal = $dbResDeal->GetNext();
                if($arFields['STATUS_ID'] === 'P') {
                    if($arDeal['STAGE_ID'] === 'NEW'){
                        $arFieldsUpd = [
                            'STAGE_ID' => 'PREPAYMENT_INVOICE',
                        ];
                    }
                }else{
                    if($arDeal['STAGE_ID'] === 'PREPAYMENT_INVOICE'){
                        $arFieldsUpd = [
                            'STAGE_ID' => 'NEW',
                        ];
                    }
                }

                $dbResUpd = $CCrmDeal->Update($arInvoice['UF_DEAL_ID'], $arFieldsUpd, false, false, []);
            }
        }
    }

    /**
     * Update related invoice
     *
     * @param $params
     * @return \Bitrix\Sale\Compatible\CDBResult|bool|int|string
     */
    public function UpdateOrderAndDealRelatedInvoice($params)
    {
        //dealId, orderId, companyId, arGoods
        global $BXS;
        $bCheckPermission = false;
        $CCrmInvoice = new \CCrmInvoice($bCheckPermission);

        // Search invoice
        if($params['orderId']){
            $arFilter = ['UF_CRM_1547635648' => $params['orderId'], 'CHECK_PERMISSIONS' => 'N'];
        }else{
            $arFilter = ['UF_DEAL_ID' => $params['dealId'], 'CHECK_PERMISSIONS' => 'N'];
        }
        $arOrder = ['ID' => 'ASC'];
        $dbRes = $CCrmInvoice::GetList($arOrder, $arFilter, false, false, [], []);
        $invoice = $dbRes->GetNext();
        $invoiceId = (int)$invoice['ID'];

        // Update or create invoice
        if (($params['dealId'] || $params['orderId']) && $params['companyId'] && (count($params['arGoodsInvoice']) > 0)) {
            // Add or update invoice
            $arFields = [
                'PAY_SYSTEM_ID' => 0,
                'PERSON_TYPE_ID' => 1,
                'UF_QUOTE_ID' => 0,
                'UF_CONTACT_ID' => 0,
                'UF_DEAL_ID' => $params['dealId'],
                'UF_COMPANY_ID' => $params['companyId'],
                'UF_MYCOMPANY_ID' => $BXS->CRM_MYCOMPANY_ID,
                'INVOICE_PROPERTIES' => ['', ''],
                'RESPONSIBLE_ID' => $this->defaultAssignedById,
                'PRODUCT_ROWS' => $params['arGoodsInvoice'],
            ];
            if($params['orderId']){
                $arFields['UF_CRM_1547635648'] = $params['orderId'];
            }
            if(in_array($params['status'], ['N', 'P', 'D'])){
                $arFields['STATUS_ID'] = $params['status'];
            }
            $arFields['ORDER_TOPIC'] = 'Сделка #' . $params['dealId'];
            $arRecalculated = false;
            $siteId = 's1';

            if ($invoiceId > 0) {
                $dbRes = $CCrmInvoice->Update($invoiceId, $arFields);
            } else {
                $arFields['ASSIGNED_BY_ID'] = $this->defaultAssignedById;
                $arFields['STATUS_ID'] = 'N';
                $invoiceId = $CCrmInvoice->Add($arFields, $arRecalculated, $siteId);
            }
        }

        // Make shipped - make salewaybill and deduct
        if($invoiceId && $params['makeshipped']){
            if($params['orderId']){
                // Deduct
                \CSaleOrder::DeductOrder($params['orderId'], 'Y');
            }
            // Make UPD
            MoeDeloApi()->makeSaleUpd($params['dealId']);
        }
    }

}