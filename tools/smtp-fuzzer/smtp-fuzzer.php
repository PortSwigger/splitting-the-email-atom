<?php
define("HOST", "localhost");
define("PORT", 25);

require("functions.inc.php");

$log = [];
$fp = connect();
for($i=0;$i<0xff;$i++) {
   echo "Fuzzing char $i...\n";
   $chr = chr($i);
   if($i === 0x0a || $i === 0x0d) {
     continue;
   }
   $fp = helo($fp);
   if(!send($fp, "RCPT TO:<psres.net!ffyl8ajie5tqesq988ge7e25pwvnjg98xx".$chr."@blah.blah>")) {
     if(!resetSmtp($fp)) {
       quit($fp);
       $fp = connect();
       helo($fp);
     }
     continue;
   }
   if(get($fp, 250)) {
     array_push($log, $i);
   }
   if(!resetSmtp($fp)) {
      quit($fp);
      $fp = connect();
      helo($fp);
   }
}

quit($fp);

echo "Completed.\n";

print_r($log);

?>
