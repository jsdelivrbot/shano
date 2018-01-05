/**
 * Created by eom on 03.07.16.
 */
(function ($, Drupal) {
  var geo_json;
  var countries;
  var map_data;

  function init_map() {

    $.getJSON("/modules/obelix/core/map/assets/js/map_style.json", function (styled_map) {

      // Create a new StyledMapType object, passing it the array of styles,
      // as well as the name to be displayed on the map type control.
      var styledMap = new google.maps.StyledMapType(styled_map, {name: "Styled Map"});

      // Create a map object, and include the MapTypeId to add
      // to the map type control.
      var mapOptions = {
        scrollwheel: false,
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

      // Set click event
      map.data.unbind( "click" );

      // Set click event
      map.data.addListener('click', function (event) {
        window.location = "/informieren/wo-wir-arbeiten";
      });

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
    });
  }

  Drupal.behaviors.google_geo_object_widget = {
    attach: function (context) {
      geo_json = drupalSettings.map.geo_json;
      countries = drupalSettings.map.countries;
      map_data = drupalSettings.map.map_data;
      //console.log(geo_json);
      init_map();
    }
  }

})
(jQuery, Drupal);

