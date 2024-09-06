<?php
/*
 * Updates page in admin theme settings after submitting the form
 * Needed in forms.php file
 */

// Requested post data was sent
if ( $_POST[ 'ip' ] ) {

    // Make array from sent IPs
    $ip_array = explode( ',', $_POST[ 'ip' ] );

    // Check all IPs in array
    foreach ( $ip_array as $key ) {

        // Check validity of sent IP adress
        if ( filter_var( trim( $key ), FILTER_VALIDATE_IP ) === false ) {
            // Invalid format
            echo '<span class="tsf-message warning">Invalid format of IP addresses. Type valid values.</span>';

            $ip_error = 1;
            break;
        }

    }

}

// If there was no error with IP format
// Update form data
if ( !isset( $ip_error ) ) {
    chsc_admin_form_update_query_single( $db_table, $db_cols );
}