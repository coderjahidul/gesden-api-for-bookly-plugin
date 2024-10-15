<?php
function select_the_time_slot($response, $selected_date) {
    // Decode the JSON data into a PHP array
    $data = json_decode($response, true);

    // Check if the expected structure exists in the response
    if (isset($data['result'][0]['resultValue'][0]['fields']['Disps'])) {
        $gesden_time_slots = $data['result'][0]['resultValue'][0]['fields']['Disps'];

        foreach ($gesden_time_slots as $gesden_time_slot) {
            // Split the string to separate the date-time part from the duration
            list($datetime, $duration) = explode(':', $gesden_time_slot);

            // Convert the date-time part into a readable format (from YYYYMMDDHHMM)
            $formatted_time = DateTime::createFromFormat('YmdHi', $datetime);
            $slot_dates = $formatted_time->format('Ymd');
            $slot_times = $formatted_time->format('g:i A');
            // Convert 2024-09-18 15:30:00 to 2024-09-18
            $slot_date = $formatted_time->format('Y-m-d');
            // Convert Hi to 15:30:00
            $slot_time = $formatted_time->format('H:i:s');

            if ($selected_date == $slot_dates) {
                // Display the date in the desired format: Day, Date, Time
                $free_slot = "<div class='gesden-free-time-slot' style='border: 1px solid #ccc; cursor: pointer; text-align: center; padding: 8px 30px; border-radius: 5px; margin-bottom: 10px;' data-date='" . esc_attr($slot_date) . "' data-time='" . esc_attr($slot_time) . "'>" . esc_html($slot_times) . "</div>";

                echo $free_slot;
            }
        }
    } else {
        echo 'No available slots found.';
    }
}