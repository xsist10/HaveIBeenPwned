<?php

namespace xsist10\HaveIBeenPwned\Response;

class BreachResponse
{
    private $breach;

    public function __construct($breach) {
        $this->breach = $breach;
    }

    public function getName() {
        return $this->breach['Name'];
    }

    public function getTitle() {
        return $this->breach['Title'];
    }

    public function getDomain() {
        return $this->breach['Domain'];
    }

    public function getBreachDate() {
        return $this->breach['BreachDate'];
    }

    public function getAddedDate() {
        return $this->breach['AddedDate'];
    }

    public function getModifiedDate() {
        return $this->breach['ModifiedDate'];
    }

    public function getPwnCount() {
        return $this->breach['PwnCount'];
    }

    public function getDescription() {
        return $this->breach['Description'];
    }

    public function getIsVerified() {
        return $this->breach['IsVerified'];
    }

    public function getIsFabricated() {
        return $this->breach['IsFabricated'];
    }

    public function getIsSensitive() {
        return $this->breach['IsSensitive'];
    }

    public function getIsActive() {
        return $this->breach['IsActive'];
    }

    public function getIsRetired() {
        return $this->breach['IsRetired'];
    }

    public function getIsSpamList() {
        return $this->breach['IsSpamList'];
    }

    public function getLogoType() {
        return $this->breach['LogoType'];
    }

    public function getDataClasses() {
        return new DataClassResponse($this->breach['DataClasses']);
    }
}
