<?php
// Register the AJAX action for logged-in and logged-out users
add_action('wp_ajax_fetch_api_response', 'fetch_api_response_callback');
add_action('wp_ajax_nopriv_fetch_api_response', 'fetch_api_response_callback');

function fetch_api_response_callback() {
    if (isset($_POST['selected_date'])) {
        $selected_date = $_POST['selected_date']; // Get the selected date from the POST request

        // Format the selected date into the required format (e.g., Ymd)
        $selected_date = date("Ymd", strtotime($selected_date));

        // Initialize cURL to call the external API with the selected date
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://secure.infomed.es/AgendaOnline/SOAS.dll/infmd/rest/TRemoteAgent/DameDispSemAgdPrest/EXEVLSTT-EOCEZEAH-I4UFZG3E-UHIE4SWL-JEZH/' . $selected_date . '/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic bWFzYW5hY29yOjEyMzQ1Ng==' // Ensure you secure this credential
            ),
        ));

        // Execute the cURL request and get the response
        $response = curl_exec($curl);
        curl_close($curl);

        // Return the API response back to the front-end
        if ($response) {
            $api_response = $response;
        } else {
            echo 'API call failed';
        }
        // Decode the JSON data into a PHP array
        $data = json_decode($api_response, true);

        // Free booking slots
        $gesden_time_slots = $data['result'][0]['resultValue'][0]['fields']['Disps'];

        foreach ($gesden_time_slots as $gesden_time_slot) {
            // Split the string to separate the date-time part from the duration
            list($datetime, $duration) = explode(':', $gesden_time_slot);

            // Convert the date-time part into a readable format (from YYYYMMDDHHMM)
            $formatted_time = DateTime::createFromFormat('YmdHi', $datetime);
            $slot_dates = $formatted_time->format('Ymd');
            $slot_date = $formatted_time->format('F d, Y');
            $slot_time = $formatted_time->format('g:i A');

            if ($selected_date == $slot_dates) {
                // Display the date in the desired format: Day, Date, Time
                $free_slot = "<div class='gesden-free-time-slot' style='border: 1px solid #ccc; cursor: pointer; text-align: center; padding: 8px 30px; border-radius: 5px; margin-bottom: 10px;' data-date='" . $slot_date . "' data-time='" . $slot_time . "'>" . $slot_time . "</div>";

                echo $free_slot;
            }
        }
    }
    
    wp_die(); // Required to terminate properly in AJAX requests
}
?>
<style>
    .gesden-time-slot-selected {
        background-color: black;
        color: white !important;
    }
</style>
<script>
    $(document).ready(function() {
		$('.gesden-free-time-slot').on('click', function(){
			// Get the data-date and data-time attributes from the clicked element
			let slotDate = $(this).data('date');
            let slotTime = $(this).data('time');

            // add Class to the clicked element
            $(this).addClass('gesden-time-slot-selected');
            console.log(slotDate);
            console.log(slotTime);

            $.ajax({
                url: ajaxurl, // WordPress AJAX URL
                type: "POST",
                data: {
                    action: 'update_api_response_in_bd', // Custom action for the AJAX request
                    selected_date: slotDate, // Send the selected date to the server
                    selected_time: slotTime
                },
                success: function(free_slot) {
                    // reload the page
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle errors if the request fails
                    alert("An error occurred: " + error);
                }
            });
		});
	});
</script>
<?php
add_action('wp_ajax_update_api_response_in_bd', 'update_api_response_in_bd_callback');
add_action('wp_ajax_nopriv_update_api_response_in_bd', 'update_api_response_in_bd_callback');

function update_api_response_in_bd_callback() {
    if (isset($_POST['selected_date']) && isset($_POST['selected_time'])) {
        $selected_date = $_POST['selected_date'];
        $selected_time = $_POST['selected_time'];
        $post_id  = 4521;

        update_post_meta($post_id, 'selected_date_gro', $selected_date);
    } else {
        $selected_date = '';
        $selected_time = '';
    }
}