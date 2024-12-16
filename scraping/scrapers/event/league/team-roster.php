<?php
/**
 * Scraping data from chess-results.com event team roster.
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

// Default IDs
// NOTE:
// var $_GET[ 'art' ] on chess-results.com
$section_id = 8;

// Set URL
// $GLOBALS[ 'event_id' ] is created in scraping/update.php (automatic update) or admin/index.php (form insert/update)
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

        // Get event name
        $event_name = $html->find( '.defaultDialog', 0 )->getElementByTagName( 'h2' )->plaintext;

        // Update event data
        $wpdb->update( $wpdb->prefix . 'chess_scraper_event_settings',
                       array( 'event_name' => $event_name ),
                       array( 'event_id' => $GLOBALS[ 'event_id' ] ),
                       array( '%s' ),
                       array( '%d' ) );

        // Table row counter
        $table_row_count  = substr_count( $html, '"CRg1b"' );
        $table_row_count += substr_count( $html, '"CRg1' );
        $table_row_count += substr_count( $html, '"CRg2' );

        // Display table rows for each row
        for ( $i = 0; $i <= $table_row_count; $i++ ) {

            // Find string
            $string = $html->find( '.CRs1', 0 )->childNodes( $i )->plaintext;

            // If string is not null
            if ( $string != NULL ) {

                // Convert Unicode characters to UTF-8
                $string = chsc_unicode_to_utf8( $string );

                // If we found row with team name yet
                if ( isset( $player_row_start ) ) {

                    // Find row with the next closest team
                    $team_name_string = '&nbsp;&nbsp;';
                    $not_plain_string = $html->find( '.CRs1', 0 )->childNodes( $i );

                    if ( str_contains( $not_plain_string, $team_name_string ) ) {
                        // Set row number where players list of your team ends
                        $player_row_end = $i - 1;
                    }

                }

                // If we haven't found row with team name yet
                // If team is in string
                if ( str_contains( $string, $GLOBALS[ 'team_name' ] ) ) {
                    // Set row number where players list of your team starts
                    $player_row_start = $i + 2;
                }

                // If we found row numbers where players list starts and ends
                // Find and display team players
                if ( isset( $player_row_end ) ) {

                    // Find event_settings_id
                    $row = $wpdb->get_row(
                            $wpdb->prepare("SELECT id
                                            FROM {$wpdb->prefix}chess_scraper_event_settings
                                            WHERE event_id = %d AND
                                                  team_name = '%s'",
                                            $GLOBALS[ 'event_id' ], $GLOBALS[ 'team_name' ] ) );

                    // If MySQL error
                    if ( $wpdb->last_error ) {
                        echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
                    } else {
                        // Create global var, because this var is needed in the next files
                        $GLOBALS[ 'event_settings_id' ] = $row->id;
                    }

                    // Insert (or don't) players
                    for ( $j = $player_row_start; $j <= $player_row_end; $j++ ) {

                        $order_no = $html->find( '.CRs1', 0 )->childNodes( $j )->childNodes(0)->plaintext;
                        $name = $html->find( '.CRs1', 0 )->childNodes( $j )->childNodes(2)->plaintext;
                        $elo = $html->find( '.CRs1', 0 )->childNodes( $j )->childNodes(3)->plaintext;
                        $fide_id = $html->find( '.CRs1', 0 )->childNodes( $j )->childNodes(5)->plaintext;
                        $games = $html->find( '.CRs1', 0 )->childNodes( $j )->childNodes(8)->plaintext;
                        $points = $html->find( '.CRs1', 0 )->childNodes( $j )->childNodes(7)->plaintext;
                        $points_fmtd = str_replace(',','.',$points );

                        // Check if player exists for this event
                        $wpdb->get_row(
                            $wpdb->prepare("SELECT id
                                            FROM {$wpdb->prefix}chess_scraper_event_roster
                                            WHERE event_settings_id = %d AND
                                                  fide_id = %d",
                                        $GLOBALS[ 'event_settings_id' ], $fide_id ) );

                        // If MySQL error
                        if ( $wpdb->last_error ) {
                            echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
                        } else {

                            // If row doesn't exist
                            if ( !$wpdb->num_rows ) {
                                // Insert basic player's data
                                $wpdb->insert( $wpdb->prefix . 'chess_scraper_event_roster',
                                               array( 'event_settings_id' => $GLOBALS[ 'event_settings_id' ],
                                                      'fide_id' => $fide_id,
                                                      'name' => $name,
                                                      'order_no' => $order_no,
                                                      'elo' => $elo,
                                                      'games' => $games,
                                                      'points' => $points_fmtd ),
                                               array( '%d', '%d', '%s', '%d', '%d', '%d', '%f' ) );
                            }

                            // Check for error
                            if ( $wpdb->last_error ) {
                                $error = 1;
                            }

                        }

                    }

                    // Display message
                    if ( isset( $error ) ) {
                        echo '<span class="tsf-message error">Error: Some of the players\' data wasn\'t successfully uploaded. Try again.</span>';
                    } else {
                        echo '<span class="tsf-message success">Players\' data was successfully uploaded.</span>';
                    }

                    // We found what we needed
                    // Stop loop
                    break;

                }

            }

        }

    } else {
        // Event ID doesn't exist
        echo '<span class="tsf-message error">Error: Event ID doesn\'t exist.</span>';
    }

} else {
    // HTML content is empty
    echo '<span class="tsf-message error">Error: HTML content for team roster is empty. It is probably traffic problem or IP is blocked. Try again.</span>';
}

// Clean up resources
chsc_clear_resources( $html );