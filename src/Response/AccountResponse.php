<?php

namespace xsist10\HaveIBeenPwned\Response;

class AccountResponse
{
    private $responseArray;

    public function __construct($responseArray) {
        $this->responseArray = $responseArray;
    }

    public function hasBreaches() {
        return count($this->responseArray) !== 0;
    }

    public function getBreaches() {
        $breaches = [];
        foreach ($this->responseArray as $breach) {
            $breaches [] = new BreachResponse($breach);
        }

        return $breaches;
    }

    public function getDataclasses() {
        $dataClasses = [];
        foreach ($this->responseArray as $breach) {
            $dataClasses[] = new DataClassResponse($breach['DataClasses']);
        }
    
        return $dataClasses;
    }
}
