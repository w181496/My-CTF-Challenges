<?php
$mapping = Array(
    "MGiTW2wkoNQ" => ["title" => "Burgundy Red", "singer" => "Sunset Rollercoaster"],
    "1cmcA48MXoU" => ["title" => "Libidream", "singer" => "Sunset Rollercoaster"],
    "Dxq0IqhT3XI" => ["title" => "Slow", "singer" => "Sunset Rollercoaster"],
    "5xwFCtDc0fI" => ["title" => "My Jinji", "singer" => "Sunset Rollercoaster"],
    "BeDWWYpD5M4" => ["title" => "Under the Skin", "singer" => "Sunset Rollercoaster"],
    "WsceOJiN-Lo" => ["title" => "Greedy", "singer" => "Sunset Rollercoaster"],
    "_WcdymteFoc" => ["title" => "Almost Mature 87'", "singer" => "Sunset Rollercoaster"],
    "dQw4w9WgXcQ" => ["title" => "Never Gonna Give You Up", "singer" => "Rick Astley"],
    "saNzVNhMSn0" => ["title" => "", "singer" => ""],
);

if(!isset($_GET['vid'])) {
    exit("Error!");
}

$vid = $_GET['vid'];

if(!in_array($vid, array_keys($mapping))) { 
    exit("Song not found!");
}

$title = $mapping[$vid]['title'];
$singer = $mapping[$vid]['singer'];
unset($mapping[$vid]);
$keys = array_keys($mapping);

?>
<div class="screen">
	<div class="back-card"></div>
	<div class="main-view">
		<div class="arrow"></div>
    <iframe id="player" src="https://www.youtube.com/embed/<?= $vid; ?>?autoplay=1&enablejsapi=1&allowsInlineMediaPlayback=true&playsinline=1&rel=0&controls=0&autohide=1&loop=1" alt="Song Cover" class="song-cover" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		
    <div class="time">
			<p class="song-title"><?= $title; ?></p>
			<p class="singer-name"><?= $singer; ?></p>
      <div style="padding-left: 271px;">
			  <i class="fa fa-forward fa-2x" onclick=document.getElementById("nextsong").submit()></i>
    </div>
      <div style="padding-left: 187px;">
        <i class="fa fa-play fa-2x" id="play"></i>
      </div>
      <div style="padding-left: 100px;">
			  <i class="fa fa-backward fa-2x" onclick=document.getElementById("nextsong").submit()></i>
      </div>
      <input type="range" min="1" max="100" value="50" class="volume-slider" id="volume">
			<i class="fa fa-volume-off" id="toggleMute"></i>
			<i class="fa fa-volume-up" id="toggleunMute"></i>
			<div class="options">
			</div>
		</div>
	</div>
</div>
<form method="post" action="index.php" id="nextsong">
    <input type="hidden" name="url" value="http://127.0.0.1/get.php?vid=<?= $keys[rand()%count($keys)]; ?>">
</form>
