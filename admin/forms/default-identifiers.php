<?php
/*
 *
 * NOTE: This section doesn't work noe because chess.sk changed HTML structure.
 *
 * For "default -> identifiers" page in admin theme settings
 * Loaded in index.php file
 *
 * 1. Db operations
 * 2. Form
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

/*
 * 1. Db operations
 */

// Set db vars

// Db table
$db_table = $wpdb->prefix . 'chess_scraper_team_settings';

// Db column name list
// NOTE:
// Column name => column type
// s = string, d = integer, f = float
$db_cols = array( 'team_id'       => 'd',
                  'federation_id' => 's',
                  'changed_at'    => 's' );

// Do db operations (update table/select data)
$query_values = form_ops( $db_table, $db_cols );

/*
 * 2. Form
 */

// Build form
/** Form start **/
chsc_admin_form_start( $page, $section );

chsc_admin_form_block_start( 'Club identifiers (for Slovakia federation only now)' );
chsc_admin_form_note( '<div style="color: #ff0000; font-weight: bold;">This section doesn\'t work now.</div>' );
//chsc_admin_form_note( 'Fill in both identifiers for the club whose data you want to upload to the database. Then submit the form.<br>Subsequent uploading of data may take several tens of seconds. Please wait until the end to upload the data correctly.' );

/** Club #1 **/
/*
$this_team_id = ( int )$query_values[ 'team_id_1' ][ 0 ];
$this_federation_id = $query_values[ 'federation_id_1' ][ 0 ];

chsc_admin_form_select( 'Federation', 'federation_id_1', array( 'Slovakia' => 'svk' ), $this_federation_id );
chsc_admin_form_input_text( 'Club ID (chess.sk)', 'team_id_1', $this_team_id, 1, 'Find your club on chess.sk website and copy your klubId number from URL.<br>Example here (needed number is bolded): chess.sk/index.php?str=clenovia&klubId=<b>10120</b>' );

// If IDs are not empty, show shortcode
if ( $this_federation_id != '' &&
     $this_team_id > 0 ) {
     chsc_admin_form_shortcode_note( 'To view the data, copy this shortcode to the requested page:', '[chess-scraper section="club-roster" settings_id="1"]', 'padding-top-16' );

     // Last update
     chsc_admin_last_import_info( $query_values[ 'changed_at_1' ][ 0 ] );

     // Import button
     chsc_admin_form_submit( 'Import data for club ', 1 );
}

chsc_admin_form_block_end();

// Submit button
chsc_admin_form_submit( 'Submit club settings' );*/

// End form
chsc_admin_form_end();