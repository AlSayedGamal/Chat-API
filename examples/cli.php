<?php
require_once '../src/config.php';
require_once '../src/whatsprot.class.php';
require_once '../src/Registration.php';
$debug    = DEBUG;  // Shows debug log, this is set to false if not specified
$log      = LOGGING;  // Enables log file, this is set to false if not specified
if ($argv == null){
  echo "Usage: PHP {$argv[0]} <number> <nickname>";
}
var_dump($argv);
switch ($argv[1]) {
  case '--register':
    $username = $argv[2];    // Your number with country code, ie: 34123456789
    $nickname = $argv[3];    // Your nickname, it will appear in push notifications
    $w = new WhatsProt($username, $nickname, $debug, $log);
    $w = new WhatsProt($username, $nickname, $debug, $log);
    $r = new Registration($username, $debug);
    $r->codeRequest('sms');
    echo "You must have received and SMS with your code save it for next step";
    break;
  case '--register-code':
    $username = $argv[2];    // Your number with country code, ie: 34123456789
    $code = $argv[3];
    $r = new Registration($username, $debug);
    $r->codeRegister($code);
    echo "your code has been registered";
    break;
  default:
    echo "Please, choose one of the options --register --login";
    break;
}
?>