(function ($) {
  // Label Animation
  $inputFields = $(".form-minimal-border .form-type-date input");
  $inputFields = $inputFields.add(".form-minimal-border .form-type-datelist input");
  $inputFields = $inputFields.add(".form-minimal-border .form-type-datetime input");
  $inputFields = $inputFields.add(".form-minimal-border .form-type-email input");
  $inputFields = $inputFields.add(".form-minimal-border .form-type-number input");
  $inputFields = $inputFields.add(".form-minimal-border .form-type-password input");
  $inputFields = $inputFields.add(".form-minimal-border .form-type-tel input");
  $inputFields = $inputFields.add(".form-minimal-border .form-type-textfield input");
  $inputFields = $inputFields.add(".form-minimal-border .form-type-url input");
  $inputFields = $inputFields.add(".form-minimal-border .form-type-textarea textarea");

  // Check value on pageload
  function checkInputFieldValue() {
    $inputFields.each(function () {
      $this = $(this);
      $label = $this.closest(".form-group").find("label");

      if ($this.val() != "") {
        $label.addClass("active");
      }
      else {
        $label.removeClass("active");
      }
    });
  }

  checkInputFieldValue();


  // Focus
  $inputFields.focus(function () {
    $this = $(this);

    $this.closest(".form-group").find("label").addClass("active");
    $this.closest(".form-group").find("label").addClass("highlighted");
  });

  // Focus out
  $inputFields.focusout(function () {
    $this = $(this);

    if ($this.val() == "") {
      $this.closest(".form-group").find("label").removeClass("active");
    }
    $this.closest(".form-group").find("label").removeClass("highlighted");
  });


//  Adaptive height on textarea fields
  function h(e) {
    $(e).attr('rows', '1');
    $(e).css({'height': 'auto', 'overflow-y': 'hidden'}).height(e.scrollHeight - 13);
  }

  $('textarea').each(function () {
    h(this);
  }).on('input', function () {
    h(this);
  });


  //  Custom radio buttons
  $radioLabels = $(".form-minimal-border .radio label");

  $radioLabels.click(function (e) {
    $this = $(this);

    $inputField = $("#" + ($this.attr('for')));
    $this.closest(".form-wrapper").find("label.checked").removeClass("checked");

    if ($inputField.prop("checked")) {
      $this.addClass("checked");
    }
  });

  //  Check custom radio states on pageload
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

  $radioBtns = $(".form-minimal-border .radio input");
  checkRadioStatus($radioBtns);


//  Custom checkboxes
  $checkboxLabels = $(".form-minimal-border .checkbox label");

  $checkboxLabels.click(function (e) {
    $this = $(this);

    $inputField = $("#" + ($this.attr('for')));

    if ($inputField.prop("checked")) {
      $this.toggleClass("checked");
    }
  });


// Donation amount (another amount)
// =======

// Input field (another amount)
  $donationAmountInput = $("#edit-field-donation-amount-amount");
  $donationAmountInput.focus(function () {
    $("#edit-field-donation-amount-amountradio-0").prop("checked", true);
    checkRadioStatus($radioBtns);
  });

//  Check checkboxes on change
  $donationCheckboxes = $("#edit-field-donation-amount-amountradio input.form-radio");
  $donationCheckboxes.change(function () {
    $this = $(this);

    if (this.checked && ($this.val() == 0)) {
      $donationAmountInput.focus();
    }
    else {
      $donationAmountInput.val("");
      checkInputFieldValue();
    }
  });

//  PaymentMethod - Bank Radio Button
// =======

  function checkPaymentRadios($el) {
    if ($el.prop("checked") && $el.is("#edit-field-payment-method-paymentmethod-1")) {
      $el.parents(".form-wrapper").find(".creditcard-fields").fadeOut(300);
      $el.parents(".form-wrapper").find(".paypal-fields").fadeOut(300);
      $el.parents(".form-wrapper").find(".bank-fields").delay(350).fadeIn(300);
    }
    else if ($el.prop("checked") && $el.is("#edit-field-payment-method-paymentmethod-2")) {
      $el.parents(".form-wrapper").find(".bank-fields").fadeOut(300);
      $el.parents(".form-wrapper").find(".creditcard-fields").fadeOut(300);
      $el.parents(".form-wrapper").find(".paypal-fields").delay(350).fadeIn(300);

    }
    else if ($el.prop("checked") && $el.is("#edit-field-payment-method-paymentmethod-3")) {
      $el.parents(".form-wrapper").find(".bank-fields").fadeOut(300);
      $el.parents(".form-wrapper").find(".paypal-fields").fadeOut(300);
      $el.parents(".form-wrapper").find(".creditcard-fields").delay(350).fadeIn(300);
    }
  }

  $paymentRadios = $("#edit-field-payment-method-paymentmethod input.form-radio");
  $paymentRadios.on('change', function () {
    $this = $(this);
    checkPaymentRadios($this);
  });

//  Initial
  checkPaymentRadios($("#edit-field-payment-method-paymentmethod-1"));

//  Toggle Ktnr./BLZ
  if($(".action-ktnr-blz").length){
    $actionKtnrBlz = $(".action-ktnr-blz");
    $bankKtnrBlz = $(".bank-ktnr-blz");
    $actionKtnrBlz.click(function () {
      //$(this).toggleClass('hidden');
      $bankKtnrBlz.toggleClass('hidden');
    });
  }

  // Toggle yearly donation description
  $yearlyDonationInfoBtn = $("#yearly-donation-info-btn");

  $yearlyDonationInfoBtn.click(function (e) {
      e.preventDefault();
      $yearlyDonationInfo = $("#yearly-donation-info");

      if ($yearlyDonationInfo.is(":visible")) {
          $yearlyDonationInfo.slideUp().animate({ opacity: 0 }, { queue: false });
      }
      else {
          $yearlyDonationInfo.slideDown().animate({ opacity: 1 }, { queue: false });
      }
  });

})(jQuery);
