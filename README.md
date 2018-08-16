# HaveIBeenPwned PHP client

[![Build Status](https://travis-ci.org/xsist10/HaveIBeenPwned.svg?branch=master)](hhttps://travis-ci.org/xsist10/HaveIBeenPwned)

## Install

```bash
composer require xsist10/haveibeenpwned:~2.0
```

## Usage

### Create manager instance
``` php
use xsist10\HaveIBeenPwned\HaveIBeenPwned;

// By default the $manager will use a Curl adapter
$manager = new HaveIBeenPwned();
```

By default the `$manager` will attempt to find the best HTTP client to use. You can install the adapter for your prefered client. You can find a list of available ones on the [HTTP Plug's client page](http://docs.php-http.org/en/latest/clients.html

If you want to manually specify your HTTP client adapters you can pass it in as a constructor.

```bash
# Install the cURL adapter
composer require php-http/curl-client
```

```php
// Manually specify the cURL adapter
use Http\Client\Curl\Client;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;

$messageFactory = MessageFactoryDiscovery::find();
$streamFactory = StreamFactoryDiscovery::find();

// Specify your CURLOPTs: http://php.net/manual/en/function.curl-setopt.php
$options = [
    // HaveIBeenPwnd API requires a user-agent to be specified
    CURLOPT_USERAGENT => 'your-user-agent',
    // The number of seconds to wait while trying to connect.
    CURLOPT_CONNECTTIMEOUT => 10,
];
$client = new Client($messageFactory, $streamFactory, $options);

// You can create a new manager with a specified adapter
$manager = new HaveIBeenPwned($client, $messageFactory);

```

### Check if you've been pwned
``` php
$accountResponse = $manager->checkAccount("your_email_address");

foreach ($report->getBreaches() as $breach) {
    echo "Name: " . $breach->getName() . "\n";
    echo "Domain: " . $breach->getDomain() . "\n";
    echo "Date: " . $breach->getBreachDate() . "\n";
    echo "# Accounts Affected" . $breach->getPwnCount() . "\n";
    echo "Details Disclosed: " . $breach->getDataClasses() . "\n";
    echo "\n";
}
```

### Check if your account has been leaked in a paste
``` php
$pastes = $manager->getPasteAccount("your_email_address");

foreach ($pastes as $paste) {
    echo "Name: " . $paste->getTitle() . "\n";
    echo "Source: " . $paste->getSource() . "\n";
    echo "Id: " . $paste->getId() . "\n";
    echo "Date: " . ($paste->getDate() ? $paste->getDate() : "unknown") . "\n";
    echo "# Emails Affected: " . $paste->getEmailCount() . "\n";
    echo "\n";
}
```

### Check if your password has been leaked before
``` php
// Your password is not sent to the remote API. Only a partial of the SHA1
// value is sent and all matching full SHA1 results are returned and compared.
$passwordResponse = $manager->isPasswordCompromised("your_password");

if ($passwordResponse->isCompromised()) {
    echo "Your password has been compromised " . $passwordResponse->numberOfTimesCompromised() . " time(s)\n";
}
```

### List all breaches that have are on record
``` php
$breaches = $manager->getBreaches();

$breach = $manager->getBreach('specific_breach_by_name');
```

### List the types of data that are covered when describing a leak
``` php
$dataClasses = $manager->getDataClasses();
```

## Logger Support

The adapters support [PSR-3 Logger](http://www.php-fig.org/psr/psr-3/). I recommend using [monolog](https://github.com/Seldaek/monolog).

### Install Monolog
```bash
$ composer require monolog/monolog
```

### Use Monolog with HaveIBeenPwned
```php
use xsist10\HaveIBeenPwned\HaveIBeenPwned;

use Http\Client\Common\Plugin\LoggerPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\Curl\Client;
use Http\Discovery\HttpClientDiscovery;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Create our log instance
$log = new Logger('name');
// Push all logging up to the level of DEBUG to our log file
$log->pushHandler(new StreamHandler('[full log filename]', Logger::DEBUG));

// Create our HTTP Client logger plugin
$loggerPlugin = new LoggerPlugin($log);

// Create our HTTP Client and compose it into a plugin client
$pluginClient = new PluginClient(
    HttpClientDiscovery::find(),
    [$loggerPlugin]
);

// You can create a new manager with a specified adapter
$manager = new HaveIBeenPwned($client);

// Calls made to HaveIBeenPwned will be logged to your log file now
```

## Credits

- [Troy Hunt](https://github.com/troyhunt) for creating https://haveibeenpwned.com/
- [Thomas Shone](https://github.com/xsist10)
- [Chung-Sheng, Li](https://github.com/peter279k)


## License

The MIT License (MIT). Please see [License File](https://github.com/xsist10/HaveIBeenPwned/blob/master/LICENSE) for more information.
