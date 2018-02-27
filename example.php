<?php

require_once 'vendor/autoload.php';

use xsist10\HaveIBeenPwned\HaveIBeenPwned;

$manager = new HaveIBeenPwned();
$report = $manager->checkAccount("bob@mailinator.com");

$password = "12345";
$numTimesCompromised = $manager->isPasswordCompromised($password);