<?php

namespace xsist10\HaveIBeenPwned\Tests;

use Http\Mock\Client as MockClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Strategy\MockClientStrategy;

use xsist10\HaveIBeenPwned\HaveIBeenPwned;
use xsist10\HaveIBeenPwned\Response\AccountResponse;
use xsist10\HaveIBeenPwned\Response\PasteResponse;
use xsist10\HaveIBeenPwned\Response\BreachResponse;
use xsist10\HaveIBeenPwned\Response\DataClassResponse;
use Psr\Log\NullLogger;
use PHPUnit\Framework\TestCase;

use xsist10\HaveIBeenPwned\Tests\DummyAdapter;
use xsist10\HaveIBeenPwned\Tests\DummyClientStrategy;

class HaveIBeenPwnedTest extends TestCase
{
    protected $client;

    public function setUp() {
        HttpClientDiscovery::prependStrategy(DummyClientStrategy::class);
        $this->client = new HaveIBeenPwned();
    }

    /**
     * Test that the getCheckAccount returns the expected result
     */
    public function testCheckAccount()
    {
        $result = $this->client->checkAccount("test@example.com");
        $dataClass = $result->getDataClasses()[0];
        $this->assertTrue($result->hasBreaches());
        $this->assertEquals("Dropbox", $result->getBreaches()[0]->getName());
        $this->assertInstanceOf(DataClassResponse::class, $dataClass);
        $this->assertEquals(['Email addresses', 'Passwords'], $dataClass->getDataClasses());
    }

    /**
     * Test that the getPasteAccount returns the expected result
     */
    public function testGetPasteAccount()
    {
        $result = $this->client->getPasteAccount("test@example.com");
        $pasteResponse = $result[0];
        $this->assertInternalType('array', $result);
        $this->assertInstanceOf(PasteResponse::class, $pasteResponse);
        $this->assertCount(1, $result);
        $this->assertEquals("KsXNChr2", $pasteResponse->getId());
    }

    /**
     * Test that the getBreaches returns the expected result
     */
    public function testGetBreaches()
    {
        $result = $this->client->getBreaches();
        $breachResponse = $result[0];
        $this->assertInternalType('array', $result);
        $this->assertInstanceOf(BreachResponse::class, $breachResponse);
        $this->assertCount(1, $result);
        $this->assertEquals("000webhost", $breachResponse->getTitle());
    }

    /**
     * Test that the getBreach returns a single result
     */
    public function testGetBreach()
    {
        $result = $this->client->getBreach("000webhost");
        $this->assertInstanceOf(BreachResponse::class, $result);
        $this->assertEquals("000webhost", $result->getTitle());
    }

    public function testGetDataClasses()
    {
        $result = $this->client->getDataClasses();
        $this->assertInstanceOf(DataClassResponse::class, $result);
        $this->assertInternalType('array', $result->getDataClasses());
        $this->assertCount(97, $result->getDataClasses());
    }

    public function testIsPasswordCompromised()
    {
        $result = $this->client->isPasswordCompromised("12345");
        $this->assertInternalType('numeric', $result->getPassword());
        $this->assertEquals(2088998, $result->getPassword());
    }
}
