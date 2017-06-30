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
        // yet. When I add PHP 7 support I'll bump it and remove this.
        if (!$this->adapter) {
            $this->adapter = new FileGetContents();
        }
        return $this->adapter;
    }

    protected function get($url) {
        //echo self::$base_url . $url . "\n";
        return json_decode($this->getAdapter()->get(self::$base_url . $url), true);
    }

    public function checkAccount($account) {
        return $this->get("breachedaccount/" . urlencode($account));
    }

    public function getBreaches() {
        return $this->get("breaches");
    }

    public function getBreach($name) {
        return $this->get("breach/" . urlencode($name));
    }

    public function getDataClasses() {
        return $this->get("dataclasses");
    }

    public function getPasteAccount($account) {
        return $this->get("pasteaccount/" . urlencode($account));
    }
}