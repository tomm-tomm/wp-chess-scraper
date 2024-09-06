<?php
/**
 * Scraping data from chess-results.com event ranking.
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

// Default IDs
// NOTE:
// var $_GET[ 'art' ] on chess-results.com
$section_id = 46;

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

        // Display table rows for each row
        for ( $i = 1; $i <= $table_row_count; $i++ ) {

            // Find the row values
            $team = $html->find( ".CRs1", 0 )->childNodes( $i )->childNodes( 2 )->plaintext;
            $ranking = $html->find( ".CRs1", 0 )->childNodes( $i )->childNodes( 0 )->plaintext;
            $games = $html->find( ".CRs1", 0 )->childNodes( $i )->childNodes( 3 )->plaintext;
            $wins = $html->find( ".CRs1", 0 )->childNodes( $i )->childNodes( 4 )->plaintext;
            $draws = $html->find( ".CRs1", 0 )->childNodes( $i )->childNodes( 5 )->plaintext;
            $losses = $html->find( ".CRs1", 0 )->childNodes( $i )->childNodes( 6 )->plaintext;
            $tb1 = str_replace( ',', '.', $html->find( ".CRs1", 0 )->childNodes( $i )->childNodes( 7 )->plaintext );
            $tb2 = str_replace( ',', '.', $html->find( ".CRs1", 0 )->childNodes( $i )->childNodes( 8 )->plaintext );
            $tb3 = str_replace( ',', '.', $html->find( ".CRs1", 0 )->childNodes( $i )->childNodes( 9 )->plaintext );

            // Convert Unicode characters to UTF-8
            $team = chsc_unicode_to_utf8( $team );

            // Find event_settings_id
            $row = $wpdb->get_row(
                        $wpdb->prepare("SELECT id
                                        FROM {$wpdb->prefix}chess_scraper_event_ranking
                                        WHERE event_settings_id = %d AND
                                              team = '%s'",
                                        $GLOBALS[ 'event_settings_id' ], $team ) );

            // If MySQL error
            if ( $wpdb->last_error ) {
                echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
            } else {

                // If row doesn't exist
                if ( !$wpdb->num_rows ) {
                    // Insert event ranking data
                    $wpdb->insert( $wpdb->prefix . 'chess_scraper_event_ranking',
                                   array( 'event_settings_id' => $GLOBALS[ 'event_settings_id' ],
                                          'team' => $team,
                                          'ranking' => $ranking,
                                          'games' => $games,
                                          'wins' => $wins,
                                          'draws' => $draws,
                                          'losses' => $losses,
                                          'tb1' => $tb1,
                                          'tb2' => $tb2,
                                          'tb3' => $tb3 ),
                                   array( '%d', '%s', '%d', '%d', '%d', '%d', '%d', '%f', '%f', '%f' ) );

                    // Check for error
                    if ( $wpdb->last_error ) {
                        $error = 1;
                    }
                }

            }

        }

        // Display message
        if ( isset( $error ) ) {
            echo '<span class="tsf-message error">Error: Some of the ranking data wasn\'t successfully uploaded. Try again.</span>';
        } else {
            echo '<span class="tsf-message success">Ranking data was successfully uploaded.</span>';
        }

    }

} else {
    // HTML content is empty
    echo '<span class="tsf-message error">Error: HTML content for team roster is empty. It is probably traffic problem or IP is blocked. Try again.</span>';
}

// Clean up resources
chsc_clear_resources( $html );