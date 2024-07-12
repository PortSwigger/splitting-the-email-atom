<?php
//Load Composer's autoloader
require 'vendor/autoload.php';

use Algo26\IdnaConvert\ToUnicode;
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
$IDN = new ToUnicode();
error_reporting(E_ERROR | E_PARSE);
set_time_limit(30);
echo '<meta charset="UTF-8">';
echo '<meta name="referrer" content="none">';
$chrs = array_merge(range('a','z'),range('A','Z'));
$whitespace = [" ","\t","\r","\n"];
$matched = false;  
$before = isset($_GET['before']) ? $_GET['before'] : '';
$matches = isset($_GET['matches']) ? $_GET['matches'] : '';
$contains = isset($_GET['contains']) ? $_GET['contains'] : '';
$randomPad = isset($_GET['randomPad']) ? (int) $_GET['randomPad'] : 0;
if(!$before) {
   echo '<style>
      * {
         max-width: 90ch;
         font-family: Arial;
      }
      input[type="text"]{width:500px;}
      </style>';
   echo '<h1>Punycode fuzzer</h1>';
   echo '<p>I used this fuzzer to generate the examples shown on the <a href="punycode-converter.php">converter</a> page. You can fuzz for numbers, characters or whitespace. PHP generally bails with large nested loops so this fuzzer iterates to 0xffff and randomly selects characters. This is very effective and finds most combinations, but have I missed something?</p>';
   echo '<form>';
   echo '<p>';
   echo '<label>Random zero pad numbers?</label><br>';
   echo '<input type=checkbox value=1 name=randomPad>';
   echo '</p>';
   echo '<p>';
   echo '$1-$9 (Random number between 0-9)<br>';
   echo '$c1-$c9 (Random character between a-zA-Z)<br>';
   echo '$w1-$w2 (Random whitespace)<br>';
   echo '</p>';
   echo '<p>';
   echo '<label>Input:</label><br>';
   echo '<input type="text" name="before" value="x@xn--script-$c1$1$2$3">';
   echo '</p>';
   echo '<p>';
   echo '<label>Matches:</label><br>';
   echo '<input type="text" name="matches" value="@<script@">';
   echo '</p>';
   echo '<p>';
   echo '<label>Contains:</label><br>';
   echo '<input type="text" name="contains" value="@[<]@">';
   echo '</p>';
   echo '<p><input type=submit value="Fuzz"></p>';
   echo '</form>';
   exit();
} else {
   echo '<p><input type=submit value="Cancel" onclick="location=\'punycode-fuzzer.php\'" /></p>';
}
for($i=0;$i<0xffff;$i++) {
   if($randomPad) {
      $paddingAmount1 = random_int(0,5);
      $paddingAmount2 = random_int(0,5);
      $paddingAmount3 = random_int(0,5);
      $paddingAmount4 = random_int(0,5);
      $paddingAmount5 = random_int(0,5);
      $paddingAmount6 = random_int(0,5);
      $paddingAmount7 = random_int(0,5);
      $paddingAmount8 = random_int(0,5);
      $paddingAmount9 = random_int(0,5);
   } else {
      $paddingAmount1 = 0;
      $paddingAmount2 = 0;
      $paddingAmount3 = 0;
      $paddingAmount4 = 0;
      $paddingAmount5 = 0;
      $paddingAmount6 = 0;
      $paddingAmount7 = 0;
      $paddingAmount8 = 0;
      $paddingAmount9 = 0;
   }
   $int1 = str_pad(random_int(0,9), $paddingAmount1, "0", STR_PAD_LEFT);
   $int2 = str_pad(random_int(0,9), $paddingAmount2, "0", STR_PAD_LEFT);
   $int3 = str_pad(random_int(0,9), $paddingAmount3, "0", STR_PAD_LEFT);
   $int4 = str_pad(random_int(0,9), $paddingAmount4, "0", STR_PAD_LEFT);
   $int5 = str_pad(random_int(0,9), $paddingAmount5, "0", STR_PAD_LEFT);
   $int6 = str_pad(random_int(0,9), $paddingAmount6, "0", STR_PAD_LEFT);
   $int7 = str_pad(random_int(0,9), $paddingAmount7, "0", STR_PAD_LEFT);
   $int8 = str_pad(random_int(0,9), $paddingAmount8, "0", STR_PAD_LEFT);
   $int9 = str_pad(random_int(0,9), $paddingAmount9, "0", STR_PAD_LEFT);
   $chr1 = $chrs[array_rand($chrs, 1)];
   $chr2 = $chrs[array_rand($chrs, 1)];
   $chr3 = $chrs[array_rand($chrs, 1)];
   $chr4 = $chrs[array_rand($chrs, 1)];
   $chr5 = $chrs[array_rand($chrs, 1)];
   $chr6 = $chrs[array_rand($chrs, 1)];
   $chr7 = $chrs[array_rand($chrs, 1)];
   $chr8 = $chrs[array_rand($chrs, 1)];
   $chr9 = $chrs[array_rand($chrs, 1)];
   $w1 = $whitespace[array_rand($whitespace, 1)];
   $w2 = $whitespace[array_rand($whitespace, 1)];
   $input = str_replace('$1', $int1, $before);
   $input = str_replace('$2', $int2, $input);
   $input = str_replace('$3', $int3, $input);
   $input = str_replace('$4', $int4, $input);
   $input = str_replace('$5', $int5, $input);
   $input = str_replace('$6', $int6, $input);
   $input = str_replace('$7', $int7, $input);
   $input = str_replace('$8', $int8, $input);
   $input = str_replace('$9', $int9, $input);
   $input = str_replace('$c1', $chr1, $input);
   $input = str_replace('$c2', $chr2, $input);
   $input = str_replace('$c3', $chr3, $input);
   $input = str_replace('$c4', $chr4, $input);
   $input = str_replace('$c5', $chr5, $input);
   $input = str_replace('$c6', $chr6, $input);
   $input = str_replace('$c7', $chr7, $input);
   $input = str_replace('$c8', $chr8, $input);
   $input = str_replace('$c9', $chr9, $input);
   $input = str_replace('$w1', $w1, $input);
   $input = str_replace('$w2', $w2, $input);
   $valid = false;

   try {
      $after = $IDN->convertEmailAddress($input);  
      $valid = true;
   } catch(TypeError $e){} catch(\Algo26\IdnaConvert\Exception\InvalidCharacterException $e){} catch(DivisionByZeroError $e){}   
   
   if(!$valid) {
      continue;
   }
   
   if(preg_match($matches, substr($after,2))) {
      echo "<b style=color:red>Found match!</b><br>";
      echo "Input urlencoded:".urlencode($input)."<br>";
      echo "Input:$input<br>";
      echo "After encoded:".htmlentities($after)."<br>";
      echo "After:".$after."<br>";
      $matched = true;
      exit;
   }

   $found = preg_match($contains, substr($after,2));

   // if($foundEquals && preg_match("@[\\x00-\\x7f]{2,}@", substr(str_replace("=","",$after),2)) && !str_contains($after, "xn--")) {
   //    echo "Found equals followed by another char!<br>";
   //    echo "Input:$input<br>";
   //    echo "After:".$after."<br>";
   //    exit;
   // }

   if($found && !str_contains($after, "xn--")) {
      echo "Found char!<br>";
      echo "Input:$input<br>";
      echo "After encoded:".htmlentities($after)."<br>";
      echo "After:".$after."<br>";
   }

   // if(preg_match("@[\\x00-\\x7f]{2,}@", substr($after,2)) && !str_contains($after, "xn--")) {
   //    echo "Found two ascii chars!<br>";
   //    echo "Input:$input<br>";
   //    echo "After:".$after."<br>";
   //    exit;
   // }
}             
echo date("H:i:s") . " fuzz completed. Running new job.";
echo '<script>location.reload()</script>';
?>