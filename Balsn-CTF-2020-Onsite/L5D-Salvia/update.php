<?php
// update every 5 mins

/*

su - www-data -s php update.php

*/

$SPLIT = "<SALVIA>";

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
    global $SPLIT;
    $tokenlist_path = "/.token_list";
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
    if($output != "") {
        if(file_put_contents("/round/rank.txt", $output))
            echo "<br>Score updated!<br>";
        else
            echo "<br>[Error 0x03] Update Score Error!<br>";
    }
}

// get round number
if(file_exists("/round/number.txt"))
    $num = intval(file_get_contents("/round/number.txt"));
else
    die("[Error 0x06] Round number error!");

$token_list = explode("\n", trim(file_get_contents("/.token_list")));

// send data back to server?
if(file_exists("/round/rank.txt")) {
    $scores = explode("\n", trim(file_get_contents("/round/rank.txt")));
    $prev = NULL;
    $send_score = Array();
    $s = 10;
    $sc = Array(10 => 5,
                5 => 3,
                3 => 2,
                2 => 1,
                1 => 0);
    for($i = 0; $i < count($scores); $i++) {
        $score = explode("<SALVIA>", trim($scores[$i]));
        $tmp = Array();
        if($prev) {
            // Y, Time, Length, Type, Token
            if($prev[0] == $score[0] && $prev[1] == $score[1] && $prev[2] == $score[2] && $prev[3] == $score[3]) {
                $tmp["token"] = $score[4];
                $tmp["score"] = $s;
            } else {
                $s = $sc[$s];
                $tmp["token"] = $score[4];
                $tmp["score"] = $s;
            }
            $prev = $score;
        } else {
            $prev = $score;
            $tmp["token"] = $score[4];
            $tmp["score"] = $s;
        }
        // check token again
        if(strlen($tmp["token"]) == 32) 
            array_push($send_score, $tmp);
    }
    // find the zero point guy
    if(count($send_score) != 6) {
        for($i = 0; $i < count($token_list); $i++) {
            $token = $token_list[$i];
            $found = false;
            for($j = 0; $j < count($send_score); $j++) {
                $_token = $send_score[$j]["token"];
                if($token == $_token) {
                    $found = true;
                    break;
                }
            }
            if(!$found) {
                $tmp = Array();
                $tmp["token"] = $token;
                $tmp["score"] = 0;
                // check token again
                if(strlen($tmp["token"]) == 32) 
                    array_push($send_score, $tmp);
            }
        }
    }

    // send
    $json_data = json_encode($send_score);
    file_put_contents("/log/log.txt", $num . ":" . $json_data . "\n", FILE_APPEND); // log
    // send score back to server
    system('curl -X POST -d \'{ "kohId": 1, "timestamp": "'.$num.'", "score_list": '.$json_data.'}\' http://kohserver/KOHScore');

}


//  update round number
$num++;
file_put_contents("/round/number.txt", $num);

// clear rank
if(file_exists("/round/rank.txt"))
    unlink("/round/rank.txt");

// clear score
for($i = 0; $i < count($token_list); $i++) {
    if(file_exists("/score/". $token_list[$i] . ".txt")) {
        unlink("/score/".$token_list[$i].".txt");
    }
}

// clear quota
for($i = 0; $i < count($token_list); $i++) 
    file_put_contents("/payload/" . $token_list[$i] . "-cnt.txt", "0");

// Re-Generate testcase
system("cd /bot/gen && python gen.py");

// Re-Run every teams' payload
for($i = 0; $i < count($token_list); $i++) {
    $_token = trim($token_list[$i]);
    if(file_exists("/payload/".$_token."-payload.txt")) {
        echo "Running $token payload....\n";
        $_target = trim(file_get_contents("/payload/".$_token."-target.txt"));
        $score_path = "/score/".$_token.".txt";

        $starttime = microtime(true);
        system("cd /bot && TEAM_TOKEN=".$_token." timeout 10 php index.php /payload/".$_token."-payload.txt");
        $endtime = microtime(true);
        $timediff = $endtime - $starttime;

        if(file_exists("/bot/output/".$_token.".txt")) {

            if(file_exists("/payload/".$_token."-payload.txt"))
                $_payload = file_get_contents("/payload/".$_token."-payload.txt");
            else
                die("[Error 0x05] Payload not found!");

            $your_ans = trim(file_get_contents("/bot/output/".$_token.".txt"));
            $real_ans = trim(file_get_contents("/bot/gen/answer".$_target.".txt"));
            $timediff = floor($timediff * 10) / 10;
        
            $is_correct = ($your_ans == $real_ans);
            $payload_len = strlen($_payload);
        
            echo "[Time]: $timediff s<br>";
            echo "[Your answer]: $your_ans<br>";
            echo "[Status]: ". ($is_correct ? "<span style='color:green'>Correct</span>" : "<span style='color:red'>Wrong Answer</span>") . "<br>";
            echo "[Payload length]: ".$payload_len;
            echo "<hr>";
            echo "<a href='/index.php'>Go back</a>";
            
            if($is_correct) {
        
                $res_data = ($is_correct ? "Y" : "N");
                $res_data .= $SPLIT;
                $res_data .= $timediff;
                $res_data .= $SPLIT;
                $res_data .= $payload_len;
                $res_data .= $SPLIT;
                $res_data .= $_target;
                $res_data .= $SPLIT;
                $res_data .= $_token;
        
                if(file_exists($score_path)) {
                    $best_score = file_get_contents($score_path);
                    if(compare(explode($SPLIT, $res_data), explode($SPLIT, $best_score)) == -1) {
                        file_put_contents($score_path, $res_data);
                    }
                } else {
                    file_put_contents($score_path, $res_data);
                }
            }
        
        } else {
            echo "QQ";
        }
    }
}
scoreboard_refresh();