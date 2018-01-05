(function ($, Drupal, window) {
  Drupal.behaviors.editorial_hero_slider_preview_video = {
    attach: function (context, settings) {

      // Init youtube video
      // =============================
      var yt_players = [];
      var player = [];
      var container = ".editorial-hero-slider-preview-video";

      function initYoutubeVideo(player) {

        var $heroSlider = $("#" + player[0]['id']).closest('.editorial-hero-slider').find('.inner-content');

        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/player_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        function createPlayer(player) {
          $playerId = $("#" + player.id);
          autoPlay = 0;

          // Autoplay only for desktop clients
          if ($(window).width() >= 768) {
            autoPlay = 1;
          }

          return new YT.Player(player.id, {
            height: '547',
            width: '972',
            videoId: player.videoId,
            playerVars: {
              autoplay: autoPlay,
              rel: 0,
              controls: 0,
              autohide: true,
              showinfo: false,
              modestbranding: 1,
              wmode: "transparent",
            }
          });
        }

        // Checks if onYouTubePlayerAPIReady is already fired
        if (typeof window.onYouTubePlayerAPIReady !== 'undefined') {
          if (typeof window.gambitOtherYTAPIReady === 'undefined') {
            window.gambitOtherYTAPIReady = [];
          }
          window.gambitOtherYTAPIReady.push(window.onYouTubePlayerAPIReady);
        }
        window.onYouTubePlayerAPIReady = function () {

          // Initialize YT.Player
          if (typeof player === 'undefined')
            return;

          for (var i = 0; i < player.length; i++) {
            yt_players[i] = createPlayer(player[i]);


            // Check Video status
            yt_players[i].addEventListener('onStateChange', function (e) {
              $previewVidCont = $(e.target['a']['offsetParent']).parent();

              // Playing
              if (e.data == 1 && $previewVidCont.hasClass('editorial-hero-slider-preview-video')) {
                e.target.mute(); // Mute video

                // IE work-around for wrong adjusting yt-video
                $previewVidCont.find('.yt-container').css("width", "99.99%");
                setTimeout(function () {
                  $previewVidCont.find('.yt-container').animate({
                    width: "100%"
                  }, 0,function(){
                    $previewVidCont.find('.yt-embed-container').css("opacity", 1);
                  });
                }, 1);
              }

              // Ended
              if (e.data == 0 && $previewVidCont.hasClass('editorial-hero-slider-preview-video')) {
                $previewVidCont.fadeOut(300);
                $heroSlider.delay(2000).slick("slickPlay");
              }
            });
          }
          if (typeof window.gambitOtherYTAPIReady !== 'undefined') {
            if (window.gambitOtherYTAPIReady.length) {
              window.gambitOtherYTAPIReady.pop()();
            }
          }
        }
      }

      $(container, context).find('.yt-container').once('editorial-hero-slider-preview-video-init').each(function (i, v) {

        $ytContainer = $(v);

        entityId = $ytContainer.data("entity-id");
        ytId = $ytContainer.data("yt-id");

        tempPlayer = [
          {
            videoId: ytId,
            id: 'yt-container-' + entityId
          }
        ];
        player = $.merge(player, tempPlayer);

        // Last Index
        var total = $(container).find('.yt-container').length;

        if (i === total - 1) {
          initYoutubeVideo(player);
        }
      });
    }
  }
})(jQuery, Drupal, window);
