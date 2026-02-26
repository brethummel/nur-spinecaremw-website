// attach a blur event for all relevant fields in the ISC column
const isc_fields = document.querySelectorAll('.column-isc_fields input, .column-isc_fields select');

isc_fields.forEach( function( el, i ){
	// ISC reacts on blur and change but for that we need to check if the form was already sent, so it isn’t triggered multiple times
	var isc_pro_columns_form_sent = false;
	[ 'blur', 'change' ].forEach(function(e) {
		el.addEventListener( e, ( event ) => {
			// check if a request was already sent
			if( isc_pro_columns_form_sent ) {
				return;
			}
			isc_pro_columns_form_sent = true;

			// get attachment ID
			var att_id = el.dataset.attId;
			// get value: input fields return the text value, check boxes, if option is enabled
			if( 'checkbox' === el.getAttribute( 'type' ) ) {
				val = el.checked;
			} else {
				val = el.value;
			}

			var request = new XMLHttpRequest();

			// show spinner
			const spinner = el.closest( '.column-isc_fields' ).querySelector( '.spinner' );
			spinner.style.visibility = 'visible';

			request.open('POST', ajaxurl, true);
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
			request.onload = function () {
				if (this.status >= 200 && this.status < 400) {
					// hide spinner
					spinner.style.visibility = 'hidden';
					isc_pro_columns_form_sent = false;
					// update the Image Source Preview column
					const previewColumn = el.closest('tr').querySelector('.column-isc_preview');
					if (previewColumn && this.responseText) {
						previewColumn.innerHTML = this.responseText;
					}
				} else {
					// Response error
					isc_pro_columns_form_sent = false;
				}
				if ( this.responseText ) {
					console.log( this.responseText );
				}
			};
			request.onerror = function() {
				console.log( 'Connection error' );
			};
			request.send( 'action=isc-update-attachment&nonce=' + isc.ajaxNonce + '&att_id=' + att_id + '&field=' + el.name + '&value=' + encodeURIComponent(val) );
		});
	});
});

// attach a blur event for all relevant fields in the ISC column
document.querySelectorAll('.column-isc_fields input[name="isc-standard"]').forEach( function( checkbox, i ){
	checkbox.addEventListener('change', function() {
		// get the data-att-id attribute to associate the checkbox with the input
		var attId = checkbox.getAttribute( 'data-att-id' );

		// find the next input field for this attachment
		var input = document.querySelector( 'input[name="isc-source"][data-att-id="' + attId + '"]' );

		if ( input ) {
			if ( checkbox.checked ) {
				// checkbox is checked, set the placeholder from data-isc-standard-text
				input.placeholder = input.getAttribute('data-isc-standard-text');
			} else if ( ! checkbox.getAttribute( 'data-isc-use-standard-by-default' ) ) {
				// checkbox is not checked and global standard is not default, clear the placeholder
				input.placeholder = '';
			}
		}
	});
});

/**
 * Add a click event to the column icon to open the screen options
 */
document.addEventListener('DOMContentLoaded', function() {
	const icons = document.querySelectorAll('.isc-admin-list-view-column-hide');
	if (icons.length) {
		icons.forEach(function(icon) {
			icon.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				const screenOptionsButton = document.getElementById('show-settings-link');
				if (screenOptionsButton) {
					screenOptionsButton.click();
				}
			});
		});
	}
});