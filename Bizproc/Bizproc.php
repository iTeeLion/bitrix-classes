<?php

namespace App\B24;

class Bizproc
{

    public function getWorkflows($iblockId, $documentId)
    {
        \Bitrix\Main\Loader::includeModule('crm');
        \Bitrix\Main\Loader::includeModule('bizproc');
        \Bitrix\Main\Loader::includeModule('workflow');

        $docType = \BizProcDocument::generateDocumentComplexType("bitrix_processes", $iblockId);
        $docId = \BizProcDocument::getDocumentComplexId("bitrix_processes", $documentId);
        return \CBPDocument::GetDocumentStates($docType, $docId);
    }

    function addWorkflow($documentId, $templateId)
    {
        \CModule::IncludeModule("bizproc");

        $workflowId = \CBPDocument::StartWorkflow(c, ["lists", "BizprocDocument", $documentId], [], $arErrorsTmp);
        return ['workflowId' => $workflowId, 'errors' => $arErrorsTmp];
    }

    function deleteWorkflow($workflowId, $iblockType, $itemId)
    {
        \CModule::IncludeModule("lists");
        \CModule::IncludeModule("bizproc");

        $documentID = \BizprocDocument::getDocumentComplexId($iblockType, $itemId);
        $err = \CBPDocument::killWorkflow($workflowId, '', $documentID);
        if(!$err){
            \CBPStateService::DeleteWorkflow($workflowId);
            \CBPHistoryService::DeleteByDocument($documentID);
            return true;
        }else{
            return $err;
        }
    }

    function startWorkflow($docID, $tmpID)
    {
        CModule::IncludeModule("bizproc");
        $CBPDocument = new \CBPDocument();
        $wfID = $CBPDocument::StartWorkflow($tmpID, ["lists", "BizprocDocument", $docID], [], []);
        return $wfID;
    }

    public function getWorkflowTasks($iblockId, $documentId, $userId)
    {
        require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
        \Bitrix\Main\Loader::includeModule('crm');
        \Bitrix\Main\Loader::includeModule('lists');
        \Bitrix\Main\Loader::includeModule('bizproc');
        \Bitrix\Main\Loader::includeModule('workflow');

        $totalTasks = [];
        $docType = \BizProcDocument::generateDocumentComplexType("bitrix_processes", $iblockId);
        $docId = \BizProcDocument::getDocumentComplexId("bitrix_processes", $documentId);
        $workflows = \CBPDocument::GetDocumentStates($docType, $docId);
        foreach ($workflows as $wfid => $workflow) {
            $tasks = \CBPDocument::GetUserTasksForWorkflow($userId, $wfid);
            $totalTasks = array_merge($totalTasks, $tasks);
        }
        return $totalTasks;
    }

    public function getWorkflowTaskLink($iblockId, $documentId, $userId)
    {
        $tasks = $this->getWorkflowTasks($iblockId, $documentId, $userId);
        $task = array_shift($tasks);
        if($task['ID']){
            $link = '/company/personal/bizproc/' . $task['ID'] . '/';
        } else {
            $link = false;
        }
        return $link;
    }

    public function getLinkToTaskOrWorkFlow($iblockId, $documentId, $userId)
    {
        $link = $this->getWorkflowTaskLink($iblockId, $documentId, $userId);
        if (!$link) {
            $link = '/bizproc/processes/' . $iblockId . '/element/0/' . $documentId . '/';
        }
        return $link;
    }

}
