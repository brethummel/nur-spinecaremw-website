// attach a blur event for all relevant fields in the ISC column
const isc_fields = document.querySelectorAll('.isc-table-storage input, .isc-table-storage select, .isc-table-storage button');

isc_fields.forEach( function( el, i ){
	[ 'click' ].forEach(function(e) {
		el.addEventListener( e, ( event ) => {
			// get image key
			var image_key = el.dataset.imgKey;
			var request = new XMLHttpRequest();

			// show spinner
			const spinner = el.closest( 'td' ).querySelector( '.spinner' );
			spinner.style.visibility = 'visible';
			// remove the button
			el.style.display = 'none';

			request.open('POST', ajaxurl, true);
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
			request.onload = function () {
				if (this.status >= 200 && this.status < 400) {
					// hide spinner
					spinner.style.visibility = 'hidden';
					// print response text, the URL to the media edit page
					if ( request.responseText ) {
						el.insertAdjacentHTML( 'afterend', request.responseText );
					}
				} else {
					// Response error
				}
			};
			request.onerror = function() {
				// Connection error
			};
			request.send( 'action=isc-update-storage-image&nonce=' + isc.ajaxNonce + '&image_key=' + image_key + '&field=' + el.name );
		});
	});
});
