<?php

namespace App\Exchange1C;

class Test extends Exchange
{

    private $exchangeType = 'products';
    private $exchangePathFrom1c;
    private $exchangePathTo1c;
    private $exchangePathTo1cLatest;

    public function __construct(string $exchangePath, $params = [])
    {
        $this->exchangePathFrom1c = $exchangePath . '/from1c/' . $this->exchangeType . '/';
        $this->createFolder($this->exchangePathFrom1c);
        $this->exchangePathTo1c = $exchangePath . '/to1c/' . $this->exchangeType . '/';
        $this->createFolder($this->exchangePathTo1c);
        if(isset($params['saveLatest'])){
            $this->exchangePathLatest = $exchangePath . '/to1c/latest/' . $this->exchangeType . '/';
            $this->createFolder($this->exchangePathTo1cLatest);
        }
    }

    public function from1c(){
        // ToDo...
    }

    public function to1c(){
        // ToDo...
    }

}