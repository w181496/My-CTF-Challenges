<?php

set_time_limit(15);
session_start();

?>

<html>
<head>
<meta charset="UTF-8">
<title>L5D Revenge - Salvia</title>
<link rel="stylesheet" type="text/css" href="semantic.min.css">
<script
  src="https://code.jquery.com/jquery-3.1.1.min.js"
  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
  crossorigin="anonymous"></script>
<script src="semantic.min.js"></script>
<link rel="stylesheet"
      href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.4.0/styles/default.min.css">
 <style>
code {
    color: #c7254e;
    background: #f9f2f4;
    border: 1px solid rgba(0,0,0,0.07);
    margin: 0 3px;
    padding: 1px 3px;
}

#count-div {
    float: right;
    right:0px;
}
 </style>
</head>
<body style="padding-top: 50px">

<div class="ui text container">
<a href="index.php">Go back</a> | <a href="score.php">Scoreboard</a>
<br>
<div class="ui raised very padded text container segment">
<?php
$SPLIT = "<SALVIA>";
$tokenlist_path = "/.token_list";
$score_path = "/score/".$_POST['token'].".txt";
$cnt_path = "/payload/".$_POST['token']."-cnt.txt";

// rank 1 to rank 6
function compare($x, $y) {
    if($x[0] == "N" && $x[0] == $y[0]) return 0;
    if($x[0] == "N" && $y[0] == "Y") return 1;
    if($x[0] == "Y" && $y[0] == "N") return -1;
    $type1 = intval($x[3]);
    $type2 = intval($y[3]);
    if($type1 > $type2) return -1;
    if($type1 < $type2) return 1;
    $time1 = (float)$x[1];
    $time2 = (float)$y[1];
    if($time1 > $time2) return 1;
    if($time1 < $time2) return -1;
    $len1 = intval($x[2]);
    $len2 = intval($y[2]);
    if($len1 > $len2) return 1;
    if($len1 < $len2) return -1;
    return 0;
}

function scoreboard_refresh() {
    global $tokenlist_path, $SPLIT;
    $scores = Array();
    $token_list = explode("\n", trim(file_get_contents($tokenlist_path)));
    for($i = 0; $i < count($token_list); $i++) {
        $token = $token_list[$i];
        if(file_exists("/score/".$token.".txt"))
            array_push($scores, explode($SPLIT, trim(file_get_contents("/score/".$token.".txt"))));
    }
    usort($scores, 'compare');
    $output = "";
    for($i = 0; $i < count($scores); $i++) {
        $score = $scores[$i];
        $output .= implode($SPLIT, $score);
        $output .= "\n";
    }
    if(file_put_contents("/round/rank.txt", $output))
        echo "Score updated!<br>";
    else
        echo "[Error 0x03] Update Score Error!<br>";
}

// get team token list
if(file_exists("/.token_list")) {
    $token_list = explode("\n", trim(file_get_contents("/.token_list")));
} else {
    die("[Error 0x01] Please contact admin!");
}

// no token || no payload || no target
if(!isset($_POST['token']) || !isset($_POST['payload']) || !isset($_POST['target']))
    die("fuck off!");

$_token = $_POST['token'];
$_payload = $_POST['payload'];
$_target = $_POST['target'];

// Check token
if(!in_array($_token, $token_list, true))
    die("fuck off!");

// Double check token
if(stripos($_token, "/") !== FALSE || stripos($_token, "'") !== FALSE)
    die("fuck off!");

// Triple check token
if(strlen($_token) != 32)
    die("fuck off!");

// Check payload
if(strpos($_payload, $SPLIT) !== FALSE)
    die("fuck off!");

// save token (you don't need to type token again!)
$_SESSION['token'] = $_token;

// check quota & cnt++
if(!file_exists($cnt_path))
    file_put_contents($cnt_path, "0");
$cnt = intval(file_get_contents($cnt_path));
if($cnt >= 3) die("Too many submissions! Please wait next round!");
$cnt++;
file_put_contents($cnt_path, $cnt);

// calculate the time
$starttime = microtime(true);
file_put_contents("/tmp/".$_token, $_payload);
system("cd /bot && TEAM_TOKEN=".$_token." timeout 10 php index.php /tmp/".$_token);
$endtime = microtime(true);

$timediff = $endtime - $starttime;

if(file_exists("/bot/output/".$_token.".txt")) {

    $your_ans = trim(file_get_contents("/bot/output/".$_token.".txt"));
    $real_ans = trim(file_get_contents("/bot/gen/answer".$_target.".txt"));
    $timediff = floor($timediff * 10) / 10;

    $is_correct = ($your_ans === $real_ans);
    $payload_len = strlen($_payload);

    echo "[Time]: $timediff s<br>";
    if($timediff >= 10) echo "(Timeout?)<br>";
    //echo "[Your answer]: $your_ans<br>";
    //echo "[Correct answer]: $real_ans<br>";
    echo "[Status]: ". ($is_correct ? "<span style='color:green'>Correct</span>" : "<span style='color:red'>Wrong Answer</span>") . "<br>";
    echo "[Payload length]: ".$payload_len;
    echo "<hr>";
    
    file_put_contents("/log/payloads.txt", $_token . " : " . $_payload . "\n", FILE_APPEND); // log

    if($is_correct) {

        // 只要答案正確就寫入 payload
        file_put_contents("/payload/".$_token."-payload.txt", $_payload);
        file_put_contents("/payload/".$_token."-target.txt", $_target);

        $res_data = ($is_correct ? "Y" : "N");
        $res_data .= $SPLIT;
        $res_data .= $timediff;
        $res_data .= $SPLIT;
        $res_data .= $payload_len;
        $res_data .= $SPLIT;
        $res_data .= $_target;
        $res_data .= $SPLIT;
        $res_data .= $_token;

        // 檢查是否成績較佳
        if(file_exists($score_path)) {
            $best_score = file_get_contents($score_path);
            if(compare(explode($SPLIT, $res_data), explode($SPLIT, $best_score)) == -1) {
                file_put_contents($score_path, $res_data);
                scoreboard_refresh();
            }
        } else {
            file_put_contents($score_path, $res_data);
            scoreboard_refresh();
        }
    }

} else {
    echo "Oops! Where is your output file O_o?!<br>";
}

?>
</body>
</html>