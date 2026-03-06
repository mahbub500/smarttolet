(function ($) {
    'use strict';

    $(document).ready(function () {

        // Inject floating map button
        var $mapBtn = $(
            '<button type="button" id="stl-map-location-btn" title="Use Current Location">' +
                '<span class="stl-pulse-ring"></span>' +
                '<span class="stl-pulse-ring stl-pulse-ring--delay"></span>' +
                '<span class="stl-map-btn-icon">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" fill="none">' +
                        '<circle cx="12" cy="12" r="3.5" fill="currentColor"/>' +
                        '<circle cx="12" cy="12" r="7" stroke="currentColor" stroke-width="1.5" fill="none" opacity=".4"/>' +
                        '<line x1="12" y1="2"  x2="12" y2="5"  stroke="currentColor" stroke-width="2" stroke-linecap="round"/>' +
                        '<line x1="12" y1="19" x2="12" y2="22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>' +
                        '<line x1="2"  y1="12" x2="5"  y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>' +
                        '<line x1="19" y1="12" x2="22" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>' +
                    '</svg>' +
                '</span>' +
                '<span class="stl-map-btn-label">My Location</span>' +
                '<span class="stl-map-btn-status"></span>' +
            '</button>'
        );

        // Wait for map container
        var mapCheckInterval = setInterval(function () {

            var $mapContainer = $('#gmap, #osm').first();

            if ($mapContainer.length) {
                $mapContainer.css('position', 'relative').append($mapBtn);
                clearInterval(mapCheckInterval);
            }

        }, 500);


        // Click handler
        $(document).on('click', '#stl-map-location-btn', function () {

            var $btn    = $(this);
            var $status = $btn.find('.stl-map-btn-status');
            var $label  = $btn.find('.stl-map-btn-label');

            // HTTPS check
            if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
                stl_set_status($btn, $status, $label, 'error', '✗ HTTPS required');
                return;
            }

            // Browser support
            if (!navigator.geolocation) {
                stl_set_status($btn, $status, $label, 'error', '✗ Not supported');
                return;
            }

            // Loading state
            $btn.addClass('stl-locating').prop('disabled', true);
            $label.text('Detecting...');
            $status.text('').hide();

            navigator.geolocation.getCurrentPosition(

                // SUCCESS
                function (position) {

                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    var acc = Math.round(position.coords.accuracy);

                    console.log('[STL Location] lat:', lat, '| lng:', lng, '| accuracy:', acc + 'm');

                    // Fill fields
                    $('#manual_lat').val(lat).trigger('input change');
                    $('#manual_lng').val(lng).trigger('input change');

                    // Check manual checkbox
                    if (!$('#manual_coordinate').is(':checked')) {

                        $('#manual_coordinate')
                            .prop('checked', true)
                            .trigger('change click');

                    }

                    // Trigger map update
                    setTimeout(function () {

                        if ($('#generate_admin_map').length) {
                            $('#generate_admin_map').trigger('click');
                        }

                    }, 400);

                    // Success UI
                    stl_set_status($btn, $status, $label, 'success', '✓ Located');

                    setTimeout(function () {

                        $btn.removeClass('stl-located').prop('disabled', false);
                        $label.text('My Location');
                        $status.fadeOut(400);

                    }, 4000);

                },

                // ERROR
                function (error) {

                    var messages = {
                        1: '✗ Permission denied',
                        2: '✗ Position unavailable',
                        3: '✗ Timed out'
                    };

                    var msg = messages[error.code] || '✗ Unknown error';

                    console.warn('[STL Location Error]', error.code, error.message);

                    stl_set_status($btn, $status, $label, 'error', msg);

                    setTimeout(function () {

                        $btn.removeClass('stl-error').prop('disabled', false);
                        $label.text('My Location');
                        $status.fadeOut(400);

                    }, 5000);

                },

                // Options
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );

        });


        // UI helper
        function stl_set_status($btn, $status, $label, type, message) {

            $btn
                .removeClass('stl-locating stl-located stl-error')
                .addClass(type === 'success' ? 'stl-located' : type === 'error' ? 'stl-error' : '')
                .prop('disabled', type !== 'error' && type !== 'success' ? true : false);

            $label.text(type === 'success' ? 'My Location' : message);

            $status
                .text(message)
                .removeClass('stl-s--success stl-s--error')
                .addClass('stl-s--' + type)
                .stop(true)
                .show();
        }

    });

}(jQuery));