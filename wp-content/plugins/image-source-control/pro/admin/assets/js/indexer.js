/**
 * Index Bot - Frontend JavaScript
 *
 * This script handles the AJAX requests for the ISC Index Bot feature.
 * It controls the indexing process, showing progress updates, and displays results.
 */

document.addEventListener( 'DOMContentLoaded', function () {
	// DOM Elements
	const indexerButtons    = document.querySelectorAll( '.isc-indexer-btn' );
	const progressBar       = document.getElementById( 'isc-indexer-progress-bar' );
	const statusText        = document.getElementById( 'isc-indexer-status' );
	const progressDiv       = document.getElementById( 'isc-indexer-progress' );
	const progressStats     = document.querySelector( '.isc-indexer-progress-stats' );
	const resultsDiv        = document.getElementById( 'isc-indexer-results' );
	const resultsTbody      = document.getElementById( 'isc-indexer-results-tbody' );
	const summaryDiv        = document.getElementById( 'isc-indexer-summary' );
	const summaryText       = document.getElementById( 'isc-indexer-summary-text' );

	// Variables
	let isIndexing               = false;
	let processedItems           = []; // Store details of processed items
	let totalItems               = 0;
	let urlBatchSize             = 100; // Number of URLs to fetch per batch
	let currentGlobalOffset      = 0; // Overall progress counter
	let currentUrlBatch          = []; // Current batch of URLs being processed
	let currentUrlBatchIndex     = 0; // Index within the current batch
	let startTimestamp           = 0; // Timestamp when indexing started
	let currentIndexingMode      = 'all'; // Current indexing mode

	/**
	 * Initialize event listeners
	 */
	function initEventListeners() {
		// Add click listeners to all indexer buttons
		indexerButtons.forEach( function( button ) {
			button.addEventListener( 'click', function () {
				if ( ! isIndexing ) {
					// Get indexing mode from button's data attribute, default to 'all'
					currentIndexingMode = button.getAttribute( 'data-isc-indexer-mode' ) || 'all';
					startIndexing();
				}
			} );
		} );
	}

	/**
	 * Start the indexing process
	 */
	function startIndexing() {
		// Clear any previous results
		processedItems         = [];
		resultsTbody.innerHTML = '';
		resultsDiv.classList.add( 'hidden' );
		summaryDiv.classList.add( 'hidden' );
		statusText.textContent = ''; // Clear previous status messages

		startTimestamp       = Math.floor( Date.now() / 1000 );
		isIndexing           = true;
		// Disable all indexer buttons
		indexerButtons.forEach( function( button ) {
			button.disabled = true;
		} );
		progressDiv.classList.remove( 'hidden' );

		// First, get the total number of items
		getTotalCount();
	}

	/**
	 * Fetch the total count of public content from the backend
	 */
	function getTotalCount() {
		const formData = new FormData();
		formData.append( 'action', 'isc_get_indexer_total' );
		formData.append( 'nonce', isc.ajaxNonce );
		formData.append( 'indexing_mode', currentIndexingMode );

		fetch( ajaxurl, {
			method:      'POST',
			credentials: 'same-origin',
			body:        formData
		} )
			.then( response => {
				if ( ! response.ok ) {
					throw new Error( 'Network response was not ok' );
				}
				return response.json();
			} )
			.then( data => {
				if ( data.success ) {
					totalItems = data.data.total;
					currentGlobalOffset = 0; // Reset global offset
					currentUrlBatch = []; // Clear batch
					currentUrlBatchIndex = 0; // Reset batch index

					if ( totalItems === 0 ) {
						statusText.textContent = 'No content to index.';
						completeIndexing();
						return;
					}

					// Start fetching the first batch of URLs
					fetchNextBatch( 0 );

				} else {
					handleError( data.data );
				}
			} )
			.catch( error => {
				handleError( 'Failed to get total count: ' + error.message );
			} );
	}

	/**
	 * Fetch the next batch of URLs from the backend
	 *
	 * @param {number} offset - The global offset for the start of the batch
	 */
	function fetchNextBatch( offset ) {
		const formData = new FormData();
		formData.append( 'action', 'isc_get_indexer_batch' );
		formData.append( 'nonce', isc.ajaxNonce );
		formData.append( 'offset', offset );
		formData.append( 'batch_size', urlBatchSize );
		formData.append( 'indexing_mode', currentIndexingMode );

		fetch( ajaxurl, {
			method:      'POST',
			credentials: 'same-origin',
			body:        formData
		} )
			.then( response => {
				if ( ! response.ok ) {
					throw new Error( 'Network response was not ok' );
				}
				return response.json();
			} )
			.then( data => {
				if ( data.success ) {
					currentUrlBatch = data.data.urls;
					currentUrlBatchIndex = 0; // Start processing from the beginning of this batch

					if ( currentUrlBatch.length === 0 ) {
						// Should not happen if totalItems was correct, but handle defensively
						completeIndexing();
						return;
					}

					// Start processing the first item in the fetched batch
					processNextItemInBatch();

				} else {
					handleError( data.data );
				}
			} )
			.catch( error => {
				handleError( 'Failed to fetch batch: ' + error.message );
				completeIndexing();
			} );
	}

	/**
	 * Process the next item in the current batch
	 */
	function processNextItemInBatch() {
		// Check if we have processed all items globally
		if ( currentGlobalOffset >= totalItems ) {
			completeIndexing();
			return;
		}

		// Check if we have processed all items in the current batch
		if ( currentUrlBatchIndex >= currentUrlBatch.length ) {
			// Fetch the next batch
			fetchNextBatch( currentGlobalOffset );
			return;
		}

		// Get the URL data for the current item in the batch
		const urlData = currentUrlBatch[currentUrlBatchIndex];

		// Run the indexer for this single item
		runIndexer( urlData, currentGlobalOffset );
	}


	/**
	 * Run the indexer for a single item
	 *
	 * @param {Object} urlData - The URL data for the item (id, url)
	 * @param {number} globalOffset - The current global offset (0 to total-1)
	 */
	function runIndexer( urlData, globalOffset ) {
		// Update the progress stats above the progress bar - This shows overall progress
		progressStats.textContent = iscIndexerL10n.processingPages
												  .replace('%1$s', globalOffset + 1)
												  .replace('%2$s', totalItems);

		// Create FormData for the request
		const formData = new FormData();
		formData.append( 'action', 'isc_run_indexer' );
		formData.append( 'nonce', isc.ajaxNonce );
		formData.append( 'url_data', JSON.stringify( urlData ) ); // Send URL data for the single item
		formData.append( 'global_offset', globalOffset ); // Send global offset for progress tracking

		// Make the AJAX request
		fetch( ajaxurl, {
			method:      'POST',
			credentials: 'same-origin',
			body:        formData
		} )
			.then( response => {
				if ( ! response.ok ) {
					throw new Error( 'Network response was not ok' );
				}
				return response.json();
			} )
			.then( data => {
				if ( data.success ) {
					// Store the processed item details
					processedItems.push( data.data );

					// Update the results list with the newly processed item
					updateResultsList( [data.data] );

					// Update the progress bar
					progressBar.value = Math.min( 100, Math.round( ( (globalOffset + 1) / totalItems ) * 100 ) );

					// Move to the next item
					currentGlobalOffset++;
					currentUrlBatchIndex++;

					// Process the next item (either in the current batch or fetch the next batch)
					processNextItemInBatch();

				} else {
					handleError( data.data );
				}
			} )
			.catch( error => {
				handleError( 'Processing item failed: ' + error.message );
				// Decide how to handle errors: stop, skip, retry? For now, stop.
				completeIndexing();
			} );
	}

	/**
	 * Update the results list with newly processed items
	 *
	 * @param {Array} items - Array of processed items with their details (should be just one item here)
	 */
	function updateResultsList(items) {
		if (resultsDiv.classList.contains('hidden')) {
			resultsDiv.classList.remove('hidden');
		}

		items.forEach(item => {
			const row = document.createElement('tr');

			// Create the Title cell with a safe link element.
			const titleCell = document.createElement('td');
			const link = document.createElement('a');
			// These URLs are WordPress post permalinks generated on the server side and are considered safe
			link.href = item.url;
			link.target = '_blank';
			link.textContent = item.title;
			titleCell.appendChild(link);
			row.appendChild(titleCell);

			// Create the Post Type cell.
			const postTypeCell = document.createElement('td');
			postTypeCell.textContent = getPostTypeName( item.post_type );
			row.appendChild(postTypeCell);

			// Create the Images Count cell.
			const imagesCountCell = document.createElement('td');
			imagesCountCell.textContent = item.images_count;
			row.appendChild(imagesCountCell);

			resultsTbody.appendChild(row);
		});
	}


	/**
	 * Get readable name for post type
	 *
	 * @param {string} postType - The post type slug
	 * @return {string} - The readable post type name
	 */
	function getPostTypeName( postType ) {
		// Use localized post type names from WordPress core
		if ( iscIndexerL10n.postTypes[postType] ) {
			return iscIndexerL10n.postTypes[postType];
		}

		// Capitalize the first letter and replace hyphens with spaces
		return postType
			.replace( /-/g, ' ' )
			.replace( /\b\w/g, c => c.toUpperCase() );
	}

	/**
	 * Enable indexer buttons and reset indexing state
	 */
	function enableIndexerButtons() {
		isIndexing = false;
		// Re-enable all indexer buttons
		indexerButtons.forEach( function( button ) {
			button.disabled = false;
		} );
	}

	/**
	 * Complete the indexing process
	 */
	function completeIndexing() {
		enableIndexerButtons();
		statusText.textContent = iscIndexerL10n.indexingComplete;

		// Calculate the total images found
		const totalImages = processedItems.reduce( ( total, item ) => total + item.images_count, 0 );

		// Update and show the summary
		summaryText.textContent = iscIndexerL10n.processedSummary
												.replace('%1$s', processedItems.length)
												.replace('%2$s', totalImages);

		summaryDiv.classList.remove( 'hidden' );

		// Move summary to the top of the results div if results are shown
	   if (!resultsDiv.classList.contains('hidden')) {
			   resultsDiv.insertBefore( summaryDiv, resultsDiv.firstChild );
	   }

	   // Only run cleanup when indexing mode is "all"
	   if ( currentIndexingMode === 'all' ) {
		   cleanupIndex();
	   }

	   // Update the status table after indexing is complete
	   updateStatusTable();
   }

   /**
	* Request cleanup of outdated index entries.
	*/
	function cleanupIndex() {
	   const formData = new FormData();
	   formData.append( 'action', 'isc_cleanup_indexer' );
	   formData.append( 'nonce', isc.ajaxNonce );
	   formData.append( 'start_time', startTimestamp );
	   formData.append( 'indexing_mode', currentIndexingMode );

	   fetch( ajaxurl, {
			   method:      'POST',
			   credentials: 'same-origin',
			   body:        formData
	   } )
			   .then( response => response.json() )
			   .then( data => {
					   if ( ! data.success ) {
							   console.error( 'Cleanup failed', data.data );
					   }
			   } )
			   .catch( error => {
					   console.error( 'Cleanup error', error );
		  	 } );
   }

	/**
	 * Update the indexer status table via AJAX
	 */
	function updateStatusTable() {
		const formData = new FormData();
		formData.append( 'action', 'isc_get_indexer_status' );
		formData.append( 'nonce', isc.ajaxNonce );

		fetch( ajaxurl, {
			method:      'POST',
			credentials: 'same-origin',
			body:        formData
		} )
			.then( response => {
				if ( ! response.ok ) {
					throw new Error( 'Network response was not ok' );
				}
				return response.json();
			} )
			.then( data => {
				if ( data.success ) {
					// Replace the entire tbody content with the rendered template
					const statusTableBody = document.querySelector( '.isc-indexer-status-table tbody' );
					if ( statusTableBody ) {
						statusTableBody.innerHTML = data.data.tbody_html;
						
						// Re-initialize event listeners for any new "Index Missing" buttons
						const newIndexerButtons = statusTableBody.querySelectorAll( '.isc-indexer-btn' );
						newIndexerButtons.forEach( function( button ) {
							button.addEventListener( 'click', function () {
								if ( ! isIndexing ) {
									currentIndexingMode = button.getAttribute( 'data-isc-indexer-mode' ) || 'all';
									startIndexing();
								}
							} );
						} );
					}
				} else {
					console.error( 'Failed to update status table:', data.data );
				}
			} )
			.catch( error => {
				console.error( 'Error updating status table:', error );
			} );
	}

	/**
	 * Handle errors during the indexing process
	 *
	 * @param {string} message - The error message to display
	 */
	function handleError( message ) {
		statusText.textContent = iscIndexerL10n.error + ': ' + message; // Keep error message
		enableIndexerButtons();
	}

	// Initialize the events
	initEventListeners();
} );