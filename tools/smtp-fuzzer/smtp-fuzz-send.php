<?php
define("HOST", "localhost");
define("PORT", 25);

require("functions.inc.php");

for($i=0;$i<=0xff;$i++) {
   $chr = chr($i);
   $email = "{$i}chrsfby8njveit3e5qm8lgr7r2ip9v0jtajy8%psres.net".$chr."@blah.blah";
   echo "Fuzzing chr ".$i."\n";
   $fp = connect();
   helo($fp);
   send($fp, "RCPT TO:<$email>");
   get($fp, 250);
   send($fp, "DATA");
   get($fp, 354);
   send($fp, ".");
   quit($fp);
}
?>
