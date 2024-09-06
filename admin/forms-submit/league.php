<?php
/*
 * League page in admin theme settings after submitting the form
 * Needed in forms.php file
 * 
 * @package Chess_Scraper
 * @version 1.0.0
 */

// Identifiers section
if ( $_GET[ 'section' ] == 'identifiers' ) {

    // If form was submitted
    if ( isset( $_POST[ 'submit' ] ) ) {

        // Basic form data was sent
        if ( $_POST[ 'submit' ] == 'Submit all teams settings' ) {

            // For each league team
            for ( $i = 1; $i <= $max_league_teams_no; $i++ ) {

                // Requested post data control
                if ( isset( $_POST[ 'event_id_' . $i ] ) &&
                     isset( $_POST[ 'team_name_' . $i ] ) ) {
                    // Requested post data was sent

                    // Update form data
                    chsc_admin_form_update_query_more_rows( $db_table, $db_cols, $i );

                } else {
                    // Requested post data wasn't sent
                    echo '<span class="tsf-message warning">Team #' . $i . ' [' . $_POST[ 'team_name_' . $i ] . ']: Both IDs need to be filled in for the data to be downloaded.</span>';
                }

            }

        } else if ( str_contains( $_POST[ 'submit' ], 'Import data' ) ) {
            // Update event settings request was sent

            // Set $GLOBALS[ 'event_settings_id' ]
            // Needed in scraper files
            $this_event_settings_id = substr( $_POST[ 'submit' ], strpos( $_POST[ 'submit' ], '#' ) + 1 );
            $GLOBALS[ 'event_settings_id' ] = ( int )$this_event_settings_id;

            // Initialize WP database
            global $wpdb;

            // Find $GLOBALS[ 'event_id' ]
            $row = $wpdb->get_row( "SELECT event_id, team_name
                                    FROM {$wpdb->prefix}chess_scraper_event_settings
                                    WHERE id = " . $GLOBALS[ 'event_settings_id' ] . "" );

            // If MySQL error
            if ( $wpdb->last_error ) {
                echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
            } else {

                // Create global vars, will be needed in included files
                $GLOBALS[ 'event_id' ] = $row->event_id;
                $GLOBALS[ 'team_name' ] = $row->team_name;

                // Scrape and import data
                // If ids are not null
                if ( $GLOBALS[ 'event_id' ] > 0 &&
                     $GLOBALS[ 'team_name' ] != '' ) {

                    // Load scrapers
                    // Event team roster
                    require( PLUGIN_DIRECTORY . 'scraping/scrapers/event/league/team-roster.php' );
                    // Event schedule
                    require( PLUGIN_DIRECTORY . 'scraping/scrapers/event/league/schedule.php' );
                    // Event fixtures
                    require( PLUGIN_DIRECTORY . 'scraping/scrapers/event/league/fixtures.php' );
                    // Event ranking
                    require( PLUGIN_DIRECTORY . 'scraping/scrapers/event/league/ranking.php' );

                } else {
                    // If ids are null
                    echo '<span class="tsf-message error">Error: Both IDs must be filled in.</span>';
                }

            }

            // Clear DB cache
            $wpdb->flush();

        }

    }

}