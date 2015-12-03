<?php
require_once '../src/config.php';
require_once '../src/whatsprot.class.php';
require_once '../src/Registration.php';
require_once '../src/events/MyEvents.php';

// $debug    = DEBUG;  // Shows debug log, this is set to false if not specified
// $log      = LOGGING;  // Enables log file, this is set to false if not specified
$debug    = false;  // Shows debug log, this is set to false if not specified
$log      = LOGGING;  // Enables log file, this is set to false if not specified

if (isset($argv[1]) == false){
  echo "Usage:  PHP cli.php [option] arg1 arg2...
".bold("--register")."      --register <username>
                --register 20123456789

".bold("--register-code")." --register-code <username> <SMS-code-withouth-dash>
                --register-code 20123456789 123456

".bold("--login-user")."    --login-user <username> <nickname> <password>
                            --login-user 20123456789 AlSayedGamal i3u4o23i4b234goi4u23l4kjblk34
".bold("--msg-text")."      --msg-text <username> <nickname> <password> <destination> <msg>
                --msg-text 20123456789 AlSayedGamal i3u4o23i4b234goi4u23l4kjblk34
  ";
}

if (isset($argv[1])){
    $res = '';
    switch ($argv[1]) {
      case '--register':
        $username = $argv[2];    // Your number with country code, ie: 34123456789
        $r = new Registration($username, $debug);
        try {
            $res = $r->codeRequest('sms');
        } catch (Exception $e) {
            echo json_encode(array('error'=>$e->getMessage()));
            echo "\n";
        }
        if ($res != ""){
            echo json_encode($res);
            echo "\n";            
        }
        break;
      case '--register-code':
        $username = $argv[2];    // Your number with country code, ie: 34123456789
        $code = $argv[3];
        $r = new Registration($username, $debug);
        $res = json_encode($r->codeRegister($code));
        if ($res != ""){
            echo json_encode($res);
            echo "\n";            
        }
        break;
      case '--login-user':
        $username = $argv[2];    // Your number with country code, ie: 34123456789
        $nickname = $argv[3];    // Your nickname, it will appear in push notifications
        $password = $argv[4];    // Your nickname, it will appear in push notifications
        $w = new WhatsProt($username, $nickname, $debug);
        $w->connect(); // Connect to WhatsApp network
        $w->loginWithPassword($password); // logging in with the password we got!
        break;
      case '--msg-text':
        $src = $argv[2];    // Your number with country code, ie: 201003544877
        $nickname = $argv[3];    // Your nickname, it will appear in push notifications
        $password = $argv[4];    //your password
        $target = $argv[5];    // Destination number  with country code, ie: 20100354499
        $msg = $argv[6];
        if ($argv[2] && $argv[3] && $argv[4] && $argv[5] && $argv[6]){
            $w = new WhatsProt($src, $nickname, $debug);
            $w->connect(); // Connect to WhatsApp network
            $w->loginWithPassword($password); // logging in with the password we got!
            echo json_encode($w->sendMessage($target , $msg));

        }else{
            echo "wrong parameters";
            echo "--msg-text          --msg-text <username> <nickname> <password> <destination> <msg>
                    --msg-text 20123456789 AlSayedGamal i3u4o23i4b234goi4u23l4kjblk34 201001234567 'Hello..'";
        }
        break;
      case '--msg-img':
        $src = $argv[2];    // Your number with country code, ie: 201003544877
        $nickname = $argv[3];    // Your nickname, it will appear in push notifications
        $password = $argv[4];    //your password
        $target = $argv[5];    // Destination number  with country code, ie: 20100354499
        $msg = $argv[6];
        $filepath = $argv[7];
        if ($argv[2] && $argv[3] && $argv[4] && $argv[5] && $argv[6] && $argv[7]){
            $w = new WhatsProt($src, $nickname, $debug);
            $w->connect(); // Connect to WhatsApp network
            $w->loginWithPassword($password); // logging in with the password we got!
            $fsize = filesize($filepath);
            $fhash = hash_file("md5", $filepath);
            // echo json_encode($w->sendMessageImage($target, $filepath, false, $fsize, $fhash, $msg));
            echo print_r($w->sendMessageImage($target, $filepath, false, false, false, $msg));
            $w->pollMessage();

        }else{
            echo "wrong parameters";
            // echo "--msg-text          --msg-text <username> <nickname> <password> <destination> <msg>
            //         --msg-text 20123456789 AlSayedGamal i3u4o23i4b234goi4u23l4kjblk34";
        }
        break;
      case '--msg-receive':
        $src = $argv[2];    // Your number with country code, ie: 201003544877
        $nickname = $argv[3];    // Your nickname, it will appear in push notifications
        $password = $argv[4];    //your password
        // $target = $argv[5];    // Destination number  with country code, ie: 20100354499
        // $msg = $argv[6];
        if ($argv[2] && $argv[3] && $argv[4]){
            $w = new WhatsProt($src, $nickname, $debug);
            $w->connect(); // Connect to WhatsApp network
            $w->loginWithPassword($password); // logging in with the password we got!
            echo json_encode($w->pollMessage());

        }else{
            echo "wrong parameters";
            // echo "--msg-text          --msg-text <username> <nickname> <password> <destination> <msg>
            //         --msg-text 20123456789 AlSayedGamal i3u4o23i4b234goi4u23l4kjblk34";
        }
        break;
      default:
        $bold_msg = bold("php cli.php");
        echo "Wrong option please use {$bold_msg} for available options";
        break;
    }
}


function bold($text){
    return "\033[1m{$text}\033[0m";
}
?>
