(function ($, Drupal, window) {
  Drupal.behaviors.child_video_src = {
    attach: function (context, settings) {

      $(document).ready(function () {
        // Deactivate right-click an video tag
        $('.child-cont').find('video').bind('contextmenu', function () {
          return false;
        });
      });

      var options = {};
      $childVideos = $('.child-video');

      $childVideos.each(function () {
        childVideoId = "#" + $(this).attr('id');

        player = videojs(childVideoId, options, function onPlayerReady() {
          playerId = "#" + $(this).attr('id');

          // Player ready
          $(playerId).fadeIn();

          // Started
          this.on('playing', function () {
            $(this.el()).closest('.child-cont').find('.child-video-close').fadeIn();
          });

          // Ended
          this.on('ended', function () {
            this.exitFullscreen();
            this.load();
            $(this.el()).closest('.child-cont').find('.child-video-close').fadeOut();
          });
        });
      });

      //  Child-video close
      $(".child-cont").find(".child-video-close").click(function () {
        $this = $(this);
        $this.fadeOut();
        player = videojs($this.closest(".child-cont").find(".child-video").attr('id'));
        player.load();
      });
    }
  }
})(jQuery, Drupal, window);
