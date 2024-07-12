<?php
require 'vendor/autoload.php';
use Algo26\IdnaConvert\ToUnicode;
use Algo26\IdnaConvert\ToIdn;

function toHex($input) {
    $output = [];
    $chars = mb_str_split($input);
    for($i=0;$i<count($chars);$i++) {
        $hex = str_pad(dechex(mb_ord($chars[$i])), 2, "0", STR_PAD_LEFT);
        $hex .= '('.$chars[$i].')';
        array_push($output, $hex);
    }
    return join(' ', $output);
}

$output = "";
$showHex = isset($_GET['showHex']) && $_GET['showHex'] ? true : false;
$input = isset($_GET['input']) ? $_GET['input'] : "x@test.xn--lzg";
$mode = isset($_GET['mode']) ? (int) $_GET['mode'] : 2;
$hex = "";
$error = "";
if($mode === 1) {
    $IDN = new ToIdn();
    try {
        $output = $IDN->convert($input);
    } catch(\Exception $e) {
        $error = $e;
    } catch(\TypeError $e) {
        $error = $e;
    }
} else if($mode === 2) {
    $IDN = new ToUnicode();
    try {
        $output = $IDN->convertEmailAddress($input);
    } catch(\Exception $e) {
        $error = $e;
    } catch(\TypeError $e) {
        $error = $e;
    }
    
} 

?>
<!doctype html>
<html>
    <head>
        <meta name="referrer" content="none">
        <meta charset="UTF-8" />
        <title>Punycode PortSwigger challenge</title>
        <style>
            textarea {
                width: 600px;
                height: 300px;
            }
            .error {
                color:red;
                font-weight: bold;
            }
            * {
                max-width: 90ch;
                font-family: Arial;
            }
            .warn {
                color:red;
                font-weight: bold;
            }    
        </style>
    </head>
    <body>
            <div>                
                <p>
                    Here are some examples:
                </p>
                <ul>
                    <li>Input:x@xn--style-321<br>Output:x@&lt;style</li>
                    <li>Input:x@xn--024<br>Output:x@@</li>
                    <li>Input: xn--x-0314.xn--0026.xn--0193.xn--0218<br>Output: &lt;x.. .=</li>
                    <li>Input: test@xn--x-0314.xn--0026.xn--0193.xn--54<b class="warn">_</b>52932<br>Output: test@&lt;x.. .='</li>
                </ul>
                <p class="warn">
                    Notice the last example contains an underscore. Unfortunately the target in question does not allow underscore in the email address. You are restricted to: a-zA-Z0-9.-
                </p>
                <p>To solve this challenge you have to generate a HTML tag with a single quoted opening attribute using only the characters <b class="warn">a-zA-Z0-9.-</b> in the domain part of the address.</p>
                <p>E.g. What I'm looking for is &lt;x y='</p>
                <p>Where x can be any valid tag and y can be any valid attribute name</p>
                <ul>
                    <li>Valid tag names: &lt;[a-zA-Z][^\s/]*</li>
                    <li>Valid attribute names: [^\s&gt;/]+</li>
                </ul>    
            </div>

            <form>
                <p>
                    <label>Mode:</label><br>
                    <select name="mode">                
                        <option<?=$mode===1?' selected="selected"':''?> value="1">Encode</option>
                        <option<?=$mode===2?' selected="selected"':''?> value="2">Decode</option>
                    </select>
                </p>
                <p>
                    <label>Input(Cmd + Return to submit):</label><br>
                    <textarea autofocus name="input" onkeydown="if(event.metaKey&&event.key==='Enter')this.form.submit()"><?=htmlentities($input, ENT_QUOTES, "UTF-8")?></textarea>
                </p>
                <?php if($showHex):?>
                <p>
                    Input (in hex):<br><?=htmlentities(toHex($input), ENT_QUOTES, "UTF-8")?>
                </p>
                <?php endif?>
                <p>
                    Output (encoded):<br><?=htmlentities($output)?>
                </p>
                <p>
                    Output:<br><?=$output?>
                </p>
                <?php if($showHex):?>
                <p>
                    Output (in hex):<br><?=toHex($output)?>
                </p>
                <?php endif?>
                <?php IF($error):?>
                    <p class="error">An error occurred:<?=htmlentities($error, ENT_QUOTES, "UTF-8")?></p>
                <?php ENDIF?>
                <p>
                    <input type="reset" value="Clear" /> <input type="submit" value="Convert" />
                    <input type="hidden" name="showHex" value="0" />
                </p>

                <h2>Useful links</h2>
                <ul>
                    <li><a href="punycode-fuzzer.php">Punycode Fuzzer</a></li>
                    <li><a href="fuzzer-source.zip">Punycode Fuzzer source</a></li>
                    <li><a href="https://www.rfc-editor.org/rfc/rfc3492">Punycode RFC</a></li>
                    <li><a href="https://github.com/algo26-matthias/idna-convert/blob/master/src/Punycode/ToPunycode.php">The vulnerable Punycode library source</a></li>
                </ul>

                <h2>Tips</h2>
                <p>Here are some tips:</p>
                <ul>
                    <li>Punycode subdomains always begin with xn--</li>
                    <li>You can use multiple punycode encoded subdomains</li>
                    <li>Hyphens are used to treat the characters as is</li>
                    <li>You can have whitespace between the attribute equal sign and quote e.g. <pre>&lt;x x    =   '</pre></li>
                </ul>
            </form>
            <script>
            let element = document.querySelector('textarea');
            element.selectionStart = element.value.length;
            </script>
    </body>
</html>