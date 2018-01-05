(function ($, Drupal, window) {
  Drupal.behaviors.custom_kipa_form = {
    attach: function (context, settings) {

      function checkRadioStatus(radioBtns) {
        radioBtns.each(function () {
          $this = $(this);

          if ($this.prop("checked")) {
            $this.closest("label").addClass("checked");
          }
          else {
            $this.closest("label").removeClass("checked");
          }
        });
      }

      // Focus yearly-amount "yes" on focus input-field
      $radioBtns = $(".form-minimal-border .radio input");

      if($("#edit-field-yearly-donation-month13").length){

        $("#edit-field-yearly-donation-month13").focusin(function () {
          $("#edit-field-yearly-donation-month13-check-1").prop("checked", true);
          checkRadioStatus($radioBtns);
        });
      }
    }
  }
})(jQuery, Drupal, window);
