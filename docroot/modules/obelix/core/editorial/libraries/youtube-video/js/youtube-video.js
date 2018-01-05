(function ($, Drupal, window) {
  Drupal.behaviors.editorial_youtube_video = {
    attach: function (context, settings) {
      // Init youtube video
      // =============================
      var yt_players = [];
      var player = [];
      var container = ".editorial-youtube-video";

      function initYoutubeVideo(player) {
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/player_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        function createPlayer(player) {

          $playerId = $("#" + player.id);

          // Play
          $playBtn = $playerId.closest(container).find(".yt-play");

          $playBtn.click(function (e) {
            e.preventDefault();

            $this = $(this);
            $closeBtn = $this.closest(container).find(".yt-close");
            $ytPreviewImg = $this.closest(container).find(".yt-preview-img");
            $ytPreviewTxt = $this.closest(container).find(".yt-preview-txt");

            $ytPreviewImg.fadeOut();
            $closeBtn.fadeIn();

            // Hide preview-text only on desktopdevice with 12er col
            if (!mobileDevice && !$this.closest("[class^='col-']").hasClass("col-sm-6")) {
              $ytPreviewTxt.fadeOut();
            }
          });

          // Close
          $closeBtn = $playerId.closest(container).find(".yt-close");

          $closeBtn.click(function (e) {
            e.preventDefault();

            $this = $(this);
            $ytPreviewImg = $this.closest(container).find(".yt-preview-img");
            $ytPreviewTxt = $this.closest(container).find(".yt-preview-txt");

            $this.fadeOut();
            $ytPreviewImg.fadeIn();
            if (!mobileDevice) {
              $ytPreviewTxt.fadeIn();
            }

            // Pause video
            for (var i = 0; i < yt_players.length; i++) {
              if (yt_players[i]["a"]["id"] == ($this.closest(container).find(".yt-container").attr("id"))) {
                yt_players[i].pauseVideo();
              }
            }
          });

          return new YT.Player(player.id, {
            height: '547',
            width: '972',
            videoId: player.videoId,
            playerVars: {
              rel: 0,
              controls: 1,
              autohide: true,
              color: "white",
              showinfo: false,
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
          }


          if (typeof window.gambitOtherYTAPIReady !== 'undefined') {
            if (window.gambitOtherYTAPIReady.length) {
              window.gambitOtherYTAPIReady.pop()();
            }
          }
        }
      }

      $(container, context).find('.yt-container').once('editorial-youtube-video-init').each(function (i, v) {

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
