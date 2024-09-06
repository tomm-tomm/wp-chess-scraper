<?php
/**
 * 1. Scrapes basic players' data from chess.sk club roster and uploads it into WP plugin db table.
 * 2. Scrapes additional players' data from fide.ratings.com player's profile page and uploads it into WP plugin db table.
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

// Set URL
$url = chsc_set_team_url();

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

        // Initialize var for the list of active players' FIDE IDs
        $active_players = '';

        // Count active players
        $row_count = substr_count( $html, 'mtkPlayers_list_src' );

        // For each player
        for ( $i = 1; $i <= $row_count; $i++ ) {

            // Scrape values
            $fide_id = $html->find( "#table_mtkPlayers_list", 0 )->childNodes( $i )->childNodes( 3 )->plaintext;
            $name = $html->find( "#table_mtkPlayers_list", 0 )->childNodes( $i )->childNodes( 1 )->plaintext;

            // Sanitize values
            filter_input( INPUT_GET, $fide_id, FILTER_SANITIZE_NUMBER_INT );
            filter_input( INPUT_GET, $name, FILTER_UNSAFE_RAW );

            // Push player's FIDE ID into active players' list
            if ( $active_players != '' ) {
                $active_players .= ', ' . $fide_id;
            } else {
                $active_players .= $fide_id;
            }

            // Find this player
            $row = $wpdb->get_row(
                       $wpdb->prepare( "SELECT id
                                        FROM {$wpdb->prefix}chess_scraper_team_roster
                                        WHERE club_settings_id = %d AND
                                              fide_id = %d",
                                        $GLOBALS[ 'club_settings_id' ], $fide_id ) );

            // Check for db error
            // Db error
            if ( $wpdb->last_error ) {
                $error = 1;
                echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
            } else {
                // No db error

                // If there is no row
                if ( !$wpdb->num_rows ) {

                    // Insert basic player's data
                    $wpdb->insert( $wpdb->prefix . 'chess_scraper_team_roster',
                    array( 'club_settings_id' => $GLOBALS[ 'club_settings_id' ],
                           'fide_id' => $fide_id ),
                    array( '%d', '%d' ) );

                    // Check for db error
                    // Db error
                    if ( $wpdb->last_error ) {
                        $insert_error = 1;
                        $error = 1;

                        echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
                    } else {
                        // No db error
                        $insert_error = 0;
                    }

                }

                // If there is a row
                if ( !isset( $insert_error ) ||
                     $insert_error == 0 ) {

                    // Scrape player's profile data from fide.ratings.com
                    // Set URL
                    $url2 = chsc_set_player_url( $fide_id );

                    // Get HTML content from URL
                    $html2 = chsc_get_html_content( $url2 );

                    // Get player's ELOs
                    $elo_standard = $html2->find( ".profile-top-rating-data_gray", 0 )->plaintext;
                    $elo_standard = filter_var( $elo_standard, FILTER_SANITIZE_NUMBER_INT );
                    $elo_standard = ( $elo_standard != '' ? $elo_standard : 0 );

                    $elo_rapid = $html2->find( ".profile-top-rating-data_red", 0 )->plaintext;
                    $elo_rapid = filter_var( $elo_rapid, FILTER_SANITIZE_NUMBER_INT );
                    $elo_rapid = ( $elo_rapid != '' ? $elo_rapid : 0 );

                    $elo_blitz = $html2->find( ".profile-top-rating-data_blue", 0 )->plaintext;
                    $elo_blitz = filter_var( $elo_blitz, FILTER_SANITIZE_NUMBER_INT );
                    $elo_blitz = ( $elo_blitz != '' ? $elo_blitz : 0 );

                    // Get active player's rankings
                    // If player has no ELO don't scrape rankings data
                    if ( $elo_standard == 0 && $elo_rapid == 0 && $elo_blitz == 0 ) {
                        $ranking_active_local = 0;
                        $ranking_active_eu = 0;
                        $ranking_active_world = 0;
                        $ranking_all_local = 0;
                        $ranking_all_eu = 0;
                        $ranking_all_world = 0;
                    } else {
                        $ranking_active_local = $html2->find( ".profile-tab-container", 0 )->childNodes( 1 )->childNodes( 1 )->getElementByTagName( "tbody" )->getElementByTagName( "tr" )->next_sibling()->getElementByTagName( "td" )->next_sibling()->plaintext;
                        $ranking_active_eu = $html2->find( ".profile-tab-container", 0 )->childNodes( 1 )->childNodes( 2 )->getElementByTagName( "tbody" )->getElementByTagName( "tr" )->next_sibling()->getElementByTagName( "td" )->next_sibling()->plaintext;
                        $ranking_active_world = $html2->find( ".profile-tab-container", 0 )->childNodes( 1 )->getElementByTagName( "tbody" )->getElementByTagName( "tr" )->next_sibling()->getElementByTagName( "td" )->next_sibling()->plaintext;
                        $ranking_all_local = $html2->find( ".profile-tab-container", 0 )->childNodes( 1 )->childNodes( 1 )->getElementByTagName( "tbody" )->getElementByTagName( "tr" )->getElementByTagName( "td" )->next_sibling()->plaintext;
                        $ranking_all_eu = $html2->find( ".profile-tab-container", 0 )->childNodes( 1 )->childNodes( 2 )->getElementByTagName( "tbody" )->getElementByTagName( "tr" )->getElementByTagName( "td" )->next_sibling()->plaintext;
                        $ranking_all_world = $html2->find( ".profile-tab-container", 0 )->childNodes( 1 )->getElementByTagName( "tbody" )->getElementByTagName( "tr" )->getElementByTagName( "td" )->next_sibling()->plaintext;
                    }

                    // Update additional player's data
                    $wpdb->update( $wpdb->prefix . 'chess_scraper_team_roster',
                                array( 'name' => $name,
                                       'elo_standard' => $elo_standard,
                                       'elo_rapid' => $elo_rapid,
                                       'elo_blitz' => $elo_blitz,
                                       'ranking_active_local' => $ranking_active_local,
                                       'ranking_active_eu' => $ranking_active_eu,
                                       'ranking_active_world' => $ranking_active_world,
                                       'ranking_all_local' => $ranking_all_local,
                                       'ranking_all_eu' => $ranking_all_eu,
                                       'ranking_all_world' => $ranking_all_world ),
                                array( 'fide_id' => $fide_id,
                                       'club_settings_id' => $GLOBALS[ 'club_settings_id' ] ),
                                array( '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d' ),
                                array( '%d', '%d' ) );

                    // Clean up resources
                    chsc_clear_resources( $html2 );

                }

            }

        }

        // Display message
        if ( isset( $error ) ) {
            echo '<span class="tsf-message error">Error: Some of the players\' data wasn\'t successfully uploaded. Try again.</span>';
        } else {
            echo '<span class="tsf-message success">Players\' data was successfully uploaded.</span>';
        }

        // Get current date and time
        $now = date( 'Y-m-d H:i:s' );

        // Update date of last change date in team settings
        $wpdb->update( $wpdb->prefix . 'chess_scraper_team_settings',
                       array( 'changed_at' => $now ),
                       array( 'id' => 1 ),
                       array( '%s' ),
                       array( '%d' ) );

        // Delete non-active players
        $wpdb->query(
            $wpdb->prepare( "DELETE FROM {$wpdb->prefix}chess_scraper_team_roster
                             WHERE fide_id
                             NOT IN ( " . $active_players . " )" ) );

        // Check for db error
        if ( $wpdb->last_error ) {
            // Error
            echo '<span class="tsf-message error">Error: Some of the non-active players wasn\'t deleted. Try again.</span>';
        }

    } else {
        // HTML content is empty
        // Error options: wrong club ID / traffic problem / IP is blocked
        echo '<span class="tsf-message error">Error: HTML content for club roster is empty.</span>';
    }

} else {
    // HTML content is empty
    echo '<span class="tsf-message error">Error: HTML content for team roster is empty. It is probably traffic problem or IP is blocked. Try again.</span>';
}

// Clean up resources
chsc_clear_resources( $html );