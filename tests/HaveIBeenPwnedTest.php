<?php

namespace xsist10\HaveIBeenPwned;

use xsist10\HaveIBeenPwned\Adapter\Adapter;
use xsist10\HaveIBeenPwned\HaveIBeenPwned;

class DummyAdapter implements Adapter
{
    public function get($url) {
        switch ($url) {
            case "https://haveibeenpwned.com/api/v2/breachedaccount/test%40example.com":
                return <<<EOT
[{"Title":"Dropbox","Name":"Dropbox","Domain":"dropbox.com","BreachDate":"2012-07-01","AddedDate":"2016-08-31T00:19:19Z","ModifiedDate":"2016-08-31T00:19:19Z","PwnCount":68648009,"Description":"In mid-2012, Dropbox suffered a data breach which exposed the stored credentials of tens of millions of their customers. In August 2016, <a href=\"https://motherboard.vice.com/read/dropbox-forces-password-resets-after-user-credentials-exposed\" target=\"_blank\" rel=\"noopener\">they forced password resets for customers they believed may be at risk</a>. A large volume of data totalling over 68 million records <a href=\"https://motherboard.vice.com/read/hackers-stole-over-60-million-dropbox-accounts\" target=\"_blank\" rel=\"noopener\">was subsequently traded online</a> and included email addresses and salted hashes of passwords (half of them SHA1, half of them bcrypt).","DataClasses":["Email addresses","Passwords"],"IsVerified":true,"IsFabricated":false,"IsSensitive":false,"IsActive":true,"IsRetired":false,"IsSpamList":false,"LogoType":"svg"}]
EOT;
            case "https://haveibeenpwned.com/api/v2/pasteaccount/test%40example.com":
                return <<<EOT
[{"Source":"Pastebin","Id":"KsXNChr2","Title":"partial of the roblox database","Date":"2017-06-06T06:24:07Z","EmailCount":1171}]
EOT;

        }
    }
}

class HaveIBeenPwnedTest extends \PHPUnit_Framework_TestCase
{
    protected $client;

    public function setUp() {
        $this->client = new HaveIBeenPwned(new DummyAdapter());
    }

    /**
     * Test that the getCheckAccount returns the expected result
     *
     * @covers \xsist10\HaveIBeenPwned\HaveIBeenPwned::getCheckAccount
     */
    public function testCheckAccount()
    {
        $result = $this->client->checkAccount("test@example.com");
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0]["Name"], "Dropbox");
    }

    /**
     * Test that the getPasteAccount returns the expected result
     *
     * @covers \xsist10\HaveIBeenPwned\HaveIBeenPwned::getPasteAccount
     */
    public function testGetPasteAccount()
    {
        $result = $this->client->getPasteAccount("test@example.com");
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0]["Id"], "KsXNChr2");
    }

    
}
