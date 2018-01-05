/**
 * Created by eom on 03.07.16.
 */
(function ($, Drupal) {
  var geo_json;
  var map_data;

  function init_map() {

    $.getJSON("/modules/obelix/core/map/assets/js/map_style.json", function (styled_map) {

      // Create a new StyledMapType object, passing it the array of styles,
      // as well as the name to be displayed on the map type control.
      var styledMap = new google.maps.StyledMapType(styled_map, {name: "Styled Map"});

      // Create a map object, and include the MapTypeId to add
      // to the map type control.
      var mapOptions = {
        zoom: parseInt(map_data.zoom),
        center: {lat: parseInt(map_data.lat), lng: parseInt(map_data.lng)},
        disableDefaultUI: true,
        zoomControl: true,
        zoomControlOptions: {
          position: google.maps.ControlPosition.LEFT_TOP
        },
        mapTypeControlOptions: {
          mapTypeIds: ['map_style']
        },
        signedIn: false
      };
      map = new google.maps.Map(document.getElementById('map'), mapOptions);

      //Associate the styled map with the MapTypeId and set it to display.
      map.mapTypes.set('map_style', styledMap);
      map.setMapTypeId('map_style');

      map.data.addGeoJson(geo_json);

      // Add some style.
      map.data.setStyle(function (feature) {
        var fill_opacity = 0.2;

        if (feature.getProperty('highlighted') == "1") {
          fill_opacity = 0.7;
        }
        return ({
          fillColor: '#ff7700',
          fillOpacity: fill_opacity,
          strokeWeight: 2,
          strokeOpacity: 1,
          strokeColor: '#ff7700',
          icon: '/modules/obelix/core/map/assets/img/catastrophe-marker.png'
        });
      });

      // ==============
      // Fullscreen Map
      // ==============

      var drupal_toolbar_offset;
      var navbar_offset;

      if ($('#toolbar-administration').length > 0) {
        drupal_toolbar_offset = 39;
      }
      else {
        drupal_toolbar_offset = 0;
      }

      if ($(window).width() < 768) {
        navbar_offset = 61;
      }
      else {
        navbar_offset = 123;
      }

      var map_height = $(window).height() - drupal_toolbar_offset - navbar_offset;

      $('#map').css('height', map_height);
      $('.country-overlay').css('max-height', map_height);
      $('.mobile-overlay-button').css('top', map_height - 69);


      // =======================
      // Overlay controls
      // =======================

      // Used for when a country is clicked, even for mobile
      function OpenOverlay (cur_country_overlay, mobile_overlay_button) {
        var $body = $('body');
        var $overlay_close = $('.overlay-close');
        var $active_country_mobile = $('.active-country-mobile');

        // If mobile, fade in overlay button and fade out old button if there is one
        if ($(window).width() < 768) {
          if ($active_country_mobile.length === 0) {
            cur_country_overlay.addClass('active-country-mobile');
            mobile_overlay_button.fadeIn();
          }
          else {
            $('.mobile-overlay-button').fadeOut();
            $active_country_mobile.removeClass('active-country-mobile');
            cur_country_overlay.addClass('active-country-mobile');
            mobile_overlay_button.fadeIn();
          }
        }
        // Else, slide overlay left
        else {
          cur_country_overlay.addClass('active-country');

          // Slide the close button out
          $overlay_close.css('display', 'block').animate({right: '320px'}, 350, 'swing');

          // Set body's overflow-x to hidden and back during animation
          $body.css('overflow-x', 'hidden');
          cur_country_overlay.animate({right: '0'}, 350, 'swing', function () {
            $body.css('overflow-x', 'visible');
          });
        }

      }

      function CloseOverlay (active_country, cur_country_overlay) {
        var $body = $('body');
        var $overlay_close = $('.overlay-close');
        var $active_country_mobile = $('.active-country-mobile');

        // If mobile, fade out overlay button
        if ($(window).width() < 768) {
          if ($active_country_mobile.length > 0) {
            $('.mobile-overlay-button').fadeOut();
            $active_country_mobile.removeClass('active-country-mobile');
          }
        }
        // Else, slide overlay right
        else {
          // Slide the close button in
          $overlay_close.animate({right: '0'}, 350, 'swing', function () {
            $overlay_close.css('display', 'none');
          });

          $body.css('overflow-x', 'hidden');
          active_country.animate({
            right: '-320px'
          }, 350, 'swing', function() {
            active_country.removeClass('active-country');

            // if a new country overlay should be opened
            if (cur_country_overlay) {
              OpenOverlay(cur_country_overlay);
            }
            else {
              $body.css('overflow-x', 'visible');
            }

          });
        }
      }

      // =======================
      // Mobile overlay controls
      // =======================

      // Used when user clicks on mobile overlay button
      function MobileOpenOverlay (mobile_overlay_button, country_overlay) {
        var $icon_arrow_down = $('.icon-arrow-down');

        mobile_overlay_button.animate({
          top: '0'
        }, 350, function() {
          mobile_overlay_button.removeClass('overlay-closed');
        });

        country_overlay.addClass('open-country');
        country_overlay.animate({top: '69px'}, 350);

        $icon_arrow_down.removeClass('rotateUp');
        $icon_arrow_down.addClass('rotateDown');
      }

      function MobileCloseOverlay (mobile_overlay_button, country_overlay) {
        var $icon_arrow_down = $('.icon-arrow-down');

        mobile_overlay_button.animate({
          top: map_height - 69
        }, 350, function() {
          mobile_overlay_button.addClass('overlay-closed');
        });

        country_overlay.animate({top: map_height}, 350, function () {
          $('.open-country').removeClass('open-country');
        });

        $icon_arrow_down.removeClass('rotateDown');
        $icon_arrow_down.addClass('rotateUp');
      }

      // ===========================
      // END Mobile overlay controls
      // ===========================

      // Set mouseover event for each feature.
      map.data.addListener('mouseover', function (event) {
        var $hover_infos = $('.hover-infos-' + event.feature.getProperty('country'));

        $hover_infos.stop(true, true).fadeIn();
      });

      // Set mouseout event for each feature.
      map.data.addListener('mouseout', function (event) {
        var $hover_infos = $('.hover-infos-' + event.feature.getProperty('country'));

        $hover_infos.stop(true, true).css('display', 'none');
      });

      // If the empty areas on the map are clicked
      map.addListener('click', function () {
        var $active_country =  $('.active-country');

        CloseOverlay($active_country);
      });

      map.data.addListener('click', function (event) {
        var $active_country =  $('.active-country');
        var $cur_country_overlay =  $('.country-overlay-' + event.feature.getProperty('country'));
        var $mobile_overlay_button =  $('.mobile-overlay-button-' + event.feature.getProperty('country'));

        // If there is currently no country selected
        if ($active_country.length === 0) {
          OpenOverlay($cur_country_overlay, $mobile_overlay_button);
        }
        else {

          // If clicked country is already open
          if (!$cur_country_overlay.hasClass('active-country')) {
            // Close overlay and open a new one
            CloseOverlay ($active_country, $cur_country_overlay);
          }

        }

      });

      $('.mobile-overlay-button').on('click', function () {
        var $mobile_overlay_button = $('.mobile-overlay-button');
        var $active_country =  $('.active-country-mobile');

        //if the overlay button is closed
        if ($mobile_overlay_button.hasClass('overlay-closed')) {
          MobileOpenOverlay($mobile_overlay_button, $active_country);
        }

        else {
          MobileCloseOverlay($mobile_overlay_button, $active_country);
        }
      });

      $('.overlay-close').on('click', function () {
        var $active_country =  $('.active-country');

        CloseOverlay($active_country);
      });
    });
  }

  Drupal.behaviors.google_geo_object_widget = {
    attach: function (context) {
      geo_json = drupalSettings.map.geo_json;
      map_data = drupalSettings.map.map_data;
      console.log(map_data);
      init_map();
    }
  }

})
(jQuery, Drupal);

