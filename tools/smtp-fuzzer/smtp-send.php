<?php
define("HOST", "localhost");
define("PORT", 25);

require("functions.inc.php");

$email = $argv[1];

$fp = connect();
helo($fp);
send($fp, "RCPT TO:<$email>");
get($fp, 250);
send($fp, "DATA");
get($fp, 354);
send($fp, ".");
quit($fp);
?>
