(function ($) {
// =============================
// Calculate Viewport
// =============================
  var vwptHeight = $(window).height();
  var vwptWidth = 0;

  function checkDevice() {
    vwptHeight = $(window).height();
    vwptWidth = $(window).width();

    if (vwptWidth < 768) {
      mobileDevice = 1;
    } else {
      mobileDevice = 0;
    }
    if (vwptWidth >= 992) {
      desktopDevice = 1;
    } else {
      desktopDevice = 0;
    }
  }

  $(document).ready(function () {
    // =============================
    // Initial
    // =============================
    checkDevice();

    $(window).resize(function () {
      checkDevice();
    });
    // =============================
    // Navigation
    // =============================

    // Shrink Subnavi on Scroll
    // =============================
    $(window).scroll(function () {
      if (!mobileDevice) {
        $subNav = $('.main-nav-cont').find(".sub-nav");
        $body = $("body");
        if ($(document).scrollTop() > 450) {
          $subNav.addClass('shrink');
          $body.addClass('nav-shrinked');
        } else {
          $subNav.removeClass('shrink');
          $body.removeClass('nav-shrinked');
        }
      }
    });

    // Transformicons
    // =============================
    $mainNavCont = $('.main-nav-cont');
    $mainNav = $mainNavCont.find('.main-nav');
    $tcon = $mainNavCont.find(".tcon");
    transformicons.add('.tcon');

    $mainNavCont.on('show.bs.offcanvas', function (e) {
      $tcon.addClass('tcon-transform');
    });
    $mainNavCont.on('hide.bs.offcanvas', function (e) {
      $tcon.removeClass('tcon-transform');
    });

    // Searchbar
    // =============================
    $mainNavCont.find(".search-toggle").click(function () {
      $this = $(this);
      $body = $("body");

      $this.toggleClass("active");
      $body.toggleClass("searchbar-opened");
      // $mainNavCont.find(".navbar-searchbar").slideToggle(300);
      if ($this.hasClass("active")) {
        $("#search-block-form").find(".form-search").focus();
      }
    });

    // Flyout/Dropdown Main-Menu OPEN
    // =============================
    $mainNav = $(".main-nav");
    $mainNav.find(".dropdown-toggle").click(function () {
      $this = $(this);
      $flyoutOpen = $(".navbar-flyout-cont.open");

      $flyout = $this.parent().find(".navbar-flyout-cont");
      $darkOverlay = $("#dark-overlay");

      // MOBILE Dropdown
      if (mobileDevice) {
        if ($this.closest(".dropdown").hasClass("open")) {
          $flyoutOpen.removeClass("open");
        }
        else {
          $flyoutOpen.removeClass("open");
          $flyout.addClass("open");
        }
      }
      // DESKTOP Flyout
      else {
        flyoutOpen = $flyoutOpen.length;
        // First open
        if (!$flyout.hasClass("open") && !($.data($this) == $.data($flyoutOpen))) {
          if (flyoutOpen) {
            // $flyoutOpen.find(".navbar-flyout-inner-cont").css("opacity", 0);
            $flyout.fadeIn();
          }
          else {
            $flyout.slideDown();
            $darkOverlay.fadeIn();
          }
          $flyout.addClass("open");
        }
        // Second click
        else {
          window.location.href = $this.attr("href");
        }
        if (flyoutOpen) {
          setTimeout(function () {
            $flyoutOpen.fadeOut();
          }, 500);
        }
        else {
          $flyoutOpen.slideUp();
        }

        $flyoutOpen.removeClass("open");
      }
    });
    // Flyout Main-Menu CLOSE
    // =============================
    $mainNav.find(".navbar-flyout-close").click(function () {
      $this = $(this);
      $flyout = $this.closest(".navbar-flyout-cont");

      $flyout.slideUp();
      $flyout.removeClass("open");
      $darkOverlay.fadeOut();
    });

    // Click on dark-overlay
    // =============================
    $darkOverlay = $("#dark-overlay");

    $darkOverlay.click(function () {
      $this = $(this);
      $flyoutOpen = $(".navbar-flyout-cont.open");

      $flyoutOpen.slideUp();
      $flyoutOpen.removeClass("open");
      $this.fadeOut();
    });

  });
})(jQuery);
