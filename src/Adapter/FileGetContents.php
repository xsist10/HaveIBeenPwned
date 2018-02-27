<?php

namespace xsist10\HaveIBeenPwned\Adapter;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

use xsist10\HaveIBeenPwned\Exception\UnsupportedException;
use \RuntimeException;

class FileGetContents implements Adapter
{
    use LoggerAwareTrait;

    public function __construct() {
        $this->setLogger(new NullLogger());
    }

    /**
     * Does this environment support URL opening with the file handler
     * functions?
     * 
     * @return boolean
     */
    public function isSupported() {
        return ini_get('allow_url_fopen');
    }

    /**
     * Perform a GET request using a file handle
     * 
     * @param  string $url What URL are we requesting from the API?
     * @return string      Returns the body of the response
     */
    public function get($url) {
        // Ensure we have the allow_url_fopen directive enabled
        if (!$this->isSupported()) {
            $this->logger->error("allow_url_fopen is disabled.");
            throw new UnsupportedException('allow_url_fopen disabled.');
        }
        $this->logger->info("allow_url_fopen is enabled.");

        $context = stream_context_create(array(
            'http' => array(
                'user_agent'          => 'xsist10-PHP-client'
            ),
            'ssl' => array(
                'method'              => 'GET',
                'verify_peer'         => true,
                'verify_depth'        => 5,
                // Manually specify CA certificate store and CN_match requirement
                'cafile'              => __DIR__ . '/../../cacert.pem',
                'CN_match'            => 'www.haveibeenpwned.com',
                // Disabled to prevent CRIME/BEAST attacks
                // https://en.wikipedia.org/wiki/BEAST_attack#CRIME_and_BREACH_attacks
                'disable_compression' => true
            )
        ));
        
        return @file_get_contents($url, false, $context);
    }
}