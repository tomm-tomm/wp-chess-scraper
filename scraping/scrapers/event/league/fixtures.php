<?php
/**
 * Scraping data from chess-results.com event results by round.
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

// Default IDs
// NOTE:
// var $_GET[ 'art' ] on chess-results.com
$section_id = 3;

// Find number of final scheduled round for the event
$row = $wpdb->get_row(
           $wpdb->prepare( "SELECT MAX( round ) AS final_scheduled_round
                            FROM {$wpdb->prefix}chess_scraper_event_schedule
                            WHERE event_settings_id = %d",
                           $GLOBALS[ 'event_settings_id' ] ) );

// If MySQL error
if ( $wpdb->last_error ) {
    echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
} else {
    // If row exists
    if ( $wpdb->num_rows ) {
        $final_scheduled_round = $row->final_scheduled_round;
    } else {
        // If row doesn't exist
        $final_scheduled_round = 0;
    }
}

// If schedule is not empty
if ( $final_scheduled_round > 0 ) {

    // Find number of last uploaded round for the event
    $row = $wpdb->get_row(
               $wpdb->prepare( "SELECT MAX( round ) AS last_uploaded_round
                                FROM {$wpdb->prefix}chess_scraper_event_fixtures
                                WHERE event_settings_id = %d AND
                                      ( result != '' AND result != ' : ' )",
                               $GLOBALS[ 'event_settings_id' ] ) );

    // If MySQL error
    if ( $wpdb->last_error ) {
        echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
    } else {
        // If row exists
        if ( $wpdb->num_rows ) {
            $last_uploaded_round = $row->last_uploaded_round;
        } else {
            // If row doesn't exist
            $last_uploaded_round = 0;
        }
    }

    // Initialize boards per round var
    $boards_per_round = 8;

    // Upload info about each round
    for ( $round_id = $last_uploaded_round + 1; $round_id <= $final_scheduled_round; $round_id++ ) {

        // Set URL
        $url = chsc_set_event_url( $GLOBALS[ 'event_id' ], $section_id, $round_id );

        // Get HTML content from URL
        $html = chsc_get_html_content( $url );

        // If HTML content is not empty
        if ( $html != '' ) {

            // Alert for non-exisiting event ID
            $event_id_not_found = 'Der angeforderte Satz wurde nicht gefunden';

            // If event ID exists
            if ( !str_contains( $html, $event_id_not_found ) ) {

                // Table row counter
                $table_row_count = substr_count( $html, '"CRg1b"' ) - 1;

                // Display table rows
                for ( $i = 1; $i <= $table_row_count; $i++ ) {

                    // Set $j (table row number)
                    if ( $i != 1 ) {
                        // 9 = number of other row with team names
                        $j += 9;
                    } else {
                        $j = 1;
                    }

                    // Find and set team names
                    // Home team
                    $team_home = $html->find( '.CRs1', 0 )->childNodes( $j )->childNodes( 2 )->plaintext;
                    $team_home = str_replace( '&nbsp;', '', $team_home );
                    // Convert Unicode characters to UTF-8
                    $team_home = chsc_unicode_to_utf8( $team_home );

                    // Away team
                    // Check specific column
                    // Needed because number of columns varies
                    $team_away_delimiter = $html->find( '.CRs1', 0 )->childNodes( $j )->childNodes( 4 )->plaintext;

                    if ( $team_away_delimiter == '-' ) {
                        $team_away = $html->find( '.CRs1', 0 )->childNodes( $j )->childNodes( 6 )->plaintext;
                    } else if ( $team_away_delimiter == '' ) {
                        $team_away = $html->find( '.CRs1', 0 )->childNodes( $j )->childNodes( 7 )->plaintext;
                    }

                    $team_away = str_replace( '&nbsp;', '', $team_away );
                    $team_away = chsc_unicode_to_utf8( $team_away );

                    // Break the $i iteration and get board list
                    if ( str_contains( $team_home, $GLOBALS[ 'team_name' ] ) ||
                         str_contains( $team_away, $GLOBALS[ 'team_name' ] ) ) {

                        // Match result
                        // Check specific column
                        if ( $team_away_delimiter == '-' ) {
                            $match_result = $html->find( '.CRs1', 0 )->childNodes( $j )->childNodes( 8 )->plaintext;
                        } else if ( $team_away_delimiter == '' ) {
                            $match_result = $html->find( '.CRs1', 0 )->childNodes( $j )->childNodes( 10 )->plaintext;
                        }

                        $match_result = chsc_check_result( $match_result );

                        // Find fixture_id
                        $row = $wpdb->get_row(
                                   $wpdb->prepare( "SELECT id
                                                    FROM {$wpdb->prefix}chess_scraper_event_fixtures
                                                    WHERE event_settings_id = %d AND
                                                          round = %d",
                                                    $GLOBALS[ 'event_settings_id' ], $round_id ) );

                        // If MySQL error
                        if ( $wpdb->last_error ) {
                            echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
                        } else {

                            // If row doesn't exist
                            if ( !$wpdb->num_rows ) {

                                // Insert event fixture data
                                $wpdb->insert( $wpdb->prefix . 'chess_scraper_event_fixtures',
                                               array( 'event_settings_id' => $GLOBALS[ 'event_settings_id' ],
                                                      'round' => $round_id,
                                                      'team_home' => $team_home,
                                                      'team_away' => $team_away,
                                                      'result' => $match_result ),
                                               array( '%d', '%d', '%s', '%s', '%s' ) );

                                // Check for error
                                if ( $wpdb->last_error ) {
                                    $error = 1;
                                } else {
                                    // Get fixture_id
                                    $fixture_id = $wpdb->insert_id;
                                }

                            } else {

                                // If row exists
                                // Update event fixture data
                                $wpdb->update( $wpdb->prefix . 'chess_scraper_event_fixtures',
                                               array( 'result' => $match_result ),
                                               array( 'event_settings_id' => $GLOBALS[ 'event_settings_id' ],
                                                      'round' => $round_id ),
                                               array( '%s' ),
                                               array( '%d', '%d' ) );

                                // Check for error
                                if ( $wpdb->last_error ) {
                                    $error = 1;
                                } else {
                                    // Get fixture_id
                                    $fixture_id = $row[ 0 ]->id;
                                }

                            }

                        }

                        // Boards for current round
                        for ( $k = 1; $k <= $boards_per_round; $k++ ) {

                            // Find and set board list
                            // Home player
                            $home_player_name = $html->find( '.CRs1', 0 )->childNodes( $j + $k )->childNodes( 2 )->plaintext;
                            $home_player_name = chsc_check_no_player( $home_player_name );
                            $home_player_name = chsc_unicode_to_utf8( $home_player_name );
                            $home_player_elo = $html->find( '.CRs1', 0 )->childNodes( $j + $k )->childNodes( 3 )->plaintext;

                            // Away player
                            // Check specific column
                            if ( $team_away_delimiter == '-' ) {
                                $away_player_name = $html->find( '.CRs1', 0 )->childNodes( $j + $k )->childNodes( 6 )->plaintext;
                                $away_player_elo = $html->find( '.CRs1', 0 )->childNodes( $j + $k )->childNodes( 7 )->plaintext;
                            } else if ( $team_away_delimiter == '' ) {
                                $away_player_name = $html->find( '.CRs1', 0 )->childNodes( $j + $k )->childNodes( 7 )->plaintext;
                                $away_player_elo = $html->find( '.CRs1', 0 )->childNodes( $j + $k )->childNodes( 8 )->plaintext;
                            }

                            $away_player_name = chsc_check_no_player( $away_player_name );
                            $away_player_name = chsc_unicode_to_utf8( $away_player_name );

                            // Board result
                            // Check specific column
                            if ( $team_away_delimiter == '-' ) {
                                $board_result = $html->find( '.CRs1', 0 )->childNodes( $j + $k )->childNodes( 8 )->plaintext;
                            } else if ( $team_away_delimiter == '' ) {
                                $board_result = $html->find( '.CRs1', 0 )->childNodes( $j + $k )->childNodes( 10 )->plaintext;
                            }

                            $board_result = chsc_check_result( $board_result, 'board' );

                            // Find event_settings_id and round
                            $wpdb->get_row(
                                $wpdb->prepare( "SELECT id
                                                FROM {$wpdb->prefix}chess_scraper_event_fixtures_boards
                                                WHERE fixture_id = %d AND
                                                    home_player_name = '%s' AND
                                                    away_player_name = '%s'",
                                                $fixture_id, $home_player_name, $away_player_name ) );

                            // If MySQL error
                            if ( $wpdb->last_error ) {
                                echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
                            } else {

                                // If row doesn't exist
                                if ( !$wpdb->num_rows ) {
                                    // Insert event fixture data
                                    $wpdb->insert( $wpdb->prefix . 'chess_scraper_event_fixtures_boards',
                                                   array( 'fixture_id' => $fixture_id,
                                                          'home_player_name' => $home_player_name,
                                                          'home_player_elo' => $home_player_elo,
                                                          'away_player_name' => $away_player_name,
                                                          'away_player_elo' => $away_player_elo,
                                                          'result' => $board_result ),
                                                   array( '%d', '%s', '%d', '%s', '%d', '%s' ) );

                                    // Check for error
                                    if ( $wpdb->last_error ) {
                                        $error = 1;
                                    }
                                }

                            }

                        }

                        // Break loop
                        // We scraped what we needed
                        break;

                    }

                }

            }

        } else {
            // HTML content is empty
            echo '<span class="tsf-message error">Error: HTML content for team roster is empty. It is probably traffic problem or IP is blocked. Try again.</span>';
        }

    }

    // Display message
    if ( isset( $error ) ) {
        echo '<span class="tsf-message error">Error: Some of the fixtures data wasn\'t successfully uploaded. Try again.</span>';
    } else {
        echo '<span class="tsf-message success">Fixtures data was successfully uploaded.</span>';
    }

}

// Clean up resources
chsc_clear_resources( $html );