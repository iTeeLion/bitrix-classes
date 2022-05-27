<?php

namespace App\DocGen;

class DocGen
{

    // Get last generated document
    public function getLastDocument($templateId, $documnetId, $docDataProvider, $arOptions, $docFormat = '') 
    {
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