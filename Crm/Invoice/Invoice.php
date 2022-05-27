<?php

namespace App\Crm\Invoice;

class Invoice
{

    // Set pay date when status changed to "P"
    public function setInvoicePayDate_EventHandler(&$arFields){
        if($arFields['STATUS_ID'] == 'P' && empty($arFields['PAY_VOUCHER_DATE'])){
            $now = date('d.m.Y');
            $arFields['PAY_VOUCHER_DATE'] = $now;
            $arFields['DATE_MARKED'] = $now;
            $arFields["PERSON_TYPE_ID"] = 1;
            $arFields["PAY_SYSTEM_ID"] = 1;
        }
    }

}