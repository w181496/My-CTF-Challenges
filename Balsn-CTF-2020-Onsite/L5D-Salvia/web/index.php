<?php session_start(); ?>
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
<h1 class="ui header">L5D-Salvia Dashboard</h1>
<div class="ui divider"></div>
<a href="source.php">L5D-Salvia</a> | <a href="#submit">Submit</a> | <a href="score.php">Scoreboard</a>
<br>
<div class="ui raised very padded text container segment">
  <h2 class="ui header">Rules</h2>
  <h3>題目介紹</h3>
  <p>你的目標是透過 <a href="source.php">L5D-Salvia</a> 的功能去完成指定的任務，任務請參考以下任務說明。 </p>
  <p>各隊伍在每一個 Round 最多提交三次 Payload。</p>
  <p>Payload 會作為 <a href="source.php">L5D-Salvia</a> 的輸入，並且開始計時，直到程式結束或 Timeout。</p>
  <h3>任務說明</h3>
  <p><b>目標0</b>：針對系統隨機生成的陣列，計算前兩元素相加結果，並輸出到檔案<code>[TEAM-TOKEN].txt</code> </p>
  <p><b>目標1</b>：針對系統隨機生成的陣列，取得元素最大值，並將結果輸出到檔案<code>[TEAM-TOKEN].txt</code> </p>
  <p><b>目標2</b>：針對系統隨機生成的陣列，由小到大做排序，並將結果輸出到檔案<code>[TEAM-TOKEN].txt</code> </p>
  <p>排名：答案錯誤者，不列入排名。答案正確者，花費時間愈少，排名愈前。相同秒數者，則比 payload 長度，愈短排名愈前。</p>
  <p>時間：取秒數小數點後一位，舉例：花費 3.12556343545 秒，則取 3.1 秒作為成績</p>
  <p>Timeout: <code>10</code>秒</p>

  <h3>目標細節</h3>
  <p>三種目標擇一完成即可列入排名。</p>
  <p>每一個 Round 的三次提交，可以選擇不同目標來做，最後結果只會取最佳的排名</p>
  <p>每一個 Round 開始時，會用<b>上一個 Round 答案正確的最後一次</b>payload先跑一次<br>(注意這裡不一定是用上一Round的最佳payload)<br>(不會佔用 3 次提交的額度)</p>
  <p>排名優先權：目標2 > 目標1 > 目標0。<br>舉例：完成目標2後的排名，不論時間或payload長短，永遠在目標1和目標0排名之上。</p>
  <p>相同優先權之目標，則同樣先按照花費時間與payload長度分排名順序。</p>
  <p>
  舉例：<br>
  TeamA 完成目標1，花費時間1.1s，長度30<br>
  TeamB 完成目標2，花費時間3.2s，長度65<br>
  TeamC 完成目標2，花費時間2.8s，長度70<br>
  TeamD 完成目標1，花費時間1.1s，長度25<br>
  TeamE 完成目標0，花費時間0.1s，長度15<br>
  則最終排名順序為: TeamC、TeamB、TeamD、TeamA、TeamE
  </p>

  <h3>測資</h3>
  <p>每一個 Round 會生成兩筆測資，目標0和目標1使用測資1(<code>testcase.txt</code>)，目標2使用測資2(<code>testcase2.txt</code>)。在同一個 Round 中，測資不會變動</p>
  <p>測資1包含 <code>n</code> 個正整數 <code>arr[i]</code> (<code>100000 < n < 1000000</code>, <code>0 <= i < n</code>, <code>0 < arr[i] <= 300000000</code>)</p>
  <p>測資2包含 <code>n</code> 個正整數 <code>arr[i]</code> (<code>100 < n < 500</code>, <code>0 <= i < n</code>, <code>0 < arr[i] <= 300000000</code>)</p>
  <h3>範例輸入</h3>
  <pre>156 15 9006 87 5487</pre>
  <h3>範例輸出 (目標0)</h3>
  <pre>171</pre>
  <h3>範例輸出 (目標1)</h3>
  <pre>9006</pre>
  <h3>範例輸出 (目標2)</h3>
  <pre>15 87 156 5487 9006</pre>

  <h3>計分</h3>
  <p>每 Round 排名第一可取得 10 分</p>
  <p>每 Round 排名第二可取得 5 分</p>
  <p>每 Round 排名第三可取得 3 分</p>
  <p>每 Round 排名第四可取得 2 分</p>
  <p>每 Round 排名第五可取得 1 分</p>
  <p>若時間、長度、目標皆相同之隊伍，可取得相同分數。</p>
</div>

<div id="submit" class="ui raised very padded text container segment">
    <form method="post" action="post.php">
    <div class="ui form"><grammarly-extension style="position: absolute; top: 0px; left: 0px; pointer-events: none;" class="_1KJtL"></grammarly-extension>
    <div class="field">
        <label>Your Payload:</label>
        <textarea id="textarea" spellcheck="false" name="payload"></textarea>
        <div id="count-div">Length: <span id="count">0</span></div>
    </div>
    <div class="field">
        <label>Team Token: </label>
        <input type="text" name="token" placeholder="Team token.." value="<?php if(isset($_SESSION['token'])) echo $_SESSION['token']; ?>" >
    </div>

    <div class="field ui form">
        <div class="inline fields">
            <div class="field">
                <div class="ui radio checkbox">
                    <input type="radio" name="target" value="0">
                    <label>目標0</label>
                </div>
            </div>
            <div class="field">
                <div class="ui radio checkbox">
                    <input type="radio" name="target" value="1">
                    <label>目標1</label>
                </div>
            </div>
            <div class="field">
                <div class="ui radio checkbox">
                    <input type="radio" name="target" value="2">
                    <label>目標2</label>
                </div>
            </div>
        </div>
    </div>

    <div class="field">
        <button class="ui teal button">提交</button>
    </div>
    </form>

</div>
</div>
<br><br>
</div>
</body>
<script>
$("#textarea").keyup(function(){
  $("#count").text($(this).val().length);
});
</script>
</html>
