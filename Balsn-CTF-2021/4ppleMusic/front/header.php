<?php

function curl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

$bg = Array("bg1.jpg", "bg2.jpg", "bg3.jpg", "bg4.jpeg", "bg5.jpeg", "bg6.jpeg", "bg7.jpeg");

?>
<html>
<head>
<meta charset="UTF-8">
<title>4pple Music</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
<style>
body {
  height: 800px;
  width: 100vw;
  background-color: #e6e6e6;
}

.banner{
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  position:absolute;
}
.banner::before {
  content: "";
  position: absolute;
  width: 100%;
  height: 100%;
  background: url(<?= "img/".$bg[rand()%7]; ?>) no-repeat center top;
  background-size: cover;
  overflow:hidden;
  -webkit-filter: blur(15px);
  -moz-filter: blur(15px);
  -o-filter: blur(15px);
  -ms-filter: blur(15px);
  filter: blur(15px);
  -webkit-transform: scale(1.2, 1.2);
  -moz-transform: scale(1.2, 1.2);
  -o-transform: scale(1.2, 1.2);
  -ms-transform: scale(1.2, 1.2);
  transform: scale(1.2, 1.2);
}

.screen {
  width: 400px;
  height: 700px;
  border-radius: 5px;
  box-shadow: 0px 0px 20px 0px rgba(17,17,17,0.7);
  position: absolute;
  top: 5%;
  left: 50%;
  transform: translateX(-200px);
  display: flex;
  justify-content: center;
  background-color: #999;
}
.screen .back-card {
  background-color: #b3b3b3;
  width: 380px;
  height: 680px;
  position: absolute;
  bottom: 0px;
  border-radius: 5px;
  left: 10px;
}
.screen .main-view {
  background-color: #e6e6e6;
  width: 400px;
  height: 673px;
  position: absolute;
  bottom: 0px;
  border-radius: 5px;
  left: 0px;
}
.screen .main-view .time {
  width: 350px;
  position: absolute;
  left: 1%;
  top: 0px;
}
.screen .main-view .time .options {
  color: #ff4981;
  font-size: 50%;
  position: absolute;
  top: 645px;
  right: -6%;
}
.screen .main-view .time .fa.fa-volume-up {
  color: #b3b3b3;
  position: absolute;
  top: 613px;
  right: -6%;
}
.screen .main-view .time .fa.fa-volume-off {
  color: #b3b3b3;
  position: absolute;
  top: 613px;
  left: 7%;
}


.volume-slider {
  -webkit-appearance: none;
  outline : none;
  position: absolute;
  height: 3px;
  width: 300px;
  background-color: #c0c0c0;
  left: 13%;
  border-radius: 500px;
  top: 620px;
}

.volume-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  position: relative;
  height: 20px;
  width: 20px;
  background-color: #e6e6e6;
  box-shadow: 0px 3px 10px 0px rgb(17 17 17 / 50%);
  border-radius: 50%;
}

.screen .main-view .time .volume::after {
  content: " ";
  position: absolute;
  height: 20px;
  width: 20px;
  box-shadow: 0px 3px 10px 0px rgba(17,17,17,0.5);
  border-radius: 50%;
  right: 0px;
  margin-top: -10px;
  background-color: #e6e6e6;
}
.screen .main-view .time i.fa.fa-backward.fa-2x {
  position: absolute;
  top: 550px;
  text-align: center;
}
.screen .main-view .time i.fa.fa-pause.fa-2x {
  position: absolute;
  top: 550px;
  text-align: center;
}

.screen .main-view .time i.fa.fa-play.fa-2x {
  position: absolute;
  top: 550px;
  text-align: center;
}
.screen .main-view .time i.fa.fa-forward.fa-2x {
  position: absolute;
  top: 550px;
  text-align: right;
}
.screen .main-view .time p.singer-name {
  position: absolute;
  font-size: 130%;
  width: 400px;
  text-align: center;
  top: 470px;
  color: #ff4981;
}
.screen .main-view .time p.song-title {
  position: absolute;
  font-size: 130%;
  width: 400px;
  text-align: center;
  top: 440px;
}
.screen .main-view .time .time-played {
  position: absolute;
  top: 420px;
  font-size: 80%;
  left: 7%;
  color: #8d8d8d;
}
.screen .main-view .time .time-left {
  position: absolute;
  top: 420px;
  font-size: 80%;
  right: -5%;
  color: #8d8d8d;
}
.screen .main-view .time .played {
  height: 3px;
  width: 30px;
  background-color: #8d8d8d;
  border-radius: 500px;
  position: absolute;
  top: 420px;
  left: 6.5%;
}
.screen .main-view .time .played::after {
  content: " ";
  position: absolute;
  right: 0px;
  height: 10px;
  width: 10px;
  border-radius: 500px;
  background-color: #8d8d8d;
  margin-top: -2.5px;
}
.screen .main-view .time .playtime {
  height: 3px;
  width: 350px;
  background-color: #b3b3b3;
  border-radius: 500px;
  position: absolute;
  top: 420px;
  left: 6.5%;
}
.screen .main-view .song-cover {
  width: 350px;
  height: 350px;
  border-radius: 5px;
  position: absolute;
  top: 40px;
  left: 6.5%;
  box-shadow: 0px 15px 20px 0px rgba(0,0,0,0.8);
}
.screen .main-view .arrow {
  position: absolute;
  top: 15px;
  left: 50%;
  height: 5px;
  width: 30px;
  background-color: #b3b3b3;
  border-radius: 500px;
  transform: rotate(-20deg) translateX(-10px) scale(0.7);
}
.screen .main-view .arrow::after {
  content: " ";
  position: absolute;
  height: 5px;
  width: 30px;
  background-color: #b3b3b3;
  left: -25px;
  transform: rotate(40deg);
  border-radius: 500px;
  top: -10px;
}
</style>
</head>
