<?php

namespace App;


class Crm
{

    //--------------------------------------------------
    //
    // CRM Company
    //
    //--------------------------------------------------

    /*
     *  Получить список всех компаний crm в виде: "id => name"
     */
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

    /**
     * Получить информацию о компаниях/компании по ID
     *
     * @param $arCompaniesIDs
     * @return array
     */
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

    //--------------------------------------------------
    //
    // CRM Invoices
    //
    //--------------------------------------------------

    /**
     * Установить дату оплаты счета автоматически при смене статуса на P
     *
     * Для событий: OnBeforeCrmInvoiceAdd / OnBeforeCrmInvoiceUpdate / OnBeforeCrmInvoiceSetStatus
     *
     * @param $arFields
     */
    function setInvoicePayDate_Event(&$arFields){
        if($arFields['STATUS_ID'] == 'P' && empty($arFields['PAY_VOUCHER_DATE'])){
            $now = date('d.m.Y');
            $arFields['PAY_VOUCHER_DATE'] = $now;
            $arFields['DATE_MARKED'] = $now;
            $arFields["PERSON_TYPE_ID"] = 1;
            $arFields["PAY_SYSTEM_ID"] = 1;
        }
    }

    //--------------------------------------------------
    //
    // CRM RQ
    //
    //--------------------------------------------------

    /**
     * Преобразование массива адреса в корректную строчку
     */
    function rqAddrToStr($arAddressSrc, $additional = []){
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

    //--------------------------------------------------
    //
    // CRM Business process
    //
    //--------------------------------------------------

    /**
     * Запустить бизнес процесс по ID документа и ID шаблона
     *
     * @param $docID
     * @param $tmpID
     * @return mixed
     */
    function bpStartWorkflow($docID, $tmpID){
        CModule::IncludeModule("bizproc");
        $CBPDocument = new \CBPDocument();
        $wfID = $CBPDocument::StartWorkflow($tmpID, ["lists", "BizprocDocument", $docID], [], []);
        return $wfID;
    }

    /*
     *  DocGen
     */
    public function getLastDocument($templateId, $documnetId, $docDataProvider, $arOptions, $docFormat = '') {
        \Bitrix\Main\Loader::includeModule('documentgenerator');

        if($arOptions['docFormat']){ $docFormat = $arOptions['docFormat']; }
        $documentTable = new \Bitrix\DocumentGenerator\Model\DocumentTable();
        $arFilter = ['TEMPLATE_ID' => $templateId, 'VALUE' => $documnetId, '=PROVIDER' => mb_strtolower($docDataProvider)];
        if($arOptions['filter']){
            $arFilter = array_merge($arFilter, $arOptions['filter']);
        }

        $dbRes = $documentTable::getList([
            'order' => ['ID' => 'DESC'],
            'filter' => $arFilter,
        ]);
        $item = $dbRes->fetch();

        if($item && false){
            $docgenDoc = \Bitrix\DocumentGenerator\Document::loadById($item['ID']);
        }else{
            $docgenTpl = \Bitrix\DocumentGenerator\Template::loadById($templateId);
            $docgenTpl->setSourceType($docDataProvider);
            $docgenDoc = \Bitrix\DocumentGenerator\Document::createByTemplate($docgenTpl, $documnetId);
            if(is_array($arOptions['docFields'])){
                $docgenDoc->setFields($arOptions['docFields']);
            }
            if(is_array($arOptions['docValues'])){
                $docgenDoc->setValues($arOptions['docValues']);
            }
            $docgenDoc->getFile();
        }

        switch($docFormat){
            case 'docx':
                return $docgenDoc->getDownloadUrl();
                break;
            case 'pdf':
                return $docgenDoc->getPdfUrl();
                break;
            case 'img':
                return $docgenDoc->getImageUrl();
                break;
            default:
                return $docgenDoc;
                break;
        }
    }

}