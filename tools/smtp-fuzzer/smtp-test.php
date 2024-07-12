<?php
define("HOST", "localhost");
define("PORT", 25);

require("functions.inc.php");

$email = $argv[1];

$fp = connect();
helo($fp);
send($fp, "RCPT TO:<$email>");
if(get($fp, 250)) {
   echo "Successful.\n";
}

quit($fp);
?>
