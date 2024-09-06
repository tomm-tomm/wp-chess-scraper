<?php
/*
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
$db_table = $wpdb->prefix . 'chess_scraper_event_settings';

// Db column name list
// NOTE:
// Column name => column type
// s = string, d = integer, f = float
$db_cols = array( 'team_name' => 's',
                  'event_id' => 'd',
                  'changed_at' => 's' );

// Do db operations (update table/select data)
$query_values = form_ops( $db_table, $db_cols );

/*
 * 2. Form
 */

// Build form
/** Form start **/
chsc_admin_form_start( $page, $section );

chsc_admin_form_block_start( 'Team and league identifiers' );
chsc_admin_form_note( 'Fill in the basic identification data for up to 3 teams whose data you want to upload to the database.<br><br>After you have successfully uploaded the basic data, the "import data button" will appear in the appropriate window with team data. With this button you can upload the data for the team.<br>The subsequent upload of the data can take several seconds. Please wait until the end to upload the data correctly.' );
chsc_admin_form_block_end();

/** Team #1 **/
$this_event_id = ( int )$query_values[ 'event_id_1' ][ 0 ];
$this_team_name = $query_values[ 'team_name_1' ][ 0 ];

// NOTE:
// If you want to add team, change also value of $league_teams in admin/index.php file
chsc_admin_form_block_start( 'Team #1 identifiers' );
chsc_admin_form_input_text( 'League ID', 'event_id_1', $this_event_id, 1, 'Find event on chess-results.com website and copy event ID number from URL.<br>Example here (needed number is bolded): https://chess-results.com/tnr<b>826760</b>.aspx' );
chsc_admin_form_input_text( 'Team name', 'team_name_1', $this_team_name, 0, 'Find team name on chess-results.com website (link above) and copy it in the exact wording here.<br>Example here: ŠK Dúbravan B' );

// If team is in db
if ( $this_event_id > 0 &&
     $this_team_name != '' ) {
    // Shortcode info
    chsc_admin_form_shortcode_note( 'If data for this team are updated, to view the league roster, copy this shortcode to the requested page:', '[chess-scraper section="league-roster" settings_id="1"]', 'padding-top-16' );
    chsc_admin_form_shortcode_note( 'If data for this team are updated, to view the league table and fixtures, copy this shortcode to the requested page:', '[chess-scraper section="league-details" settings_id="1"]' );

    // Last update
    chsc_admin_last_import_info( $query_values[ 'changed_at_1' ][ 0 ] );

    // Import button
    chsc_admin_form_submit( 'Import data for team ', 1 );
}

chsc_admin_form_block_end();

/** Team #2 **/
$this_event_id = ( int )$query_values[ 'event_id_2' ][ 0 ];
$this_team_name = $query_values[ 'team_name_2' ][ 0 ];

chsc_admin_form_block_start( 'Team #2 identifiers' );
chsc_admin_form_input_text( 'League ID', 'event_id_2', $this_event_id, 1, 'Find event on chess-results.com website and copy event ID number from URL.<br>Example here (needed number is bolded): https://chess-results.com/tnr<b>826760</b>.aspx' );
chsc_admin_form_input_text( 'Team name', 'team_name_2', $this_team_name, 0, 'Find team name on chess-results.com website and copy it in the exact wording here.<br>Example here: ŠK Dúbravan B' );

// If team is in db
if ( $this_event_id > 0 &&
     $this_team_name != '' ) {
    // Shortcode info
    chsc_admin_form_shortcode_note( 'If data for this team are updated, to view the league roster, copy this shortcode to the requested page:', '[chess-scraper section="league-roster" settings_id="2"]', 'padding-top-16' );
    chsc_admin_form_shortcode_note( 'If data for this team are updated, to view the league table and fixtures, copy this shortcode to the requested page:', '[chess-scraper section="league-details" settings_id="2"]' );

    // Last update
    chsc_admin_last_import_info( $query_values[ 'changed_at_2' ][ 0 ] );

    // Import button
    chsc_admin_form_submit( 'Import data for team ', 2 );
}

chsc_admin_form_block_end();

/** Team #3 **/
$this_event_id = ( int )$query_values[ 'event_id_3' ][ 0 ];
$this_team_name = $query_values[ 'team_name_3' ][ 0 ];

chsc_admin_form_block_start( 'Team #3 identifiers' );
chsc_admin_form_input_text( 'League ID', 'event_id_3', $this_event_id, 1, 'Find event on chess-results.com website and copy event ID number from URL.<br>Example here (needed number is bolded): https://chess-results.com/tnr<b>826760</b>.aspx' );
chsc_admin_form_input_text( 'Team name', 'team_name_3', $this_team_name, 0, 'Find team name on chess-results.com website and copy it in the exact wording here.<br>Example here: ŠK Dúbravan B' );

// If team is in db
if ( $this_event_id > 0 &&
     $this_team_name != '' ) {
    // Shortcode info
    chsc_admin_form_shortcode_note( 'If data for this team are updated, to view the league roster, copy this shortcode to the requested page:', '[chess-scraper section="league-roster" settings_id="3"]', 'padding-top-16' );
    chsc_admin_form_shortcode_note( 'If data for this team are updated, to view the league table and fixtures, copy this shortcode to the requested page:', '[chess-scraper section="league-details" settings_id="3"]' );

    // Last update
    chsc_admin_last_import_info( $query_values[ 'changed_at_3' ][ 0 ] );

    // Import button
    chsc_admin_form_submit( 'Import data for team ', 3 );
}

chsc_admin_form_block_end();

/** Form submit **/
chsc_admin_form_submit( 'Submit all teams settings' );

/** Form end **/
chsc_admin_form_end();