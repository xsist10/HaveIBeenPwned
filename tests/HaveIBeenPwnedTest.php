<?php

namespace xsist10\HaveIBeenPwned;

use xsist10\HaveIBeenPwned\Adapter\Adapter;
use xsist10\HaveIBeenPwned\HaveIBeenPwned;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class DummyAdapter implements Adapter
{
    use LoggerAwareTrait;

    public function isSupported() {
        return true;
    }

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
            case "https://haveibeenpwned.com/api/v2/breaches":
                return <<<EOT
[{"Title":"000webhost","Name":"000webhost","Domain":"000webhost.com","BreachDate":"2015-03-01","AddedDate":"2015-10-26T23:35:45Z","ModifiedDate":"2015-10-26T23:35:45Z","PwnCount":13545468,"Description":"In approximately March 2015, the free web hosting provider <a href=\"http://www.troyhunt.com/2015/10/breaches-traders-plain-text-passwords.html\" target=\"_blank\" rel=\"noopener\">000webhost suffered a major data breach</a> that exposed over 13 million customer records. The data was sold and traded before 000webhost was alerted in October. The breach included names, email addresses and plain text passwords.","DataClasses":["Email addresses","IP addresses","Names","Passwords"],"IsVerified":true,"IsFabricated":false,"IsSensitive":false,"IsActive":true,"IsRetired":false,"IsSpamList":false,"LogoType":"png"}]
EOT;
            case "https://haveibeenpwned.com/api/v2/breach/000webhost":
                return <<<EOT
{"Title":"000webhost","Name":"000webhost","Domain":"000webhost.com","BreachDate":"2015-03-01","AddedDate":"2015-10-26T23:35:45Z","ModifiedDate":"2015-10-26T23:35:45Z","PwnCount":13545468,"Description":"In approximately March 2015, the free web hosting provider <a href=\"http://www.troyhunt.com/2015/10/breaches-traders-plain-text-passwords.html\" target=\"_blank\" rel=\"noopener\">000webhost suffered a major data breach</a> that exposed over 13 million customer records. The data was sold and traded before 000webhost was alerted in October. The breach included names, email addresses and plain text passwords.","DataClasses":["Email addresses","IP addresses","Names","Passwords"],"IsVerified":true,"IsFabricated":false,"IsSensitive":false,"IsActive":true,"IsRetired":false,"IsSpamList":false,"LogoType":"png"}
EOT;
            case "https://haveibeenpwned.com/api/v2/dataclasses":
                return <<<EOT
["Account balances","Age groups","Astrological signs","Auth tokens","Avatars","Bank account numbers","Banking PINs","Beauty ratings","Biometric data","Browser user agent details","Buying preferences","Car ownership statuses","Career levels","Charitable donations","Chat logs","Credit card CVV","Credit cards","Credit status information","Customer feedback","Customer interactions","Dates of birth","Deceased date","Device information","Device usage tracking data","Drinking habits","Drug habits","Education levels","Email addresses","Email messages","Employers","Ethnicities","Family members' names","Family plans","Family structure","Financial investments","Financial transactions","Fitness levels","Genders","Geographic locations","Government issued IDs","Health insurance information","Historical passwords","Home ownership statuses","Homepage URLs","Income levels","Instant messenger identities","IP addresses","Job titles","MAC addresses","Marital statuses","Names","Net worths","Nicknames","Parenting plans","Partial credit card data","Passport numbers","Password hints","Passwords","Payment histories","Payment methods","Personal descriptions","Personal health data","Personal interests","Phone numbers","Physical addresses","Physical attributes","Political donations","Political views","Private messages","Professional skills","Purchases","Purchasing habits","Races","Recovery email addresses","Relationship statuses","Religions","Reward program balances","Salutations","Security questions and answers","Sexual fetishes","Sexual orientations","Smoking habits","SMS messages","Social connections","Spoken languages","Survey results","Time zones","Travel habits","User statuses","User website URLs","Usernames","Utility bills","Vehicle details","Website activity","Work habits","Years of birth","Years of professional experience"]
EOT;
        }
    }
}

class HaveIBeenPwnedTest extends \PHPUnit_Framework_TestCase
{
    protected $client;

    public function setUp() {
        $adapter = new DummyAdapter();
        $adapter->setLogger(new NullLogger());
        $this->client = new HaveIBeenPwned($adapter);
    }

    /**
     * Test that the getCheckAccount returns the expected result
     */
    public function testCheckAccount()
    {
        $result = $this->client->checkAccount("test@example.com");
        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0]["Name"], "Dropbox");
    }

    /**
     * Test that the getPasteAccount returns the expected result
     */
    public function testGetPasteAccount()
    {
        $result = $this->client->getPasteAccount("test@example.com");
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0]["Id"], "KsXNChr2");
    }

    /**
     * Test that the getBreaches returns the expected result
     */
    public function testGetBreaches()
    {
        $result = $this->client->getBreaches();
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0]["Title"], "000webhost");
    }

    /**
     * Test that the getBreach returns a single result
     */
    public function testGetBreach()
    {
        $result = $this->client->getBreach("000webhost");
        $this->assertTrue(is_array($result));
        $this->assertEquals($result["Title"], "000webhost");
    }

    public function testGetDataClasses()
    {
        $result = $this->client->getDataClasses();
        $this->assertTrue(is_array($result));
        $this->assertEquals(count($result), 97);
    }
}
