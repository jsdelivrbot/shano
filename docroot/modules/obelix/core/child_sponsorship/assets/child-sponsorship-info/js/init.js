(function ($, Drupal, window) {
  Drupal.behaviors.child_sponsorship_child_sponsorship_info = {
    attach: function (context, settings) {
// =============================
// Smooth-Scrolling
// =============================
      $('a.smooth-scroll[href*=\\#]:not([href=\\#])').click(function () {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
          var target = $(this.hash);
          var hash = $(this).attr("href");
          target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');

          naviOffset = 0;
          if (!mobileDevice) {
            naviOffset = $(".main-nav-cont .main-nav").height();
            if ($("#toolbar-bar").length) {
              naviOffset = naviOffset + $("#toolbar-bar").height();
            }
          }
          if (target.length) {
            $('html,body').animate({
              scrollTop: target.offset().top - naviOffset
            }, 1000);
            if (history.pushState) {
              history.pushState(null, null, hash);
            }
            else {
              location.hash = hash;
            }
            return false;
          }
        }
      });

      // Set footer-padding for position fixed child-finder
      function setChildFinderPadding() {
        $childFinderSec = $(".child-sponsorship-section-child-finder");
        $footer = $("footer");

        if ($childFinderSec.length && !mobileDevice) {
          childFinderHeight = $childFinderSec.outerHeight();
          $footer.css("padding-bottom", childFinderHeight);
        }
        else{
          $footer.css("padding-bottom", 0);
        }
      }

      // Fix slider height on resize
      // =============================
      $(window).resize(function () {
        // SetHeight for slider on resize
        $editorialHeroSlider = $(".editorial-hero-slider");
        if ($editorialHeroSlider.length) {
          $editorialHeroSlider.find("inner-content").slick('setPosition');
        }
        // setChildFinderPadding();
      });
      $( window ).load(function() {
        // setChildFinderPadding();
      });
    }
  }
})(jQuery, Drupal, window);

