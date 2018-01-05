(function ($, Drupal) {
    $.fn.gta_datalayer_event = function (data) {
        dataLayer.push(data);
        console.log(dataLayer);
    };

    Drupal.behaviors.google_tag_manager_events = {
        attach: function (context) {
            // If datalayer events aren't available there is no need to continue processing.
            if (!drupalSettings.google_tag_manager || !drupalSettings.google_tag_manager.google_tag_manager_events) {
                return;
            }

            var events = drupalSettings.google_tag_manager.google_tag_manager_events.events;

            if (events.length > 0) {
                drupalSettings.google_tag_manager.google_tag_manager_events.events_set = events;
                drupalSettings.google_tag_manager.google_tag_manager_events.events = [];

                for (var i = 0; i < events.length; i++) {
                    var event = events[i];

                    $(events[i].selector).attr('id', 'event-' + i);
                    $('#event-' + i).click(function (element) {
                        var events = drupalSettings.google_tag_manager.google_tag_manager_events.events_set;
                        for (var i = 0; i < events.length; i++) {
                            if ('event-' + i == element.target.id) {
                                events[i].data = searchCustomJS(events[i].data);
                                dataLayer.push(events[i].data);
                                console.log(dataLayer);
                            }
                        }
                    });
                }
            }

            function searchCustomJS(args) {
                $.each(args, function( index, value ) {
                    if (
                        typeof value['function'] != 'undefined' &&
                        typeof value['selector'] != 'undefined'
                    ) {
                        var command = "var temp = $('" + value['selector'] + "')." + value['function'] + "();";
                        eval(command);
                        args[index] = temp;
                    }else if(typeof value === 'object'){
                        args[index] = searchCustomJS(value);
                    }
                });

                return args;
            }
        }
    }
})
(jQuery, Drupal);
