# HaveIBeenPwned PHP client

[![Build Status](https://travis-ci.org/xsist10/HaveIBeenPwned.svg?branch=master)](hhttps://travis-ci.org/xsist10/HaveIBeenPwned)

## Install

``` json
{
    "require": {
        "xsist10/haveibeenpwned": "~1.0"
    }
}
```

## Usage

### Check if you've been pwned
``` php
use xsist10\HaveIBeenPwned\HaveIBeenPwned;

$manager = new HaveIBeenPwned();
$manager->checkAccount("your_email_address");
```

### List all breaches that have are on record
``` php
use xsist10\HaveIBeenPwned\HaveIBeenPwned;

$manager = new HaveIBeenPwned();
$manager->getBreaches();

$manager->getBreach('specific_breach_by_name');
```

### List the types of data that are covered when describing a leak
``` php
use xsist10\HaveIBeenPwned\HaveIBeenPwned;

$manager = new HaveIBeenPwned();
$manager->getDataClasses();
```

## Credits

- [Troy Hunt](https://github.com/troyhunt) for creating https://haveibeenpwned.com/
- [Thomas Shone](https://github.com/xsist10)


## License

The MIT License (MIT). Please see [License File](https://github.com/xsist10/HaveIBeenPwned/blob/master/LICENSE) for more information.
