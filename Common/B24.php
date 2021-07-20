<?php

namespace App;


class Portal
{

    public function addStreamPost($title, $detail, $arFields = [])
    {
        global $USER;
        \Bitrix\Main\Loader::includeModule('blog');
        $CBlog = new \CBlog();
        $CBlogPost = new \CBlogPost();
        if((int)$arFields['FROM_USER'] > 0){
            $fromUser = $arFields['FROM_USER'];
        }else{
            $fromUser = $USER->GetID();
        }
        $now = date('d.m.Y H:i:s');

        $arBlog = $CBlog::GetByOwnerID($fromUser);
        if(count($arBlog) > 0){
            $arFieldsQ = array(
                'BLOG_ID' => $arBlog['ID'],
                'AUTHOR_ID' => $fromUser,
                'DATE_PUBLISH' => $now,
                'DATE_CREATE' => $now,
                'PUBLISH_STATUS' => 'P',
                'SOCNET_RIGHTS' => ['UA'], // Usage: 'U123', 'G123'
                'PERMS_POST' => ['U'.$fromUser],
                //'PERMS_COMMENT' => [],
                'PATH' => '/company/personal/user/' . $arBlog['ID'] . '/blog/#post_id#/',
            );
            foreach($arFields as $f => $v){
                $arFieldsQ[$f] = $v;
            }
            $arFieldsQ['TITLE'] = $title;
            $arFieldsQ['DETAIL_TEXT'] = $detail;
            $postId = $CBlogPost->Add($arFieldsQ);

            if((int)$postId > 0){
                $arFieldsQ["ID"] = $postId;
                $arParamsNotify = Array(
                    "bSoNet" => true,
                    "UserID" => $fromUser,
                    "user_id" => $fromUser,
                );
                $CBlogPost::Notify($arFieldsQ, array(), $arParamsNotify);

                if($arFields['IM_NOTIFY'] === true){
                    foreach($arFieldsQ['SOCNET_RIGHTS'] as $sr){
                        if($sr[0] === 'U'){
                            $id = preg_replace("/[^0-9]/", '', $sr);
                            $this->sendImMsg($id, $arFieldsQ['TITLE'], ['FROM_USER_ID' => $fromUser]);
                        }
                    }
                }
            }
        }
    }

    public function sendImMsg(int $msgTo, $msgText, $arFields)
    {
        global $USER;
        \Bitrix\Main\Loader::includeModule('im');
        if((int)$arFields['FROM_USER_ID'] > 0){
            $fromUser = (int)$arFields['FROM_USER_ID'];
        }else{
            $fromUser = $USER->GetID();
        }

        $arMessageFields = Array(
            'system' => 'N',
            'FROM_USER_ID' => $fromUser,
            'TO_USER_ID' => $msgTo,
            'MESSAGE'  => $msgText,
        );
        \CIMNotify::Add($arMessageFields);
    }

}