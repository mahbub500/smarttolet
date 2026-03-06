/**
 * SmartToLet – Admin JavaScript
 * ==========================================================================
 */
/* global smarttolet_admin, wp */

( function ( $, cfg ) {
    'use strict';

    // ── Gallery media uploader ────────────────────────────────────────────

    var galleryFrame;

    $( '#stl-add-gallery' ).on( 'click', function () {
        if ( galleryFrame ) {
            galleryFrame.open();
            return;
        }

        galleryFrame = wp.media( {
            title    : cfg.i18n.select_image,
            button   : { text: cfg.i18n.use_image },
            library  : { type: 'image' },
            multiple : true,
        } );

        galleryFrame.on( 'select', function () {
            var selection = galleryFrame.state().get( 'selection' );
            var ids       = $( '#stl_gallery_ids' ).val()
                              ? $( '#stl_gallery_ids' ).val().split( ',' )
                              : [];

            selection.each( function ( attachment ) {
                var a = attachment.toJSON();

                if ( ids.indexOf( String( a.id ) ) !== -1 ) return; // skip dupes

                ids.push( a.id );
                var thumb = a.sizes && a.sizes.thumbnail
                    ? a.sizes.thumbnail.url
                    : a.url;

                $( '#stl-gallery-preview' ).append(
                    '<div class="stl-gallery-item" data-id="' + a.id + '">' +
                    '<img src="' + thumb + '" alt="">' +
                    '<button type="button" class="stl-remove-image" title="Remove">✕</button>' +
                    '</div>'
                );
            } );

            $( '#stl_gallery_ids' ).val( ids.join( ',' ) );
        } );

        galleryFrame.open();
    } );

    // Remove image from gallery.
    $( '#stl-gallery-preview' ).on( 'click', '.stl-remove-image', function () {
        var $item = $( this ).closest( '.stl-gallery-item' );
        var id    = $item.data( 'id' );
        var ids   = $( '#stl_gallery_ids' ).val().split( ',' ).filter( function ( v ) {
            return v && String( v ) !== String( id );
        } );

        $( '#stl_gallery_ids' ).val( ids.join( ',' ) );
        $item.remove();
    } );

    // Sortable gallery.
    if ( $( '#stl-gallery-preview' ).length ) {
        $( '#stl-gallery-preview' ).sortable( {
            update: function () {
                var ids = [];
                $( '#stl-gallery-preview .stl-gallery-item' ).each( function () {
                    ids.push( $( this ).data( 'id' ) );
                } );
                $( '#stl_gallery_ids' ).val( ids.join( ',' ) );
            },
        } );
    }

    // ── Geocode button ────────────────────────────────────────────────────

    $( '#stl-geocode' ).on( 'click', function () {
        var address = $( '#stl_address' ).val();
        if ( ! address ) return;

        $( this ).prop( 'disabled', true ).text( 'Finding…' );

        // Requires Google Maps JS to be loaded via API key.
        if ( typeof google === 'undefined' || ! google.maps ) {
            alert( 'Google Maps not loaded. Please save your API key in Settings first.' );
            $( this ).prop( 'disabled', false ).text( 'Find on Map' );
            return;
        }

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode( { address: address }, function ( results, status ) {
            if ( status === 'OK' ) {
                var loc = results[0].geometry.location;
                $( '#stl_latitude' ).val( loc.lat().toFixed( 6 ) );
                $( '#stl_longitude' ).val( loc.lng().toFixed( 6 ) );
            } else {
                alert( 'Geocoding failed: ' + status );
            }
            $( '#stl-geocode' ).prop( 'disabled', false ).text( 'Find on Map' );
        } );
    } );

    // ── Confirm delete ────────────────────────────────────────────────────

    $( document ).on( 'click', '.stl-confirm-delete', function ( e ) {
        if ( ! confirm( cfg.i18n.confirm_delete ) ) {
            e.preventDefault();
        }
    } );

} )( jQuery, smarttolet_admin );
