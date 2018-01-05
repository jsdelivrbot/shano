(function ($, Drupal, window) {
  Drupal.behaviors.branding_campaign = {
    attach: function (context, settings) {
      $(window).load(function () {
        $editorialHeroSlider = $("#editorial-hero-slider-7404").find(".slick-slider");

        // Deactivate autoplay on second hero-slider on my-child-billy campaign page
        $editorialHeroSlider.slick('slickPause');
      });
    }
  }
})(jQuery, Drupal, window);
