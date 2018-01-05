(function ($, Drupal, window) {
  Drupal.behaviors.branding_campaign = {
    attach: function (context, settings) {
      $(window).load(function () {
        $editorialHeroSlider = $("#editorial-hero-slider-5941").find(".slick-slider");

        // Deactivate autoplay on second hero-slider on branding-campaign page
        $editorialHeroSlider.slick('slickPause');
      });
    }
  }
})(jQuery, Drupal, window);
