<?php
require_once '../src/config.php';
require_once '../src/whatsprot.class.php';
require_once '../src/Registration.php';
$debug    = DEBUG;  // Shows debug log, this is set to false if not specified
$log      = LOGGING;  // Enables log file, this is set to false if not specified
if (isset($argv[1]) == false){
  echo "Usage:  PHP cli.php [option] arg1 arg2...
--register          --register <username>
                    --register 20123456789

--register-code     --register-code <username> <code-withouth-dash>
                    --register-code 20123456789 123456

--login-user        --login-user <username> <nickname> <password from --register-code>
                    --login-user 20123456789 AlSayedGamal i3u4o23i4b234goi4u23l4kjblk34
  ";
}
if (isset($argv[1])){
    switch ($argv[1]) {
      case '--register':
        $username = $argv[2];    // Your number with country code, ie: 34123456789
        $r = new Registration($username, $debug);
        $r->codeRequest('sms');
        echo "You must have received and SMS with your code save it for next step";
        break;
      case '--register-code':
        $username = $argv[2];    // Your number with country code, ie: 34123456789
        $code = $argv[3];
        $r = new Registration($username, $debug);
        $r->codeRegister($code);
        echo "your code has been registered .. save the password for next step";
        break;
      case '--login-user':
        $username = $argv[2];    // Your number with country code, ie: 34123456789
        $nickname = $argv[3];    // Your nickname, it will appear in push notifications
        $password = $argv[4];    // Your nickname, it will appear in push notifications
        $w = new WhatsProt($username, $nickname, $debug);
        $w->connect(); // Connect to WhatsApp network
        $w->loginWithPassword($password); // logging in with the password we got!
        echo "your code has been registered .. save the password for next step";
        break;
      default:
        echo "Please, choose one of the options --register --login";
        break;
    }
}
?>