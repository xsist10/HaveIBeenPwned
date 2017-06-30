<?php

namespace xsist10\HaveIBeenPwned;

use xsist10\HaveIBeenPwned\Adapter\Adapter;
use xsist10\HaveIBeenPwned\HaveIBeenPwned;

class DummyAdapter implements Adapter
{
    public function get($url) {
        switch ($url) {
            case "https://haveibeenpwned.com/api/v2/breachedaccount/test":
                return <<<EOT
        [{"Title":"Dropbox","Name":"Dropbox","Domain":"dropbox.com","BreachDate":"2012-07-01","AddedDate":"2016-08-31T00:19:19Z","ModifiedDate":"2016-08-31T00:19:19Z","PwnCount":68648009,"Description":"In mid-2012, Dropbox suffered a data breach which exposed the stored credentials of tens of millions of their customers. In August 2016, <a href=\"https://motherboard.vice.com/read/dropbox-forces-password-resets-after-user-credentials-exposed\" target=\"_blank\" rel=\"noopener\">they forced password resets for customers they believed may be at risk</a>. A large volume of data totalling over 68 million records <a href=\"https://motherboard.vice.com/read/hackers-stole-over-60-million-dropbox-accounts\" target=\"_blank\" rel=\"noopener\">was subsequently traded online</a> and included email addresses and salted hashes of passwords (half of them SHA1, half of them bcrypt).","DataClasses":["Email addresses","Passwords"],"IsVerified":true,"IsFabricated":false,"IsSensitive":false,"IsActive":true,"IsRetired":false,"IsSpamList":false,"LogoType":"svg"}]
EOT;
                break;
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
     * Test that the configuration is set correctly on init
     *
     * @covers \xsist10\HaveIBeenPwned\HaveIBeenPwned::checkAccount
     */
    public function testCheckAccount()
    {
        $result = $this->client->checkAccount("test");
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($result), 1);
    }
}
