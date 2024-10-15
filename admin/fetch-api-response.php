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

        // Check if the response is valid
        if ($response === false) {
            echo 'API call failed';
            wp_die(); // Properly terminate the request
        }

        // Process the response
        select_the_time_slot($response, $selected_date);

    }

    wp_die(); // Required to terminate properly in AJAX requests
}

?>

<?php
add_action('wp_ajax_update_api_response_in_bd', 'update_api_response_in_bd_callback');
add_action('wp_ajax_nopriv_update_api_response_in_bd', 'update_api_response_in_bd_callback');

function update_api_response_in_bd_callback() {
    // Check if the required POST fields are set
    if (isset($_POST['selected_date']) && isset($_POST['selected_time'])) {
        $selected_date = $_POST['selected_date'];
        $selected_time = $_POST['selected_time'];

        // Get user information
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $user_name = $user->display_name;
        $user_email = $user->user_email;
        $user_first_name = $user->first_name;
        $user_last_name = $user->last_name;

        // Get user phone from user meta if available
        $user_phone = get_user_meta($user_id, 'billing_phone', true); // Example for WooCommerce billing phone

        // Table name
        global $wpdb;
        $bookly_customers = $wpdb->prefix . 'bookly_customers';
        $bookly_customer_appointments = $wpdb->prefix . 'bookly_customer_appointments';
        $bookly_appointments = $wpdb->prefix . 'bookly_appointments';

        // Insert data into the table
        $insert_customer = $wpdb->insert($bookly_customers, array(
            'wp_user_id' => $user_id,
            'facebook_id' => NULL, // Set to NULL if not applicable
            'group_id' => NULL, // Set to NULL if not applicable
            'full_name' => $user_first_name . ' ' . $user_last_name,
            'first_name' => $user_first_name,
            'last_name' => $user_last_name,
            'phone' => $user_phone, // Set to NULL if not applicable
            'email' => $user_email,
            'created_at' => current_time('mysql') // Use current_time for created_at
        ));

        // Prepare start and end dates for the appointment
        $start_datetime = new DateTime($selected_date . ' ' . $selected_time);
        $end_datetime = clone $start_datetime;
        $end_datetime->modify('+1 hour');

        // Insert bookly_appointments
        $insert_bookly_appointments = $wpdb->insert($bookly_appointments, array(
            'location_id' => NULL,
            'staff_id' => 1,
            'staff_any' => 0,
            'service_id' => 2,
            'start_date' => $start_datetime->format('Y-m-d H:i:s'),
            'end_date' => $end_datetime->format('Y-m-d H:i:s'),
            'created_from' => 'bookly',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ));

        // Get the last inserted appointment ID
        $appointment_id = $wpdb->insert_id;

        // Define the table name
        $bookly_payments_table = $wpdb->prefix . 'bookly_payments';

        // Insert the payment entry
        $insert_payment = $wpdb->insert(
            $bookly_payments_table,
            array(
                'coupon_id'                => null, // Replace with your actual value or null
                'gift_card_id'             => null, // Replace with your actual value or null
                'type'                      => 'free', // Replace with your actual value
                'total'                     => 0, // Replace with your actual value
                'tax'                       => 0, // Replace with your actual value
                'paid'                      => 0, // Replace with your actual value
                'paid_type'                => 'in_full', // Replace with your actual value
                'gateway_price_correction' => 0, // Replace with your actual value
                'status'                    => 'pending', // Replace with your actual value
                'token'                     => md5(uniqid(rand(), true)),
                'details'                  => json_encode(array('item' => 'Web development')), // Replace with your actual value
                'order_id'                 => null, // Replace with your actual value or null
                'ref_id'                   => null, // Replace with your actual value or null
                'invoice_id'               => null, // Replace with your actual value or null
                'created_at'               => current_time('mysql'), // Automatically use current date and time
                'updated_at'               => current_time('mysql')  // Automatically use current date and time
            ),
            array(
                '%s', // coupon_id (string or null)
                '%s', // gift_card_id (string or null)
                '%s', // type (string)
                '%f', // total (float)
                '%f', // tax (float)
                '%f', // paid (float)
                '%s', // paid_type (string)
                '%f', // gateway_price_correction (float)
                '%s', // status (string)
                '%s', // token (string)
                '%s', // details (string)
                '%d', // order_id (integer or null)
                '%d', // ref_id (integer or null)
                '%d', // invoice_id (integer or null)
                '%s', // created_at (datetime)
                '%s'  // updated_at (datetime)
            )
        );

        
        // Get the last inserted Payment ID
        $payment_id = $wpdb->insert_id;

        // get token from bookly_payments table
        $token = $wpdb->get_var("SELECT token FROM $bookly_payments_table WHERE id = $payment_id");

        $bookly_orders_table = $wpdb->prefix . 'bookly_orders';

        // Insert the order entry
        $insert_order = $wpdb->insert(
            $bookly_orders_table,
            array(
                'token' => $token
            ),
            array(
                '%s'  // token (string)
            )
        );

        // Get the last inserted Order ID
        $order_id = $wpdb->insert_id;
        

        // Insert bookly_customer_appointments
        $insert_customer_appointments = $wpdb->insert($bookly_customer_appointments, array(
            'customer_id' => $user_id,
            'appointment_id' => $appointment_id,
            'payment_id' => $payment_id,
            'order_id' => $order_id,
            'number_of_persons' => 1,
            'units' => 1,
            'extras_multiply_nop' => 1,
            'status' => 'approved',
            'status_changed_at' => current_time('mysql'),
            'token' => md5($user_id . time()),
            'created_from' => 'frontend',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ));

        // wp_bookly_email_log
        $bookly_email_log = $wpdb->prefix . 'bookly_email_log';

        // Insert data into the wp_bookly_email_log table
        $insert_email_log = $wpdb->insert(
            $bookly_email_log, // Table name
            array(
                'to' => 'sonthing@gmail.com',
                'subject' => 'Your appointment information',
                'body' => '<p>Dear john,</p><p>This is a confirmation that you have booked Web development.</p><p>We are waiting for you on October 15, 2024, at 12:00 PM.</p><p>Thank you for choosing our company.</p>',
                'headers' => json_encode(array("is_html" => true, "from" => array("name" => "Bookly", "email" => "sobuz0349@gmail.com"))), // Encoded JSON
                'attach' => json_encode([]), // Empty array for attachments
                'type' => 'new_booking',
                'created_at' => current_time('mysql'), // Current time in MySQL format
            ),
            array(
                '%s', // 'to'
                '%s', // 'subject'
                '%s', // 'body'
                '%s', // 'headers'
                '%s', // 'attach'
                '%s', // 'type'
                '%s', // 'created_at'
            )
        );
        $bookly_log_table = $wpdb->prefix . 'bookly_log';

        // get author name from wp_users table
        $wp_users_table = $wpdb->prefix . 'users';
        $author = $wpdb->get_var("SELECT display_name FROM $wp_users_table WHERE ID = $user_id");
        // Insert a log entry
        $insert_log = $wpdb->insert(
            $bookly_log_table,
            array(
                'action'     => 'create',
                'target'     => 'wp_bookly_payments',
                'target_id'  => 7,
                'author'     => $author,
                'details'    => json_encode(array(
                    'id' => 5,
                    'coupon_id' => null,
                    'gift_card_id' => null,
                    'type' => 'free',
                    'total' => 0,
                    'tax' => 0,
                    'paid' => 0,
                    'paid_type' => '',
                    'gateway_price_correction' => 0,
                    'status' => 'pending',
                    'token' => $token,
                    'details' => null,
                    'order_id' => $order_id,
                    'ref_id' => null,
                    'invoice_id' => null,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                )),
                'ref'        => "Bookly\\Lib\\Base\\Gateway->createPayment\nBookly\\Lib\\Base\\Gateway->createIntent\nBookly\\Lib\\Base\\Gateway->createCheckout\n",
                'comment'    => '',
                'created_at' => '2024-10-14 13:11:45'
            ),
            array(
                '%s', // action
                '%s', // target
                '%d', // target_id
                '%s', // author
                '%s', // details (JSON encoded string)
                '%s', // ref
                '%s', // comment
                '%s'  // created_at
            )
        );
        
        // Check if insertion was successful
        if ($insert_customer && $insert_bookly_appointments && $insert_customer_appointments && $insert_email_log && $insert_log && $insert_order && $insert_payment) {
            echo 'Data inserted successfully.';
        } else {
            echo 'Error inserting data. Please try again.';
        }
    } else {
        echo 'Invalid request.';
    }

    wp_die(); // Required to terminate properly in AJAX requests
}
?>
