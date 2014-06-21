<?php

require_once 'vendor/autoload.php';

use xsist10\HaveIBeenPwned\HaveIBeenPwned;

$manager = new HaveIBeenPwned();
$manager->checkAccount("your_email_address");