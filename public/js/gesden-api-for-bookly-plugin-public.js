(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	jQuery(function($) {
		// Initialize the datepicker
		$("#gesden-calendar").datepicker({
			onSelect: function(dateText) {
				// Show loading message while the AJAX call is made
				$('#gesden-time-slot').html("Loading...");
				// When a date is selected, trigger an AJAX call to the server
				$.ajax({
					url: ajaxurl, // WordPress AJAX URL
					type: "POST",
					data: {
						action: 'fetch_api_response', // Custom action for the AJAX request
						selected_date: dateText // Send the selected date to the server
					},
					success: function(free_slot) {
						// Show the response in the #gesden-time-slot Id
						$('#gesden-time-slot').html(free_slot);
					},
					error: function(xhr, status, error) {
						// Handle errors if the request fails
						alert("An error occurred: " + error);
					}
				});
			}
		});
	});
	

})( jQuery );


