<?php
/**
 * @package Chess_Scraper
 * @version 1.0.0
 *
 * Functions for scraping the data from various websites.
 */

// Get HTML content from URL
function chsc_get_html_content( $url ) {

    // Include the Simple HTML DOM parser library
    include_once( 'tools/simple_html_dom.php' );

    // Initialize a cURL session
    $curl = curl_init();

    // Set the website URL
    curl_setopt( $curl, CURLOPT_URL, $url );

    // Return the response as a string
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

    // Follow redirects
    curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );

    // Ignore SSL verification (not recommended in production)
    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );

    // Execute the cURL session
    $htmlContent = curl_exec( $curl );

    // Check for errors
    if ( $htmlContent === false ) {

        // Handle the error
        $error = curl_error( $curl );
        echo 'cURL error: ' . $error;

        exit;

    } else {

        // No error
        // Close cURL session
        curl_close( $curl );

        // Create a new Simple HTML DOM instance and parse the HTML
        $html = str_get_html( $htmlContent );

        return $html;

    }

}

// Clean up resources
function chsc_clear_resources( $html ) {

    $html->clear();

}

// Set the target website's URL for player data
// Source: fide.com
function chsc_set_player_url( $fide_id ) {

    $url = 'https://ratings.fide.com/profile/' . $fide_id;

    return $url;

}

// Set the target website's URL for event (league/tournament) data
// Source: chess-results.com
function chsc_set_event_url( $event_id, $section_id, $round_id = NULL ) {

    if ( isset( $round_id ) ) {
        $round_url = '&rd=' . $round_id;
    } else {
        $round_url = '';
    }

    $url = 'https://chess-results.com/tnr' . $event_id . '.aspx?lan=1&art=' . $section_id . $round_url;

    return $url;

}

// Set URL for players list
// Source: Local federations
function chsc_set_team_url() {

    switch ( $GLOBALS[ 'federation_id' ] ) {

        case 'svk':
            $url = 'https://chess.sk/index.php?str=clenovia&klubId=' . $GLOBALS[ 'team_id' ];
            break;
        default:
            $url = '';

    }

    return $url;

}

// Set the missing player name format
function chsc_check_no_player( $name ) {

    if ( $name == 'no player' ) {
        $name = 'bez súpera';
    }

    return $name;

}

// Set the format of players result when one opponent was missing
function chsc_check_result( $result, $type = NULL ) {

    // Replace half point code
    $result = str_replace( '&frac12;', ',5', $result );
    $result = str_replace( ' ,5', '0,5', $result );

    if ( substr( $result, 0, 2 ) == ',5' ) {
        $result = '0' . $result;
    }

    // Edit spaces
    if ( !str_contains( $result, ' :' ) ) {
        $result = str_replace( ':', ' :', $result );
    }

    if ( !str_contains( $result, ': ' ) ) {
        $result = str_replace( ':', ': ', $result );
    }

    if ( !str_contains( $result, ' -' ) ) {
        $result = str_replace( '-', ' -', $result );
    }

    if ( !str_contains( $result, '- ' ) ) {
        $result = str_replace( '-', '- ', $result );
    }

    // For board result only
    if ( $type == 'board' ) {
        // Edit special results
        if ( $result == '+ - -' ) {
            $result = '1 - 0';
        } else if ( $result == '- - +' ) {
            $result = '0 - 1';
        }
    }

    return $result;

}

// Convert Unicode characters in string to UTF-8
function chsc_unicode_to_utf8( $string ) {

    $string = html_entity_decode( preg_replace( "/U\+([0-9A-F]{4})/", "&#x\\1;", $string ), ENT_NOQUOTES, 'UTF-8' );

    return $string;

}