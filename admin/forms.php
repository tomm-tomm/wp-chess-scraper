<?php
/**
 * Admin form operations file.
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

// Form operations function
// Call after-submit file
// Call select queries, return input values
// Params:
// $db_table => db table name
// $db_cols => db table cols
function form_ops( $db_table, $db_cols ) {

    // Sanitize page var
    filter_input( INPUT_GET, $_GET[ 'page' ], FILTER_UNSAFE_RAW );
    $page = $_GET[ 'page' ];

    // Max number of clubs
    $max_clubs_no = 1;

    // Max number of league teams
    $max_league_teams_no = 3;

    // If form was submitted
    if ( isset( $_POST[ 'submit' ] ) ) {
        // Load required file with page logic
        require( PLUGIN_DIRECTORY . 'admin/forms-submit/' . $page . '.php' );
    }

    // Get data
    // Needed in /forms/xxx.php files
    switch ( $page ) {

        case "default":
            // This function works with db tables which have more than one row
            $query_values = chsc_admin_form_select_query_more_rows( $db_table, $db_cols, $max_clubs_no );
            break;
        case "league":
            // This function works with db tables which have more than one row
            $query_values = chsc_admin_form_select_query_more_rows( $db_table, $db_cols, $max_league_teams_no );
            break;
        case "updates":
            // This function works with db tables which have one row
            $query_values = chsc_admin_form_select_query_single( $db_table, $db_cols );
            break;

    }

    return $query_values;

}