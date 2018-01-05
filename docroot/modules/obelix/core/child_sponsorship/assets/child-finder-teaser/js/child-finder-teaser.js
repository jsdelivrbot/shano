(function ($) {
  $.fn.child_finder_success = function () {
  };
})(jQuery);


(function ($, Drupal, window) {
  Drupal.behaviors.child_finder_teaser = {
    attach: function (context, settings) {
      $htmlBody = $("html, body");

      $teaser = $(".child-finder-teaser");
      $teaserToggle = $(".child-finder-teaser", context).find(".child-finder-teaser-toggle");
      $teaserToggleSection = $(".child-finder-teaser").find(".child-teaser-toggle-sec");
      $teaserToggleIcon = $(".child-finder-teaser").find(".child-finder-teaser-toggle i");

      // Toggle-Button
      // =================
      $teaserToggle.click(function () {
        $this = $(this);

        // Desktop
        if (!mobileDevice) {
          if ($teaserToggleSection.is(":hidden")) {
            $teaserToggleSection.slideDown();
            $teaserToggleIcon.removeClass('rotated');
          }
          else {
            $teaserToggleSection.slideUp();
            $teaserToggleIcon.addClass('rotated');
          }
        }
        // Mobile
        else {
          if ($teaserToggleSection.is(":hidden")) {
            $teaser.fadeOut(function () {
              $teaser.addClass("opened-mobile");
              $teaserToggleSection.css("min-height", $teaser.outerHeight());
              $teaserToggleIcon.removeClass('rotated');

              $teaser.fadeIn();
              $htmlBody.addClass("overflow-hidden");
            });
          }
          else {
            $teaser.fadeOut(function () {
              $teaser.removeClass("opened-mobile");
              $teaser.fadeIn();
              $teaserToggleIcon.addClass('rotated');
              $teaserToggleSection.css("min-height", 0);
              $htmlBody.removeClass("overflow-hidden");
            });
          }
        }
      });

      // Resize
      // =================
      $(window).resize(function () {
        // Desktop
        if (!mobileDevice) {
          // Mobile open on Desktop
          if (!$teaserToggleSection.is(":hidden") && $teaser.hasClass('opened-mobile')) {
            $teaser.fadeOut(function () {
              $teaser.removeClass("opened-mobile");
              $teaser.fadeIn();
              $teaserToggleIcon.addClass('rotated');
              $teaserToggleSection.css("min-height", 0);
              $htmlBody.removeClass("overflow-hidden");
            });
          }
        }
        // Mobile
        else{
          // Desktop open on mobile
          if (!$teaserToggleSection.is(":hidden") && !$teaser.hasClass('opened-mobile')) {
            $teaserToggleSection.slideUp();
            $teaserToggleIcon.addClass('rotated');
          }
        }
      });
    }
  }
})(jQuery, Drupal, window);
