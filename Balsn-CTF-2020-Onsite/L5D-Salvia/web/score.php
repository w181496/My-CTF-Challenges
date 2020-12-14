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
<h1 class="ui header">Current Scoreboard ( Round #<?php echo file_get_contents("/round/number.txt"); ?> )</h1>
<div class="ui divider"></div>
<a href="index.php">Home</a> | <a href="fine/">Fine</a>
<br>
<div class="ui raised very padded text container segment">
<table class="ui celled table">
  <thead>
    <tr>
    <th>Rank</th>
    <th>Team</th>
    <th>Time</th>
    <th>Length</th>
    <th>Target</th>
    </tr>
  </thead>
  <tbody>
    <?php 
        if(file_exists("/round/rank.txt"))
            $score_list = explode("\n", trim(file_get_contents("/round/rank.txt")));
        else
            $score_list = Array();
        
        $token_list = explode("\n", trim(file_get_contents("/.token_list")));

        // token mapping
        $mapping = Array($token_list[0] => "Goburin'",
                         $token_list[1] => "BambooFox",
                         $token_list[2] => "Brain2NOP",
                         $token_list[3] => "NCtfU",
                         $token_list[4] => "10sec",
                         $token_list[5] => "資安食物好難吃");

        $rank = 1;
        $prev = NULL;

        for($i = 0; $i < count($score_list); $i++) {
            
            $line = explode("<SALVIA>", $score_list[$i]);

            if($line[3] == "1")
                echo "<tr class='warning'>";
            else if($line[3] == "2")
                echo "<tr class='negative'>";
            else
                echo "<tr>";

            
            if($prev) {
                if($prev[1] == $line[1] && $prev[2] == $line[2] && $prev[3] == $line[3]) {
                    echo '<td data-label="Rank">'.$rank.'</td>';
                } else {
                    $rank++;
                    echo '<td data-label="Rank">'.$rank.'</td>';
                    $prev = $line;
                }
            } else {
                $prev = $line;
                echo '<td data-label="Rank">'.$rank.'</td>';
            }

            // team name
            echo '<td data-label="Team">';
            echo $mapping[$line[4]];
            echo '</td>';

            // time
            echo '<td data-label="Time">';
            echo $line[1];
            echo '</td>';

            // length
            echo '<td data-label="Length">';
            echo $line[2];
            echo '</td>';

            // type
            echo '<td data-label="Type">';
            echo $line[3];
            echo '</td>';

            echo "</tr>";

        }
    ?>
  </tbody>
</table>
</div>
</div>
</body>
</html>