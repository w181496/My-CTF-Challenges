<head>
<meta charset="UTF-8">
<title>KoreaFish</title>
</head>
<form action="/" method="get">
    <input type="text" name="🇰🇷🐟" placeholder="http://54.87.54.87/koreafish?id=1">
    <input type="submit">
</form>

<hr>

<?php

ini_set('default_socket_timeout', 1);

$waf = array("@","#","!","$","%","<", "*", "'", "&", "..", "localhost", "file", "gopher", "flag", "information_schema", "select", "from", "sleep", "user", "where", "union", ".php", "system", "access.log", "passwd", "cmdline", "exe", "fd", "meta-data");

$dst = @$_GET['🇰🇷🐟'];
if(!isset($dst)) exit("Forbidden");

$res = @parse_url($dst);
$ip = @dns_get_record($res['host'], DNS_A)[0]['ip'];

if($res['scheme'] !== 'http' && $res['scheme'] !== 'https') die("Error");
if(stripos($res['path'], "korea") === FALSE) die("Error");

for($i = 0; $i < count($waf); $i++) 
    if(stripos($dst, $waf[$i]) !== FALSE)
        die("<svg/onload=\"alert('發大財!')\">".$waf[$i]);
sleep(1);

// u can only touch this useless ip :p
$dev_ip = "54.87.54.87";
if($ip === $dev_ip) {
    $content = file_get_contents($dst);
    echo $content;
}
