<?php

namespace xsist10\HaveIBeenPwned\Response;

class DataClassResponse
{
    private $dataClass;

    public function __construct($dataClass) {
        $this->dataClass = $dataClass;
    }

    public function getDataClasses() {
        return $this->dataClass;
    }

    public function __toString() {
        return implode(", ", $this->dataClass);
    }
}
