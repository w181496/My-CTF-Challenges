<script src="https://www.youtube.com/iframe_api"></script>
<script>
      var player;
      function onYouTubeIframeAPIReady() {

        player = new YT.Player('player', {
          events: {
            'onReady': onPlayerReady,
            'onStateChange': changeState
          }
        });

      }

      function changeState(event) {
            if(event.data == -1 || event.data == YT.PlayerState.ENDED){
                if ($("#play").hasClass("fa-pause")) {
                   $("#play").removeClass("fa-pause");
                   $("#play").toggleClass("fa-play");
                }
                if(event.data == YT.PlayerState.ENDED) document.getElementById("nextsong").submit();
            } else if(event.data == YT.PlayerState.PLAYING) {
                if ($("#play").hasClass("fa-play")) {
                   $("#play").removeClass("fa-play");
                   $("#play").toggleClass("fa-pause");
                }
            }
        }

      function onPlayerReady(e) {
        const play = document.getElementById('play');
        const pause = document.getElementById('pause');
        const stop = document.getElementById('stop');
      
        const volume = document.getElementById('volume');
        const mute = document.getElementById('toggleMute');
        const unmute = document.getElementById('toggleunMute');
      
        playFunc = (() => {
            var state = player.getPlayerState();
            if(state == 1) { // playing
                e.target.pauseVideo();
                if ($("#play").hasClass("fa-pause")) {
                   $("#play").removeClass("fa-pause");
                   $("#play").toggleClass("fa-play");
                }
            } else if (state == 2 || state == -1 || state == 0) { // pause // unstarted // finished
                e.target.unMute().playVideo();
                if ($("#play").hasClass("fa-play")) {
                  $("#play").removeClass("fa-play");
                  $("#play").toggleClass("fa-pause");
                }
            }
        });
        play.addEventListener('click', playFunc);
      
        volume.value = e.target.getVolume(); 
        volume.addEventListener('input', () => e.target.setVolume(volume.value))
      
        mute.addEventListener('click', () => e.target.mute().playVideo());
        unmute.addEventListener('click', () => e.target.unMute().playVideo());
      }
</script>
</body>
</html>
