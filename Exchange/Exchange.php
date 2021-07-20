<?php

namespace App\Exchange1C;

class Exchange
{

    private $commonExchangePath;

    public function setCommonPath($path)
    {
        $this->commonExchangePath = $path;
    }

    public function getCommonPath()
    {
        return $this->commonExchangePath;
    }

    public function toBxLog($type, $item, $description)
    {
        \CEventLog::Add([
            "SEVERITY" => "SECURITY",
            "AUDIT_TYPE_ID" => $type,
            "MODULE_ID" => "EXCHANGE",
            "ITEM_ID" => $item,
            "DESCRIPTION" => $description,
        ]);
    }

    public function logRow($str)
    {
        return '[' . date('Y-m-d H:i:s') . '] ' . $str . '<br>' . PHP_EOL;
    }

    public function printLogRow($str)
    {
        echo $this->logRow($str);
    }

    public function createFolder($path)
    {
        if (!mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }

    public function array2xml($data, &$xml_data)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item';
            }
            if (is_array($value)) {
                $subnode = $xml_data->addChild($key);
                $this->array2xml($value, $subnode);
            } else {
                $xml_data->addChild("$key", ("$value"));
            }
        }
    }

}