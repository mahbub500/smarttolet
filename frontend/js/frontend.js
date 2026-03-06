/**
 * SmartToLet – Frontend JavaScript
 * ==========================================================================
 */
/* global smarttolet, jQuery */

( function ( $, cfg ) {
    'use strict';

    // ── AJAX property search ──────────────────────────────────────────────

    var $searchForm    = $( '#stl-search-form' );
    var $searchResults = $( '#stl-search-results' );
    var searchTimer;

    $searchForm.on( 'submit', function ( e ) {
        e.preventDefault();
        runSearch();
    } );

    // Live search on select change.
    $searchForm.on( 'change', 'select', function () {
        clearTimeout( searchTimer );
        searchTimer = setTimeout( runSearch, 400 );
    } );

    // Debounced keyword input.
    $searchForm.on( 'input', '#stl-keyword', function () {
        clearTimeout( searchTimer );
        searchTimer = setTimeout( runSearch, 600 );
    } );

    function runSearch() {
        var data = $searchForm.serializeArray().reduce( function ( acc, item ) {
            acc[ item.name ] = item.value;
            return acc;
        }, {} );

        data.action = 'stl_search';
        data.nonce  = cfg.nonce;

        $searchResults.html( '<div class="stl-spinner" aria-label="' + cfg.i18n.loading + '"></div>' );

        $.post( cfg.ajax_url, data, function ( res ) {
            if ( res.success ) {
                if ( res.data.html ) {
                    $searchResults.html( '<div class="stl-listings__grid">' + res.data.html + '</div>' );
                } else {
                    $searchResults.html( '<p class="stl-listings__empty">' + cfg.i18n.error + '</p>' );
                }
            } else {
                $searchResults.html( '<p class="stl-listings__empty">' + cfg.i18n.error + '</p>' );
            }
        } ).fail( function () {
            $searchResults.html( '<p class="stl-listings__empty">' + cfg.i18n.error + '</p>' );
        } );
    }

    // ── Enquiry form ──────────────────────────────────────────────────────

    $( document ).on( 'click', '#stl-enquiry-submit', function () {
        var $form = $( '#stl-enquiry-form' );
        var $msg  = $( '#stl-enquiry-msg' );
        var $btn  = $( this );

        var name    = $form.find( '#stl-name' ).val().trim();
        var email   = $form.find( '#stl-email' ).val().trim();
        var phone   = $form.find( '#stl-phone' ).val().trim();
        var message = $form.find( '#stl-message' ).val().trim();
        var pid     = $form.data( 'property' );

        if ( ! name || ! email ) {
            showMsg( $msg, 'error', 'Please fill in all required fields.' );
            return;
        }

        $btn.attr( 'aria-busy', 'true' ).text( cfg.i18n.loading );
        $msg.hide().removeClass( 'stl--success stl--error' );

        $.post( cfg.ajax_url, {
            action      : 'stl_enquiry',
            nonce       : cfg.nonce,
            property_id : pid,
            name        : name,
            email       : email,
            phone       : phone,
            message     : message,
        }, function ( res ) {
            if ( res.success ) {
                showMsg( $msg, 'success', res.data.message || cfg.i18n.enquiry_sent );
                $form.find( 'input:not([type=hidden]), textarea' ).val( '' );
            } else {
                showMsg( $msg, 'error', res.data.message || cfg.i18n.error );
            }
        } ).fail( function () {
            showMsg( $msg, 'error', cfg.i18n.error );
        } ).always( function () {
            $btn.attr( 'aria-busy', 'false' ).text( 'Send Enquiry' );
        } );
    } );

    function showMsg( $el, type, text ) {
        $el.text( text )
           .removeClass( 'stl--success stl--error' )
           .addClass( 'stl--' + type )
           .show();
    }

    // ── Favourite toggle ──────────────────────────────────────────────────

    $( document ).on( 'click', '.stl-favourite', function () {
        var $btn = $( this );
        var pid  = $btn.data( 'id' );

        $.post( cfg.ajax_url, {
            action      : 'stl_toggle_favourite',
            nonce       : cfg.nonce,
            property_id : pid,
        }, function ( res ) {
            if ( res.success ) {
                $btn.toggleClass( 'is-saved', res.data.favourited );
                $btn.text( res.data.favourited ? '♥' : '♡' );
            }
        } );
    } );

    // ── Gallery lightbox (simple swap) ────────────────────────────────────

    $( document ).on( 'click', '.stl-gallery__thumb-img', function () {
        var $thumb = $( this );
        var src    = $thumb.attr( 'src' );

        // Swap main image src to higher-res version (data-full if available).
        var full = $thumb.data( 'full' ) || src;

        var $main = $( '.stl-gallery__main-img' );
        $main.attr( 'src', full );

        $( '.stl-gallery__thumb-img' ).removeClass( 'is-active' );
        $thumb.addClass( 'is-active' );
    } );

} )( jQuery, smarttolet );
