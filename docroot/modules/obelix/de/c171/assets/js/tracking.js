(function ($, dataLayer, Drupal, window) {
  Drupal.behaviors.c171_tracking = {
    attach: function (context, settings) {
      var self = this;

      $('#editorial-hero-slider-8717', context).once('tracking').each(function () {
        var _ = $('.slick-slider').slick('getSlick');
        _.$slides.find('a').click(function () {
          self.trackLink(this);
        });
        _.$prevArrow.click(function () {
          self.trackSlide(_.$slides[_.currentSlide]);
        });
        _.$nextArrow.click(function () {
          self.trackSlide(_.$slides[_.currentSlide]);
        });
        _.$slider.on('swipe', function () {
          self.trackSlide(_.$slides[_.currentSlide]);
        });
        _.$dots.find('li').click(function () {
          self.trackSlide(_.$slides[_.currentSlide]);
        });
      });
    },
    trackLink: function (link) {
      var action = link.innerText;

      var label = $(link)
        .parents('.slider-item')
        .find('img')
        .attr('alt');

      dataLayer.push({
        'event': 'analytics-event',
        'analytics-category': 'Carousel',
        'analytics-action': action,
        'analytics-label': label
      });
    },
    trackSlide: function (slide) {
      var label = $(slide).find('picture img').attr('alt');

      dataLayer.push({
        'event': 'analytics-event',
        'analytics-category': 'Carousel',
        'analytics-action': 'SlideChanged',
        'analytics-label': label
      });
    }
  }
})(jQuery, dataLayer, Drupal, window);
