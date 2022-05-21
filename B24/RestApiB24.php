<?php

namespace App\B24;

class RestApiB24
{

    protected $hookUrl;

    public function __construct($hookUrl)
    {
        $this->hookUrl = $hookUrl;
    }

    public function makeRequest(string $method, string $url, $arData = [], array $headers = []): array
    {
        $dataJson = json_encode($arData);
        $arRes = $this->sendRequest($method, $url, $dataJson, $headers);
        if($arRes['error']){
            return $arRes;
        }
        $arRes['data'] = json_decode($arRes['data'], true);
        return $arRes;
    }

    public function sendRequest(string $method, string $url, $data = [], array $headers = []): array
    {
        if ($method == 'GET') {
            $dataString = http_build_query($data);
            $url = $url . '?' . $dataString;
        }

        $CURL = curl_init($this->hookUrl . $url);
        curl_setopt($CURL, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($CURL, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($CURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($CURL, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($CURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($CURL, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');

        $headers[] = 'Content-Type: application/json';
        if (is_array($headers)) {
            curl_setopt($CURL, CURLOPT_HTTPHEADER, $headers);
        }
        if (!empty($data)) {
            if (is_array($data)) {
                curl_setopt($CURL, CURLOPT_POSTFIELDS, http_build_query($data));
            } else {
                curl_setopt($CURL, CURLOPT_POSTFIELDS, $data);
            }
        }

        $arResult = [
            'data' => curl_exec($CURL),
            'info' => curl_getinfo($CURL),
            'error' => curl_error($CURL),
        ];

        return $arResult;
    }

}
