<?php
function send($fp, $cmd) {
  echo 'Sending->'.$cmd."\n";
  return fwrite($fp, $cmd . "\n");
}

function get($fp, $expect) {
  $response = fgets($fp, 1024);
  echo "Getting<-" . $response . "\n";
  $foundResponse = false;
  if(str_starts_with($response, $expect)) {
    $foundResponse = true;
  } 
  fflush($fp);
  return $foundResponse;
}

function resetSmtp($fp) {
  send($fp, "RSET");
  if(!get($fp,"250 2.0.0 Ok")) {
    echo "Unable to reset\n";
    return false;
  }
  return true;
}

function quit($fp) {
   send($fp, "quit");
   get($fp, 221);
   fclose($fp);
}

function connect() {
   $fp = fsockopen(HOST, PORT, $errno, $errstr, 360);
   if(!$fp) {
      echo "$errstr ($errno)\n";
   } else {
      if(!get($fp, 220)) {
         echo "Unable to establish connection!\n";
         quit($fp);
         exit;
      }
     return $fp;
   }
   echo "Unable to connect to server\n";
   exit;
}

function helo($fp) {
   $preCommands = ["HELO localhost" => 250,"MAIL FROM:<root@localhost>" => 250];
   foreach($preCommands as $cmd => $expect) {
     send($fp, $cmd);
     if(!get($fp, $expect)) {
       echo "Invalid response\n";
       $fp = connect();
       $fp = helo($fp);
       return $fp;
     }
   }
   return $fp;
}
