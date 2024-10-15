
    jQuery(document).ready(function($) {
        // Attach click event handler for each time slot
        $(document).on('click', '.gesden-free-time-slot', function() {
            // Remove 'selected' class from any previously selected slots
            $('.gesden-free-time-slot').removeClass('selected');
            
            // Add 'selected' class to the clicked slot
            $(this).addClass('selected');

            // Get the data-date and data-time attributes from the clicked element
            let slotDate = $(this).data('date');
            let slotTime = $(this).data('time');

            // Log the date and time for debugging
            console.log("Selected Date: " + slotDate);
            console.log("Selected Time: " + slotTime);

            // AJAX request to save the selected time slot in the database
            $.ajax({
                url: ajaxurl, // WordPress AJAX URL
                type: "POST",
                data: {
                    action: 'update_api_response_in_bd', // Custom action for AJAX request
                    selected_date: slotDate, // Send the selected date
                    selected_time: slotTime // Send the selected time
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle errors if the AJAX request fails
                    alert("Error occurred: " + error);
                }
            });
        });
    });

