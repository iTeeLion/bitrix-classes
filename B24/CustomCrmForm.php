<?php

/*
USAGE EXAMPLE: https://b24.site.zone/pub/form/1/q1w2e3/

$arFormFields = [
    'LEAD_NAME' => 'Ivan',
    'LEAD_LAST_NAME' => 'Ivanov',
    'LEAD_PHONE' => '+79998887766',
    'LEAD_EMAIL' => 'test@test.test',
    'LEAD_COMMENTS' => 'some text info',
];
$CustomCrmFormB24 = new \App\B24\CustomCrmForm('b24.site.zone');
$res = $CustomCrmFormB24->sendForm(1, 'q1w2e3', $arFormFields);
*/

namespace App\B24;

class CustomCrmForm
{

    protected string $proto;
    protected string $host;

    public function __construct(string $host, string $proto = 'https')
    {
        $this->proto = $proto;
        $this->host = $host;
    }

    public function sendRequest(string $url, array $arData = [])
    {
        $CURL = curl_init($url);
        curl_setopt($CURL, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($CURL, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($CURL, CURLOPT_RETURNTRANSFER, true);
        if($this->proto == 'https'){
            curl_setopt($CURL, CURLOPT_SSL_VERIFYPEER, true);
        }
        curl_setopt($CURL, CURLOPT_POSTFIELDS, $arData);

        return curl_exec($CURL);
    }

    function sendForm(int $id, string $sec, array $arFormFields): array
    {
        try {
            $url = $this->proto . '://' . $this->host . '/bitrix/services/main/ajax.php?action=crm.site.form.fill';
            $arRequestData = [
                'id' => $id,
                'sec' => $sec,
                'values' => json_encode($arFormFields),
            ];
            $response = $this->sendRequest($url, $arRequestData);

            $arResponse = json_decode($response, true);
            if($arResponse){
                if(isset($arResponse['result']['resultId']) && (int)$arResponse['result']['resultId'] > 0){
                    $arResult = [
                        'success' => true,
                    ];
                }else{
                    $arResult = [
                        'success' => false,
                        'error' => 'Wring response data (resultId incorrect)',
                        'response' => $response,
                    ];
                }
            }else{
                $arResult = [
                    'success' => false,
                    'error' => 'Can`t parse response JSON',
                    'response' => $response,
                ];
            }
        } catch (\Exception $e) {
            $arResult = [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage(),
            ];
        }

        return $arResult;
    }

}