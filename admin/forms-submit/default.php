<?php
/*
 * Default page in admin theme settings after submitting the form
 * Needed in forms.php file
 */

// Identifiers section
if ( $_GET[ 'section' ] == 'identifiers' ) {

    // If form was submitted
    if ( isset( $_POST[ 'submit' ] ) ) {

        // Basic form data was sent
        if ( $_POST[ 'submit' ] == 'Submit club settings' ) {

            // For each club
            for ( $i = 1; $i <= $max_clubs_no; $i++ ) {

                // Requested post data control
                if ( isset( $_POST[ 'federation_id_' . $i ] ) &&
                     isset( $_POST[ 'team_id_' . $i ] ) ) {

                    // Update form data
                    chsc_admin_form_update_query_more_rows( $db_table, $db_cols, $i );

                } else {
                    // Requested post data wasn't sent
                    echo '<span class="tsf-message warning">Club #' . $i . ']: Both identifiers need to be filled in for the data to be downloaded.</span>';
                }

            }

        }  else if ( str_contains( $_POST[ 'submit' ], 'Import data' ) ) {
            // Update event settings request was sent

            // Set $GLOBALS[ 'club_settings_id' ]
            // Needed in scraper files
            $this_club_settings_id = substr( $_POST[ 'submit' ], strpos( $_POST[ 'submit' ], '#' ) + 1 );
            $GLOBALS[ 'club_settings_id' ] = ( int )$this_club_settings_id;

            // Initialize WP database
            global $wpdb;

            // Find $GLOBALS[ 'club_id' ]
            $row = $wpdb->get_row( "SELECT team_id, federation_id
                                    FROM {$wpdb->prefix}chess_scraper_team_settings
                                    WHERE id = " . $GLOBALS[ 'club_settings_id' ] );

            // If MySQL error
            if ( $wpdb->last_error ) {
                echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
            } else {

                // Create global vars, will be needed in included file
                $GLOBALS[ 'team_id' ] = $row->team_id;
                $GLOBALS[ 'federation_id' ] = $row->federation_id;

                // Scrape and import data
                // If ids are not null
                if ( $GLOBALS[ 'team_id' ] > 0 &&
                     $GLOBALS[ 'federation_id' ] != '' ) {

                    // Load scrapers
                    // Event team roster
                    require( PLUGIN_DIRECTORY . 'scraping/scrapers/club/' . $GLOBALS[ 'federation_id' ] . '/roster.php' );

                } else {
                    // If ids are null
                    echo '<span class="tsf-message error">Error: Both ids must be filled in.</span>';
                }

            }

            // Clear DB cache
            $wpdb->flush();

        }

    }

}