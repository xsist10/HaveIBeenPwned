<?php

namespace xsist10\HaveIBeenPwned;

use xsist10\HaveIBeenPwned\Adapter\Adapter;
use xsist10\HaveIBeenPwned\Adapter\FileGetContents;

class HaveIBeenPwned
{
    private static $base_url = "https://haveibeenpwned.com/api/v2/";

    protected $adapter;

    public function __construct(Adapter $adapter = null) {
        $this->adapter = $adapter;
    }

    protected function getAdapter() {
        // Backwards compatability as I won't bump the version number for this
        // yet.
        if (!$this->adapter) {
            $this->adapter = new FileGetContents();
        }
        return $this->adapter;
    }

    public function checkAccount($account) {
        $url = self::$base_url . "breachedaccount/" . urlencode($account);
        return json_decode($this->getAdapter()->get($url), true);
    }

    public function getBreaches() {
        $url = self::$base_url . "breaches";
        return json_decode($this->getAdapter()->get($url), true);
    }

    public function getBreach($name) {
        $url = self::$base_url . "breach/" . urlencode($name);
        return json_decode($this->getAdapter()->get($url), true);
    }

    public function getDataClasses()
    {
        $url = self::$base_url . "dataclasses";
        return json_decode($this->getAdapter()->get($url), true);
    }
}