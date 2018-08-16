<?php

require_once 'vendor/autoload.php';

use xsist10\HaveIBeenPwned\HaveIBeenPwned;

$manager = new HaveIBeenPwned();
$report = $manager->checkAccount("bob@mailinator.com");
foreach ($report->getBreaches() as $breach) {
    echo "Breached in " . $breach->getTitle() . " (" . $breach->getDomain() . ") ";
    echo "along with " . ($breach->getPwnCount() - 1) . " other accounts ";
    echo "on " . $breach->getBreachDate() . "\n";
    echo "The following details were disclosed: " . $breach->getDataClasses() . "\n";
    echo "\n";
}

$pastes = $manager->getPasteAccount("bob@mailinator.com");

foreach ($pastes as $paste) {
    echo "Source: " . $paste->getSource() . "\n";
    echo "Id: " . $paste->getId() . "\n";
    echo "Title: " . $paste->getTitle() . "\n";
    echo "Date: " . ($paste->getDate() ? $paste->getDate() : "unknown") . "\n";
    echo "EmailCount: " . $paste->getEmailCount() . "\n";
    echo "\n";
}

$password = "12345";
$passwordResponse = $manager->isPasswordCompromised($password);

if ($passwordResponse->isCompromised()) {
    echo "Your password has been compromised " . $passwordResponse->numberOfTimesCompromised() . " time(s)\n";
}