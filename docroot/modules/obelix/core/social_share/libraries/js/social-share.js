(function ($, Drupal, window) {

// =============================
// Pop-Up
// =============================

  function setPopUp(url, width, height) {
    var winlinks = (screen.width - width) / 2;
    var winoben = (screen.height - height) / 2;
    window.open(url, '', 'left=' + winlinks + ',top=' + winoben + ',width=' + width + ',height=' + height + ',toolbar=0,resizable=0');
  }

// =============================
// Share-Btns
// =============================

// Facebook
  $('.share-btn.fb').click(function () {
    setPopUp('https://www.facebook.com/sharer/sharer.php?u=' + $(this).attr("data-share-url"), 550, 533);
  });

// Twitter
  $(".share-btn.twitter").click(function () {
    setPopUp('https://twitter.com/share?text=' + document.title.replace("|", "-") + ': &url=' + $(this).attr("data-share-url") + '', 550, 260);
  });

// Google Plus
  $(".share-btn.google-plus").click(function () {
    setPopUp('https://plus.google.com/share?url=' + $(this).attr("data-share-url"), 550, 640);
  });

// Pinterest
  $('.share-btn.pinterest').click(function () {
    var utmName = $(this).attr("data-utm-name");
    var url = $(this).attr('href');
    var media = $(this).attr('data-share-img');
    var desc = $(this).attr('data-share-text');
    setPopUp("//www.pinterest.com/pin/create/button/" +
      "?url=" + url +
      "&media=" + media +
      "&description=" + desc, 750, 490);
    return false;
  });
//  Mail
  $('.share-btn.mail').click(function () {
    subject = document.title.replace("|", "-");

    // Opengraph description set
    if (document.querySelector('meta[property="og:description"]') != null) {
      body = document.querySelector('meta[property="og:description"]').getAttribute("content");
    } else if(document.querySelector('meta[name="description"]') != null){ // Opengraph description NOT set
      body = document.querySelector('meta[name="description"]')["content"];
    }
    else{
      body = "";
    }

    body += " "+window.location.href;

    window.location.href = "mailto:?subject=" + subject + "&body=" + body;
  });
//  Print
  $('.share-btn.print').click(function () {
    window.print();
  });
})(jQuery, Drupal, window);

