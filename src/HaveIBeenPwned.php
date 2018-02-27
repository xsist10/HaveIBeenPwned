<?php

namespace xsist10\HaveIBeenPwned;

use xsist10\HaveIBeenPwned\Adapter\Adapter;
use xsist10\HaveIBeenPwned\Adapter\FileGetContents;
use xsist10\HaveIBeenPwned\Adapter\Curl;
use xsist10\HaveIBeenPwned\Response\CheckAccountResponse;
use Psr\Log\NullLogger;

class HaveIBeenPwned
{
    private static $base_url = "https://haveibeenpwned.com/api/v2/";
    private static $password_url = "https://api.pwnedpasswords.com/";

    protected $adapter;

    public function __construct(Adapter $adapter = null) {
        $this->adapter = $adapter;
    }

    /**
     * Return the adapter being used to connect to the remote server
     *
     * @return Adapter
     */
    protected function getAdapter() {
        // Backwards compatability as I won't bump the version number for this
        // yet. When I add PHP 7 support I'll bump it and remove this.
        if (!$this->adapter) {
            $this->adapter = new Curl();
            $this->adapter->setLogger(new NullLogger());
        }
        return $this->adapter;
    }

    /**
     * Set a new adapter for the HaveIBeenPwned client
     *
     * @param Adapter $adapter Which adapter to use?
     */
    public function setAdapter(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    protected function get($url) {
        $body = $this->getAdapter()->get(self::$base_url . $url);
        return json_decode($body ? $body : '[]', true);
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

    public function isPasswordCompromised($password) {
        $sha1 = strtoupper(sha1($password));
        $fragment = substr($sha1, 0, 5);

        $body = $this->getAdapter()->get(self::$password_url . "range/" . urlencode($fragment));
        foreach (explode("\n", $body) as $match) {
            $line = explode(":", $match);
            if ($fragment . $line[0] === $sha1) {
                return $line[1];
            }
        }

        return 0;
    }
}