<?php
/**
 * CLI non-interactive script for Chat-API
 *
 * PHP Version: 5.6+
 *
 * @category  Communications
 * @package   Chat-API
 * @author    Anass Ahmed <anass@mussder.com>
 * @copyright 2015 - Anass Ahmed, 08 December, 2015
 * @license   GPL <http://gnu.org/>
 * @version   GIT: $id
 * @link      http://github.com/AlSayedGamal/Chat-API
 */

require_once '../src/config.php';
require_once '../src/whatsprot.class.php';
require_once '../src/Registration.php';
require '../src/events/AllEvents.php';

$debug    = false;  // Shows debug log, this is set to false if not specified
$log      = LOGGING;  // Enables log file, this is set to false if not specified
// $cliMessages = array();
$output = [
    'onSendMessage' => array(),
    'onSendMessageReceived' => array(),
    'onMessageReceivedServer' => array(),
    'onMessageReceivedClient' => array(),
    'messages' => array(),
];

/**
 * MyEvents
 *
 * Extends AllEvents
 *
 * @package Chat-API
 * @author  Anass Ahmed <anass@mussder.com>
 */
class MyEvents extends AllEvents
{
    public $activeEvents = array(
        'onSendMessage',
        'onSendMessageReceived',
        'onMessageReceivedServer',
        'onMessageReceivedClient',
        'onGetMessage',
        'onGetImage',
        'onGetAudio',
        'onGetVideo',
    );

    public function onSendMessage($mynumber, $target, $messageId, $node) {
        global $output;
        array_push(
            $output['onSendMessage'],
            [
                'target' => $target,
                'messageId' => $messageId,
                'node' => $node
            ]
        );
    }

    public function onSendMessageReceived($mynumber, $id, $from, $type) {
        global $output;
        array_push(
            $output['onSendMessageReceived'],
            [
                'id' => $id,
                'from' => $from,
                'type' => $type
            ]
        );
    }

    public function onMessageReceivedClient($mynumber, $from, $id, $type, $time, $participant) {
        global $output;
        array_push(
            $output['onMessageReceivedClient'],
            [
                'id' => $id,
                'from' => $from,
                'type' => $type,
                'time' => $time,
                'participant' => $participant
            ]
        );
    }

    public function onMessageReceivedServer($mynumber, $from, $id, $type, $time) {
        global $output;
        array_push(
            $output['onMessageReceivedServer'],
            [
                'id' => $id,
                'from' => $from,
                'type' => $type,
                'time' => $time,
            ]
        );
    }

    public function onGetMessage($mynumber, $from, $id, $type, $time, $name, $body) {
        global $output;
        array_push(
            $output['messages'],
            [
                'id' => $id,
                'from' => $from,
                'type' => $type,
                'time' => $time,
                'name' => $name,
                'body' => $body
            ]
        );
    }

    public function onGetImage($mynumber, $from, $id, $type, $time, $name, $size, $url, $file, $mimeType, $fileHash, $width, $height, $preview, $caption) {
        global $output;
        array_push(
            $output['messages'],
            [
                'id' => $id,
                'from' => $from,
                'type' => $type,
                'time' => $time,
                'name' => $name,
                'size' => $size,
                'url' => $url,
                'file' => $file,
                'mimeType' => $mimeType,
                'fileHash' => $fileHash,
                'width' => $width,
                'height' => $height,
                // 'preview' => $preview,
                'caption' => $caption
            ]
        );
    }

    public function onGetAudio($mynumber, $from, $id, $type, $time, $name, $size, $url, $file, $mimeType, $fileHash, $duration, $acodec, $fromJID_ifGroup = null) {
        global $output;
        array_push(
            $output['messages'],
            [
                'id' => $id,
                'from' => $from,
                'type' => $type,
                'time' => $time,
                'name' => $name,
                'size' => $size,
                'url' => $url,
                'file' => $file,
                'mimeType' => $mimeType,
                'fileHash' => $fileHash,
                'duration' => $duration,
                'acodec' => $acodec
            ]
        );
    }

    public function onGetVideo($mynumber, $from, $id, $type, $time, $name, $url, $file, $size, $mimeType, $fileHash, $duration, $vcodec, $acodec, $preview, $caption) {
        global $output;
        array_push(
            $output['messages'],
            [
                'id' => $id,
                'from' => $from,
                'type' => $type,
                'time' => $time,
                'name' => $name,
                'size' => $size,
                'url' => $url,
                'file' => $file,
                'mimeType' => $mimeType,
                'fileHash' => $fileHash,
                'duration' => $duration,
                'acodec' => $acodec,
                'vcodec' => $vcodec,
                // 'preview' => $preview,
                'caption' => $caption
            ]
        );
    }
}

