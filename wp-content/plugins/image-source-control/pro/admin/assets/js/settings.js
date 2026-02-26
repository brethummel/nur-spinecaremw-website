jQuery( document ).ready( function ( $ ) {
	// activate licenses
	$( '#license-activate' ).on( 'click', function () {

		var button = $( this )
		isc_disable_license_buttons( true )

		var query = {
			action:     'isc-activate-license',
			license:    $( this ).parents( 'td' ).find( '#license-key' ).val(),
			security:   $( '#isc-licenses-ajax-referrer' ).val()
		}

		// show loader
		$( '<span class="spinner"></span>' ).insertAfter( button ).show();

		// send and close message
		$.post( ajaxurl, query, function ( r ) {
			// remove spinner
			$( 'span.spinner' ).remove();
			var parent = button.parents( 'td' )

			if ( r === '1' ) {
				parent.find( '#license-activate-error' ).hide();
				parent.find( '#license-deactivate' ).show();
				button.fadeOut();
				parent.find( 'input' ).prop( 'readonly', 'readonly' )
			} else {
				parent.find( '#license-activate-error' ).show().html( r )
			}
			isc_disable_license_buttons( false )
		} )
	} )

	// deactivate licenses
	$( '#license-deactivate' ).on( 'click', function () {

		var button = $( this )
		isc_disable_license_buttons( true )

		var query = {
			action:     'isc-deactivate-license',
			security:   $( '#isc-licenses-ajax-referrer' ).val()
		}

		// show loader
		$( '<span class="spinner"></span>' ).insertAfter( button ).show();

		// send and close message
		$.post( ajaxurl, query, function ( r ) {
			// remove spinner
			$( 'span.spinner' ).remove()

			if ( r === '1' ) {
				button.siblings( '#license-activate-error' ).hide()
				button.siblings( '#license-activate' ).show()
				button.siblings( 'input' ).prop( 'readonly', false )
				button.fadeOut()
			} else {
				button.siblings( '#license-activate-error' ).show().html( r )
			}
			isc_disable_license_buttons( false )
		} )
	} );

	function isc_disable_license_buttons ( disable = true ) {
		var buttons = $( 'button#license-activate, button#license-deactivate' ) // all activation buttons
		// disable all buttons to prevent issues when users try to enable multiple licenses at the same time
		if ( disable ) {
			buttons.attr( 'disabled', 'disabled' )
		} else {
			buttons.removeAttr( 'disabled' )
		}
	}
} );
