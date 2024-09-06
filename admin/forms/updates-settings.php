<?php
/*
 * For "default -> allowed_ips" page in admin theme settings
 * Loaded in index.php file
 *
 * 1. Db operations
 * 2. Form
 */

/*
 * 1. Db operations
 */

// Set db vars

// Db table
$db_table = $wpdb->prefix . 'chess_scraper_updates_allowed_ips';

// Db column name list
// NOTE:
// Column name => column type
// s = string, d = integer, f = float
$db_cols = array( 'ip' => 's' );

// Do db operations (update table/select data)
$query_values = form_ops( $db_table, $db_cols );

/*
 * 2. Form
 */

// Build form
chsc_admin_form_start( $page, $section );

chsc_admin_form_block_start( 'Automatic updates' );
chsc_admin_form_note( 'Club data can be uploaded automatically after the first login from allowed IP addresses in a given month.<br>The subsequent upload of the data can take several seconds. Please wait until the end to upload the data correctly.' );
chsc_admin_form_input_text( 'Allowed IPs', 'ip', $query_values[ 'ip' ][ 0 ], 0, 'Enter the requested IP addresses in this field.<br>Separate them with commas.<br>Note: You can get your IP address <a href="https://whatismyipaddress.com/" target="_blank">here</a>.' );
chsc_admin_form_block_end();

chsc_admin_form_submit( 'Submit' );

chsc_admin_form_end();