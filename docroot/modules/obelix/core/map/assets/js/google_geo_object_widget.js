/**
 * Created by eom on 03.07.16.
 */
(function ($, Drupal) {

    var geoJson;
    var polygon_coords = [];
    var marker_coords = [];
    var polygon_object;
    var marker_objects = [];
    var drawingManager;
    var map;

    function init_map() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: -34.397, lng: 150.644},
            zoom: 8,
            scrollwheel: false
        });
    }

    function init_draw() {
        drawingManager = new google.maps.drawing.DrawingManager({
            drawingControl: true,
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_CENTER,
                drawingModes: [
                    google.maps.drawing.OverlayType.MARKER,
                    google.maps.drawing.OverlayType.POLYGON
                ]
            },
            polygonOptions: {
                fillColor: '#ffffff',
                fillOpacity: 0.5,
                strokeWeight: 2,
                strokeColor: '#000000',
                strokeOpacity: 0.5,
                clickable: true,
                draggable: false,
                geodesic: true,
                editable: true,
                zIndex: 1,
                visible: true
            },
            markerOptions: {
                draggable: true,
                zIndex: 1
            }
        });
        drawingManager.setMap(map);

        google.maps.event.addListener(drawingManager, 'polygoncomplete', function (polygon) {

            var paths = polygon_object.getPaths();

            paths.push(polygon.getPath());
            polygon_object.setPaths(paths.getArray());
            polygon.setMap(null);

            var index = paths.getArray().length - 1;
            saveGeoElements(index, 'POLYGON');

            paths = polygon_object.getPaths().getArray();
            google.maps.event.addListener(polygon_object, 'mousedown', function (element) {

                google.maps.event.addListener(paths[index], 'set_at', function (index, obj) {
                    saveGeoElements(element.path, 'POLYGON');
                });
                google.maps.event.addListener(paths[index], 'insert_at', function (index, obj) {
                    saveGeoElements(element.path, 'POLYGON');
                });

            });
        });

        google.maps.event.addListener(drawingManager, 'markercomplete', function (marker) {
            marker_objects.push(marker);
            var index = marker_objects.length - 1;

            marker_objects[index].index = index;
            google.maps.event.addListener(marker_objects[index], 'dragend', function () {
                saveGeoElements(this.index, 'MARKER');
            });
            saveGeoElements(index, 'MARKER');

        });

    }

    function loadGeoElements() {

        polygon_object = new google.maps.Polygon({
            paths: polygon_coords,
            fillColor: '#ffffff',
            fillOpacity: 0.5,
            strokeWeight: 2,
            strokeColor: '#000000',
            strokeOpacity: 0.5,
            clickable: true,
            draggable: false,
            geodesic: true,
            editable: true,
            zIndex: 1,
            visible: true
        });

        if (polygon_coords.length > 0) {


            var paths = polygon_object.getPaths().getArray();
            google.maps.event.addListener(polygon_object, 'mousedown', function (element) {

                for (var i = 0; i < paths.length; i++) {
                    google.maps.event.addListener(paths[i], 'set_at', function (index, obj) {
                        saveGeoElements(element.path, 'POLYGON');
                    });
                    google.maps.event.addListener(paths[i], 'insert_at', function (index, obj) {
                        saveGeoElements(element.path, 'POLYGON');
                    });
                }
            });

        }

        polygon_object.setMap(map);

        if (marker_coords.length > 0) {
            for (var i = 0; i < marker_coords.length; i++) {
                marker_objects.push(new google.maps.Marker({
                    position: marker_coords[i],
                    draggable: true,
                    zIndex: 1
                }));
                marker_objects[i].index = i;
                google.maps.event.addListener(marker_objects[i], 'dragend', function () {
                    saveGeoElements(this.index, 'MARKER');
                });

                marker_objects[i].setMap(map);
            }
        }
    }

    function saveGeoElements(index, type) {
        switch (type) {
            case 'MARKER':
                marker_coords[index] = marker_objects[index].position.toJSON();
                break;
            case 'POLYGON':
                var paths = polygon_object.getPaths().getArray();
                polygon_coords[index] = paths[index].getArray();
                break;
        }
        setGeoJson();
    }

    function setGeoJson() {
        var result = {
            polygons: polygon_coords,
            markers: marker_coords
        };
        $('#geo_json').val(JSON.stringify(result));
    }

    function getGeoJson() {
        if (drupalSettings.map.objects != null) {
            return JSON.parse(drupalSettings.map.objects);
        } else {
            return [];
        }
    }

    function setCoords() {
        if (typeof geoJson.polygons != 'undefined') {
            polygon_coords = geoJson.polygons;
        }
        if (typeof geoJson.markers != 'undefined') {
            marker_coords = geoJson.markers;
        }
    }

    function clearMap() {
        polygon_object.setMap(null);
    }

    Drupal.behaviors.google_geo_object_widget = {
        attach: function (context) {
            geoJson = getGeoJson();
            setCoords();
            init_map();
            init_draw();
            loadGeoElements();
        }
    }

})(jQuery, Drupal);
