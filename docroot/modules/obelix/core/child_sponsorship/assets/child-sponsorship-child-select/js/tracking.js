(function ($) {
  $.fn.child_finder_success = function () {
    try {
      ga('send', 'event', 'child_finder', 'success', '');
    } catch (err) {
      // Do nothing.
    }

    try {
      dataLayer.push({
        event: 'child_finder',
        'success': 'true'
      });
    } catch (err) {
      // Do nothing.
    }
  };
})(jQuery);
