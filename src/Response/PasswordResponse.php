<?php

namespace xsist10\HaveIBeenPwned\Response;

class PasswordResponse
{
    private $hashList;

    private $password;

    public function __construct($response, $password) {
        foreach (explode("\n", $response) as $match) {
            $line = explode(":", $match);
            $this->hashList[$line[0]] = $line[1];
        }

        $this->password = $password;
    }

    public function isCompromised() {
        return $this->numberOfTimesCompromised() ? true : false;
    }

    public function numberOfTimesCompromised() {
        $sha1 = strtoupper(sha1($this->password));
        $fragment = substr($sha1, 5);
        return !empty($this->hashList[$fragment])
            ? $this->hashList[$fragment]
            : 0;
    }
}