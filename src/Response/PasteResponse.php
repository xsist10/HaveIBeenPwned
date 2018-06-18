<?php

namespace xsist10\HaveIBeenPwned\Response;

class PasteResponse
{
    private $pasteArray;

    public function __construct($pasteArray) {
        $this->pasteArray = $pasteArray;
    }

    public function getSource() {
        return $this->pasteArray['Source'];
    }

    public function getId() {
        return $this->pasteArray['Id'];
    }

    public function getTitle() {
        return $this->pasteArray['Title'];
    }

    public function getDate() {
        return $this->pasteArray['Date'];
    }

    public function getEmailCount() {
        return $this->pasteArray['EmailCount'];
    }
}
