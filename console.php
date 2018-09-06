<?php
require_once('vendor/autoload.php');

use Symfony\Component\Console\Application;
use Willshaw\WorldFirst\Command\TillReceipt;
use Willshaw\WorldFirst\Command\BankAccount;

$application = new Application();

// Add commands
$application->add(new TillReceipt());

$application->run();