/**
 * Initiate Connection for Sending, Receiving Messages
 *
 * @return WhatsProt
 * @author Anass Ahmed
 **/

function initiateConnection($username, $nickname, $password, $debug) {
    $w = new WhatsProt($username, $nickname, $debug);
    $events = new MyEvents($w);
    $events->setEventsToListenFor($events->activeEvents);
    $w->connect(); // Connect to WhatsApp network
    $w->loginWithPassword($password); // logging in with the password we got!
    $w->sendGetClientConfig(); // Get client config
    $w->sendGetServerProperties(); // Get server properties
    $w->pollMessage();
    return $w;
}

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
".bold("--msg-receive")."      --msg-receive <username> <nickname> <password>
                --msg-receive 201003544877 AlSayedGamal ZAJjYyss\/\/ty6Xbud8oi9Eat83s=
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
        }
        if ($res != ""){
            echo json_encode($res);    
        }
        echo "\n\n";
        break;

        case '--register-code':
            $username = $argv[2];    // Your number with country code, ie: 34123456789
            $code = $argv[3];
            $r = new Registration($username, $debug);
            $res = json_encode($r->codeRegister($code));
            if ($res != ""){
                echo json_encode($res);
            }
            echo "\n\n";
            break;
      
        case '--login-user':
            $username = $argv[2];    // Your number with country code, ie: 34123456789
            $nickname = $argv[3];    // Your nickname, it will appear in push notifications
            $password = $argv[4];    // Your nickname, it will appear in push notifications

            $w = new WhatsProt($username, $nickname, $debug);
            $w->connect(); // Connect to WhatsApp network
            try{
                $w->loginWithPassword($password); // logging in with the password we got!
            } catch (Exception $e) {
                echo json_encode(array('error'=>$e->getMessage()));
            }

            echo "\n\n";
            break;

        case '--msg-text':
            $username = $argv[2];    // Your number with country code, ie: 201003544877
            $nickname = $argv[3];    // Your nickname, it will appear in push notifications
            $password = $argv[4];    //your password
            $target = $argv[5];    // Destination number  with country code, ie: 20100354499
            $msg = $argv[6];
            if ($argv[2] && $argv[3] && $argv[4] && $argv[5] && $argv[6]){
                try{
                    $w = initiateConnection($username, $nickname, $password, $debug);
                    $w->sendMessage($target , $msg);
                    echo "\n";
                    echo json_encode($output);
                } catch (Exception $e) {
                    echo json_encode(array('error'=>$e->getMessage()));
                }
            }else{
                echo "wrong parameters";
                echo "--msg-text          --msg-text <username> <nickname> <password> <destination> <msg>
                        --msg-text 20123456789 AlSayedGamal i3u4o23i4b234goi4u23l4kjblk34 201001234567 'Hello..'";
            }
            echo "\n\n";
            break;

        case '--msg-img':
            $username = $argv[2];    // Your number with country code, ie: 201003544877
            $nickname = $argv[3];    // Your nickname, it will appear in push notifications
            $password = $argv[4];    //your password
            $target = $argv[5];    // Destination number  with country code, ie: 20100354499
            $msg = $argv[6];
            $filepath = $argv[7];
            if ($argv[2] && $argv[3] && $argv[4] && $argv[5] && $argv[6] && $argv[7]){
                try{
                    $w = initiateConnection($username, $nickname, $password, $debug);
                    $fsize = filesize($filepath);
                    $fhash = hash_file("md5", $filepath);
                    // echo json_encode($w->sendMessageImage($target, $filepath, false, $fsize, $fhash, $msg));
                    echo print_r($w->sendMessageImage($target, $filepath, false, false, false, $msg));
                } catch (Exception $e) {
                    echo json_encode(array('error'=>$e->getMessage()));
                }
            }else{
                echo "wrong parameters";
                // echo "--msg-text          --msg-text <username> <nickname> <password> <destination> <msg>
                //         --msg-text 20123456789 AlSayedGamal i3u4o23i4b234goi4u23l4kjblk34";
            }
            echo "\n\n";
            break;

      case '--msg-receive':
        if ($argv[2] && $argv[3] && $argv[4]){
            $username = $argv[2];       // telephone number +201003544877
            $nickname = $argv[3];       // Your nickname, it will appear in push notifications if any.
            $password = $argv[4];       // your password (generated by whatsapp)
            $w = initiateConnection($username, $nickname, $password, $debug);
            echo "\n";
            echo json_encode($output);
        }else{
            echo json_encode(array('error'=>"Expecting --msg-receive <username> <nickname> <password>"));
        }
        echo "\n\n";
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
