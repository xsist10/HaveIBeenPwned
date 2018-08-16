<?php

namespace xsist10\HaveIBeenPwned;


use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;

use xsist10\HaveIBeenPwned\Exception\RateLimitExceededException;
use xsist10\HaveIBeenPwned\Exception\InvalidCredentialsException;
use xsist10\HaveIBeenPwned\Exception\UnsupportedException;

use xsist10\HaveIBeenPwned\Response\CheckAccountResponse;
use xsist10\HaveIBeenPwned\Response\AccountResponse;
use xsist10\HaveIBeenPwned\Response\BreachResponse;
use xsist10\HaveIBeenPwned\Response\PasteResponse;
use xsist10\HaveIBeenPwned\Response\DataClassResponse;
use xsist10\HaveIBeenPwned\Response\PasswordResponse;
use Psr\Log\NullLogger;

class HaveIBeenPwned
{
    CONST API_URL = "https://haveibeenpwned.com/api/v2/";
    const API_PASSWORD_URL = "https://api.pwnedpasswords.com/";

    const USER_AGENT = 'xsist10/haveibeenpwned';

    protected $client;
    protected $messageFactory;

    public function __construct(HttpClient $client = null, MessageFactory $messageFactory = null) {
        $this->client = $client;
        $this->messageFactory = $messageFactory;
    }

    /**
     * Return the client being used to connect to manage HTTP communication
     *
     * @return HttpClient
     */
    protected function getHttpClient() {
        if (!$this->client) {
            // Specify the user agent to prevent a "API request must include a user agent" error
            $headerDefaultsPlugin = new HeaderDefaultsPlugin([
                'User-Agent' => self::USER_AGENT
            ]);

            $this->client = new PluginClient(
                HttpClientDiscovery::find(),
                [$headerDefaultsPlugin]
            );
        }
        return $this->client;
    }

    /**
     * Return the factory being used to create HTTP messages
     *
     * @return MessageFactory
     */
    protected function getMessageFactory() {
        if (!$this->messageFactory) {
            $this->messageFactory = MessageFactoryDiscovery::find();
        }
        return $this->messageFactory;
    }

    protected function get($url) {
        $response = $this->getHttpClient()->sendRequest(
            $this->getMessageFactory()->createRequest('GET', $url)
        );

        $statusCode = $response->getStatusCode();
        switch ($response->getStatusCode()) {
            case 401:
                throw new InvalidCredentialsException("Invalid API key specified.");
            case 400:
                throw new RuntimeException("Bad request. Check the URL you specified for errors.");
            case 404:
                throw new RuntimeException("Unknown endpoint specified. Check the URL you specified for errors.");
            case 429:
                $retryAfter = $response->getHeader('Retry-After') ?: "a few";
                throw new RateLimitExceededException("Ratelimit reached. Please try again in $retryAfter seconds.");
            case 503:
                throw new RuntimeException("Service unavailable. It is possible that your client has been throttled.");
        }

        if ($statusCode != 200) {
            throw new RuntimeException("Unknown issue encountered. Server returned status code $statusCode.");
        }
        
        // Cast to a string to materialize streams
        return (string)$response->getBody();
    }

    protected function getJSON($url) {
        $body = $this->get($url);
        return json_decode($body ? $body : '[]', true);
    }

    public function checkAccount($account) {
        return new AccountResponse($this->getJSON(self::API_URL . "breachedaccount/" . urlencode($account)));
    }

    public function getBreaches() {
        $breachArray = [];
        $result = $this->getJSON(self::API_URL . "breaches");
        foreach ($result as $breach) {
            $breachArray[] = new BreachResponse($breach);
        }

        return $breachArray;
    }

    public function getBreach($name) {
        return new BreachResponse($this->getJSON(self::API_URL . "breach/" . urlencode($name)));
    }

    public function getDataClasses() {
        return new DataClassResponse($this->getJSON(self::API_URL . "dataclasses"));
    }

    public function getPasteAccount($account) {
        $pasteArray = [];
        $result = $this->getJSON(self::API_URL . "pasteaccount/" . urlencode($account));
        foreach ($result as $paste) {
            $pasteArray[] = new PasteResponse($paste);
        }

        return $pasteArray;
    }

    public function isPasswordCompromised($password) {
        $sha1 = strtoupper(sha1($password));
        $fragment = substr($sha1, 0, 5);

        $body = $this->get(self::API_PASSWORD_URL . "range/" . urlencode($fragment));
        return new PasswordResponse($body, $password);
    }
}