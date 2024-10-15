<?php
function select_the_time_slot($response, $selected_date) {
    // Decode the JSON data into a PHP array
    $data = json_decode($response, true);

    // Check if the expected structure exists in the response
    if (isset($data['result'][0]['resultValue'][0]['fields']['Disps'])) {
        $gesden_time_slots = $data['result'][0]['resultValue'][0]['fields']['Disps'];

        global $wpdb; // Access the global $wpdb object

        // Define the table name
        $table_name = $wpdb->prefix . 'bookly_appointments';

        // Write the SQL query to get booked slots for the selected date
        $query = $wpdb->prepare("SELECT start_date FROM {$table_name} WHERE DATE(start_date) = %s", $selected_date);

        // Execute the query and fetch results
        $results = $wpdb->get_results($query);

        // Create an array to store booked times for the selected date
        $booked_slots = array();

        // Check if any results were returned
        if (!empty($results)) {
            // Assuming $results contains rows with start_date
            foreach ($results as $row) {
                // Create DateTime object from the start_date
                $_formatted_time = DateTime::createFromFormat('Y-m-d H:i:s', $row->start_date);

                // Check if the date was parsed correctly
                if ($_formatted_time !== false) {
                    // Format only the time as His (Hours, Minutes, Seconds)
                    $booked_slots[] = $_formatted_time->format('His');
                } else {
                    echo 'Error parsing the start_date: ' . $row->start_date . '<br>';
                }
            }
        }

        // Iterate over the available slots from the API
        foreach ($gesden_time_slots as $gesden_time_slot) {
            // Split the string to separate the date-time part from the duration
            list($datetime, $duration) = explode(':', $gesden_time_slot);

            // Convert the date-time part into a readable format (from YYYYMMDDHHMM)
            $formatted_time = DateTime::createFromFormat('YmdHi', $datetime);
            $slot_dates = $formatted_time->format('Ymd');
            $slot_times = $formatted_time->format('g:i A');
            // Convert date-time to date and time format separately
            $slot_date = $formatted_time->format('Y-m-d');
            $slot_time = $formatted_time->format('H:i:s');
            $api_slot_time_his = $formatted_time->format('His'); // Get the slot time in His format

            // Only show slots for the selected date and not booked
            if ($selected_date == $slot_dates && !in_array($api_slot_time_his, $booked_slots)) {
                // Display the free slot
                $free_slot = "<div class='gesden-free-time-slot' style='border: 1px solid #ccc; cursor: pointer; text-align: center; padding: 8px 30px; border-radius: 5px; margin-bottom: 10px;' data-date='" . esc_attr($slot_date) . "' data-time='" . esc_attr($slot_time) . "'>" . esc_html($slot_times) . "</div>";
                echo $free_slot;
            }
        }
        
    } else {
        echo 'No available slots found.';
    }
}
