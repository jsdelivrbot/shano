(function ($, Drupal, window) {
  Drupal.behaviors.editorial_hero_slider = {
    attach: function (context, settings) {
      $editorialHeroSlider = $(".editorial-hero-slider", context);

      $editorialHeroSlider.once('slick-slider-initialized').each(function () {
          $this = $(this);

          $('.inner-content', this).slick(
            settings['editorial']['hero_slider'][$(this).parent().attr("id")]
          );
          // Preview video embedded
          if($this.find('.editorial-hero-slider-preview-video').length){
            $this.find('.inner-content').slick('slickPause');
          }
        }
      );

      $(window).resize(function () {
        // SetHeight for slider on resize
        if ($editorialHeroSlider.length) {
          $editorialHeroSlider.find(".inner-content").slick('setPosition');
        }
      });
    }
  }
})(jQuery, Drupal, window);
