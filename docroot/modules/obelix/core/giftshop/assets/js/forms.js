(function ($, Drupal, window) {
  Drupal.behaviors.giftshop = {
    attach: function (context, settings) {
      $("#edit-send-type-email, #edit-send-type-post").on('change', function () {

        $this = $(this);
        if ($this.prop("checked") && $this.is("#edit-send-type-email")) {
          $(".hl-send-email").removeClass('hidden');
        }
        else {
          $(".hl-send-email").addClass('hidden');
        }
      });

      // @TODO Quickfix: remove ajax submit on button
      $("#giftshop-gift-select-form").find("#edit-submit").unbind();
    }
  }
})(jQuery, Drupal, window);
