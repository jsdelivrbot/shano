(function ($, Drupal, window) {
  Drupal.behaviors.editorial_slideshow = {
    attach: function (context, settings) {
      $('.editorial-slideshow', context).once('slick-slider-initialized').each(function () {
        $('.inner-content', this).on('init reInit afterChange', function (event, slick, currentSlide, nextSlide) {
          var i = (currentSlide ? currentSlide : 0) + 1;
          $(this).find('.slick-pagination').text(i + ' / ' + slick.slideCount);
        }).slick(settings['editorial']['slideshow'][this.id]);
      });
    }
  }
})(jQuery, Drupal, window);
