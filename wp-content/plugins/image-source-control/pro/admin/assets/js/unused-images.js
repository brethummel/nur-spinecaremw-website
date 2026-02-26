const isc_deep_check_buttons = document.querySelectorAll( '#isc-table-unused-images-page table button' );

isc_deep_check_buttons.forEach( function( el, i ){
	[ 'click' ].forEach( function(e) {
		el.addEventListener( e, ( event ) => {
			// get the image key
			var image_id        = el.dataset.imageId;
			var request= new XMLHttpRequest();

			// show the spinner
			const spinner            = el.closest( 'tr' ).querySelector( '.spinner' );
			spinner.style.visibility = 'visible';
			spinner.style.display = 'block';
			// remove the button
			el.style.display = 'none';
			el.closest('tr').querySelector('.isc-table-unused-images-deep-check-result').style.visibility = 'hidden';

			request.open( 'POST', ajaxurl, true );
			request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );
			request.onload  = function () {
				if ( this.status >= 200 && this.status < 400 ) {
					// hide the spinner
					spinner.style.visibility = 'hidden';
					// remove the last check date, if exists
					let last_check = el.closest( 'tr' ).querySelector( '.isc-table-unused-images-last-check' );
					if ( last_check ) {
						last_check.innerHTML = '&nbsp;';
					}
					let last_check_result = el.closest( 'tr' ).querySelector( '.isc-table-unused-images-deep-check-result' );
					last_check_result.style.visibility = 'visible';
					if ( request.responseText ) {
						last_check_result.innerHTML = request.responseText;
					}
				}
			};
			request.onerror = function() {
				if ( request.responseText ) {
					el.closest('tr').querySelector('.isc-table-unused-images-deep-check-result').innerHTML = request.responseText;
				}
			};
			request.send( 'action=isc-unused-images-deep-check&nonce=' + isc.ajaxNonce + '&image_id=' + image_id );
		} );
	} );
} );

/**
 * Handle the form submission for the unused images page.
 * Run a database check for all selected images.
 */
document.getElementById( 'unused-images-form' ).addEventListener( 'submit', function( event ) {
	// Get the selected action
	const action = document.querySelector( 'select[name="action"]' ).value;

	// If the selected action is 'delete', show a confirmation message and only proceed if the user confirms it. The notice comes from WordPress core.
	if ( action === 'delete' ) {
		if ( ! showNotice.warn() ) {
			event.preventDefault();
		}
	} else if ( action === 'deep_check' ) {
		event.preventDefault();

		// Get all the selected rows
		const selectedRows = document.querySelectorAll( 'input[name="bulk_edit[]"]:checked' );

		// click the deep-check-buttons incrementally
		function isc_unused_images_click_deep_check_buttons(index) {
			if (index >= selectedRows.length) {
				return;
			}

			// Get the deep-check-button for the current row
			const button = selectedRows[index].closest( 'tr' ).querySelector( '.isc-button-deep-check' );

			// Click the button and wait for the AJAX request to complete
			button.click();
			const spinner  = button.closest( 'tr' ).querySelector( '.spinner' );
			const observer = new MutationObserver(
				function () {
					if ( spinner.style.visibility === 'hidden' ) {
						// When the AJAX request is complete, click the next button
						observer.disconnect();
						// Uncheck the checkbox
						selectedRows[index].checked = false;
						isc_unused_images_click_deep_check_buttons( index + 1 );
					}
				}
			);
			observer.observe( spinner, { attributes: true } );
		}

		// Start clicking the buttons
		isc_unused_images_click_deep_check_buttons( 0 );
	}
});

/**
 * When clicking on the database search icon in the check details of the Appearance list
 * trigger a click on the database search button in the same row.
 */
document.querySelectorAll('.isc-check-indicator.isc-check-database').forEach(function (indicator) {
	indicator.addEventListener('click', function () {
		const button = this.closest('tr').querySelector('button.isc-button-deep-check');
		if (button) {
			button.click();
		}
	});
});