<?php

namespace xsist10\HaveIBeenPwned\Adapter;

use \RuntimeException;
use xsist10\HaveIBeenPwned\Exception\RateLimitExceededException;
use xsist10\HaveIBeenPwned\Exception\InvalidCredentialsException;
use xsist10\HaveIBeenPwned\Exception\UnsupportedException;

use Psr\Log\LoggerAwareTrait;

class Curl implements Adapter
{
    use LoggerAwareTrait;
    
    /**
     * Is this environment have cURL installed and the module enabled in PHP?
     * 
     * @return boolean
     */
    public function isSupported() {
        return function_exists('curl_init');
    }

    /**
     * Build a cURL resource with basic configurations already made
     * 
     * @return resource cURL handle
     */
    private function _createCurlHandle() {

        // Make sure the cURL extension is enabled
        if (!$this->isSupported()) {
            $this->logger->error("cURL extension not found (or not enabled).");
            throw new UnsupportedException('cURL extension not found (or not enabled).');
        }
        $this->logger->info("cURL extension is loaded.");

        // Initialize cURL
        $curl = curl_init();
        // If cURL is still not initialized, then there is a problem
        if (!$curl) {
            $this->logger->error("cURL was unable to initalized successfully.");
            throw new RuntimeException('Unable to create handle for cURL.');
        }
        $this->logger->info("cURL initialised successfully.");

        // Set default cURL configuration
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 3);

        // Ensure we correctly perform peer verification for PHP < 5.6 by using
        // our own CA certificate store (see https://wiki.php.net/rfc/tls-peer-verification)
        // otherwise just fall back to the sane system options for PHP >= 5.6
        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/../../cacert.pem');
            $this->logger->info("Manually specifying CA certificate store.");
        }
        curl_setopt($curl, CURLOPT_USERAGENT, 'xsist10-PHP-client');

        return $curl;
    }

    /**
     * Perform a GET request using a cURL handle
     * 
     * @param  string $url What URL are we requesting from the API?
     * @return string      Returns the body of the response
     */
    public function get($url) {

        $curl = $this->_createCurlHandle();

        // Configure our cURL handle for our GET call
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_POST, 0);
        curl_setopt($curl, CURLOPT_URL, $url);

        // Perform our request and check the response
        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->logger->info("Request returned a $code response.");
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        curl_close($curl);

        // Extract header information from the response
        $headers = [];
        $raw_headers = explode("\n", substr($response, 0, $header_size));
        array_shift($raw_headers);
        foreach ($raw_headers as $header) {
            if (!trim($header)) {
                continue;
            }
            $field = explode(": ", $header);
            $headers[array_shift($field)] = trim(implode(': ', $field));
        }

        // Low level debug output
        foreach ($headers as $header => $value) {
            $this->logger->debug($header . ': ' . $value);
        }
        $body = substr($response, $header_size);
        $this->logger->debug($body);

        // Check our result for unexpected errors
        switch ($code) {
            case 401:
                throw new InvalidCredentialsException("Invalid API key specified.");
            case 400:
                throw new RuntimeException("Bad request. Check the URL you specified for errors.");
            case 404:
                throw new RuntimeException("Unknown endpoint specified. Check the URL you specified for errors.");
            case 429:
                throw new RateLimitExceededException("Ratelimit reached. Please try again in {$headers['Retry-After']} seconds.");
            case 503:
                throw new RuntimeException("Service unavailable. It is possible that your client has been throttled.");
        }

        // Check for unexpected errors
        if ($code != 200 && $code != 204) {
            throw new RuntimeException("Remote server returned an unexpected response: $code");
        }

        $this->logger->info("Response size: " . strlen($body));

        return $body;
    }
}