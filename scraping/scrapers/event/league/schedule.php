<?php
/**
 * Scraping data from chess-results.com event schedule.
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

// Default IDs
// NOTE:
// var $_GET[ 'art' ] on chess-results.com
$section_id = 14;

// Set URL
$url = chsc_set_event_url( $GLOBALS[ 'event_id' ], $section_id );

// Get HTML content from URL
$html = chsc_get_html_content( $url );

// If HTML content is not empty
if ( $html != '' ) {

    // Alert for non-exisiting event ID
    $event_id_not_found = 'Der angeforderte Satz wurde nicht gefunden';

    // If event ID exists
    if ( !str_contains( $html, $event_id_not_found ) ) {

        // Initalize WP database functions
        global $wpdb;

        // Table row counter
        $table_row_count  = substr_count( $html, '"CRg1"' );
        $table_row_count += substr_count( $html, '"CRg2"' );

        // Set and display table rows
        for ( $i = 1; $i <= $table_row_count; $i++ ) {

            // Find and format the row values
            // Round
            $round = $html->find( ".CRs1", 0 )->childNodes( $i )->childNodes( 0 )->plaintext;

            // Date
            $orig_date = $html->find( ".CRs1", 0 )->childNodes( $i )->childNodes( 1 )->plaintext;
            $date = date( 'Y-n-j', strtotime( $orig_date ) );

            // Time
            $time = $html->find( ".CRs1", 0 )->childNodes( $i )->childNodes( 2 )->plaintext;

            // Merge date and time
            $datetime = $date . ' ' . $time;

            // Find event_settings_id
            $row = $wpdb->get_row(
                    $wpdb->prepare("SELECT id
                                    FROM {$wpdb->prefix}chess_scraper_event_schedule
                                    WHERE event_settings_id = %d AND
                                          round = %d AND
                                          date = '%s'",
                                    $GLOBALS[ 'event_settings_id' ], $round, $datetime ) );

            // If MySQL error
            if ( $wpdb->last_error ) {
                echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
            } else {

                // If row doesn't exist
                if ( !$wpdb->num_rows ) {
                    // Insert event schedule data
                    $wpdb->insert( $wpdb->prefix . 'chess_scraper_event_schedule',
                                   array( 'event_settings_id' => $GLOBALS[ 'event_settings_id' ],
                                          'round' => $round,
                                          'date' => $datetime ),
                                   array( '%d', '%d', '%s' ) );

                    // Check for error
                    if ( $wpdb->last_error ) {
                        $error = 1;
                    }
                }

            }

        }

        // Display message
        if ( isset( $error ) ) {
            echo '<span class="tsf-message error">Error: Some of the schedule data wasn\'t successfully uploaded. Try again.</span>';
        } else {
            echo '<span class="tsf-message success">Schedule data was successfully uploaded.</span>';
        }

    }

} else {
    // HTML content is empty
    echo '<span class="tsf-message error">Error: HTML content for team roster is empty. It is probably traffic problem or IP is blocked. Try again.</span>';
}

// Clean up resources
chsc_clear_resources( $html );