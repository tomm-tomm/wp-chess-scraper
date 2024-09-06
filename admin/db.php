<?php
/**
 * Admin form DB operations.
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

// Do select query in table with single row
// Params:
// $db_table => db table name
// $db_cols => db table cols
function chsc_admin_form_select_query_single( $db_table, $db_cols ) {

    // Set columns for select query
    foreach ( $db_cols as $key => $value ) {

        if ( isset( $db_select_cols ) ) {
            $db_select_cols .= ', ' . $key;
        } else {
            $db_select_cols = $key;
        }

    }

    // Initalize WP database functions
    global $wpdb;

    // Do select query
    $row = $wpdb->get_row( "SELECT $db_select_cols
                            FROM $db_table
                            WHERE id = 1" );

    // If MySQL error
    if ( $wpdb->last_error ) {
        echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
    } else {
        // If MySQL success

        // Set values
        foreach ( $db_cols as $key => $value ) {

            // Initialize array for query values
            $query_values[ $key ] = array();

            // Set same variable name as a table column name
            ${ $key } = $row->$key;

            array_push( $query_values[ $key ], ${ $key } );

        }

    }

    return $query_values;

}

// Do update query in table with single row
// Params:
// $db_table => db table name
// $db_cols => db table cols
function chsc_admin_form_update_query_single( $db_table, $db_cols ) {

    // Initialize arrays for columns and values/formats
    $db_update_colvals = array();
    $db_update_formats = array();

    foreach ( $db_cols as $key => $value ) {

        // Set columns and values for update query
        // Exception for non-POST vars
        if ( $key == 'changed_at' ) {
            $col_val = date( 'Y-m-d H:i:s' );
        } else {
            // For POST vars
            $col_val = stripslashes( $_POST[ $key ] );
        }

        $db_update_colvals[ $key ] = $col_val;

        // Set value formats for update query
        array_push( $db_update_formats, '%' . $value );

    }

    // Initalize WP database functions
    global $wpdb;

    // Update data
    // NOTE:
    // WHERE id = 1 => there will always be just one row in each table
    $wpdb->update( $db_table,
                   $db_update_colvals,
                   array( 'id' => 1 ),
                   $db_update_formats );

    // Display information
    if ( $wpdb->last_error ) {
        // Error
        echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
    } else {
        // Success
        echo '<span class="tsf-message success">Data were successfully updated.</span>';
    }

}

// Do select query in table with more rows
// Params:
// $db_table => db table name
// $db_cols => db table cols
// $j => table row id
function chsc_admin_form_select_query_more_rows( $db_table, $db_cols, $j ) {

    // Set columns for select query
    foreach ( $db_cols as $key => $value ) {

        if ( isset( $db_select_cols ) ) {
            $db_select_cols .= ', ' . $key;
        } else {
            $db_select_cols = $key;
        }

    }

    // Initalize WP database functions
    global $wpdb;

    // For each row id
    for ( $i = 1; $i <= $j; $i++ ) {

        // Do select query
        $row = $wpdb->get_row( "SELECT $db_select_cols
                                FROM $db_table
                                WHERE id = $i" );

        // If MySQL error
        if ( $wpdb->last_error ) {
            echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
        } else {
            // If MySQL success

            // Set values
            foreach ( $db_cols as $key => $value ) {

                // Initialize array for query values
                $query_values[ $key . '_' . $i ] = array();

                // If row id exists
                if ( $wpdb->num_rows ) {
                    // Set same variable name as a table column name
                    ${ $key } = $row->$key;
                } else {
                    // If row id doesn't exist
                    ${ $key } = '';
                }

                array_push( $query_values[ $key . '_' . $i ], ${ $key } );

            }

        }

    }

    return $query_values;

}

// Do update query in table with more rows
// Params:
// $db_table => db table name
// $db_cols => db table cols
// $i => table row id
function chsc_admin_form_update_query_more_rows( $db_table, $db_cols, $i ) {

    // Set main vars
    // Club
    if ( isset( $_POST[ 'team_id_' . $i ] ) ) {
        $var1_name = 'team_id';
        $var1_value = $_POST[ 'team_id_' . $i ];
        $var2_name = 'federation_id';
        $var2_value = $_POST[ 'federation_id_' . $i ];
    } else if ( isset( $_POST[ 'event_id_' . $i ] ) ) {
        // League
        $var1_name = 'event_id';
        $var1_value = $_POST[ 'event_id_' . $i ];
        $var2_name = 'team_name';
        $var2_value = $_POST[ 'team_name_' . $i ];
    }

    // Sanitize posted values
    filter_input( INPUT_GET, $var1_value, FILTER_SANITIZE_NUMBER_INT );
    filter_input( INPUT_GET, $var2_value, FILTER_UNSAFE_RAW );

    // Set zero for var1 value, otherwise there will be MySQL error
    $var1_value = $var1_value == '' ? 0 : $var1_value;

    // Initalize array for posted values
    $posted_values = array();

    // Initalize WP database functions
    global $wpdb;

    // Check if row with sent values exists
    $wpdb->get_row( "SELECT id
                     FROM $db_table
                     WHERE $var1_name = $var1_value AND $var1_name != NULL AND
                           $var2_name = '$var2_value'  AND $var2_name != NULL AND
                           id != $i" );

    // If MySQL error
    if ( $wpdb->last_error ) {
        echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
    } else {

        // If row doesn't exist
        if ( !$wpdb->num_rows ) {

            // Check if row with sent id exists
            $row = $wpdb->get_row( "SELECT $var1_name, $var2_name
                                    FROM $db_table
                                    WHERE id = $i" );

            // If MySQL error
            if ( $wpdb->last_error ) {
                echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
            } else {

                // If row exists
                // Update data
                if ( $wpdb->num_rows ) {

                    // If sent ids are not already in db
                    ${ "db_$var1_name" } = $row->{ $var1_name };
                    ${ "db_$var2_name" } = $row->{ $var2_name };

                    if ( !( $var1_value == ${ "db_$var1_name" } &&
                            $var2_value == ${ "db_$var2_name" } ) ) {

                        // Initialize arrays for columns and values/formats
                        $db_update_colvals = array();
                        $db_update_formats = array();

                        foreach ( $db_cols as $key => $value ) {

                            // Set columns and values for update query
                            // Exception for non-POST vars
                            if ( $key == 'changed_at' ) {
                                $col_val = date( 'Y-m-d H:i:s' );
                            } else {
                                // For POST vars
                                $col_val = stripslashes( $_POST[ $key . '_' . $i ] );
                            }

                            $db_update_colvals[ $key ] = $col_val;

                            // Set value formats for update query
                            array_push( $db_update_formats, '%' . $value );

                        }

                        // If values are null
                        if ( $var1_value == 0 &&
                             $var2_value == '' ) {

                            // Delete empty row
                            $wpdb->delete( $db_table,
                                           array( 'id' => $i ),
                                           array( '%d', '%d' ) );

                            // Display information
                            if ( $wpdb->last_error ) {
                                // Error
                                echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
                            } else {
                                // Success
                                echo '<span class="tsf-message success">Data #' . $i . ' were successfully deleted.</span>';

                            }

                        } else {

                            // Update data
                            $wpdb->update( $db_table,
                                           $db_update_colvals,
                                           array( 'id' => $i ),
                                           $db_update_formats );

                            // Display information
                            if ( $wpdb->last_error ) {
                                // Error
                                echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
                            } else {
                                // Success
                                // Requested post data control
                                if ( $var1_value != 0 &&
                                     $var2_value != '' ) {
                                    echo '<span class="tsf-message success">Data #' . $i . ' are complete.</span>';
                                } else {
                                    echo '<span class="tsf-message warning">Data #' . $i . ' are not complete.</span>';
                                }

                            }

                        }

                    }

                } else {
                    // If row doesn't exist
                    // Insert data

                    // And if team and event ids both are not null
                    if ( !( $var1_value == 0 &&
                            $var2_value == '' ) ) {

                        // Initialize arrays for columns and values/formats
                        $db_insert_colvals = array();
                        $db_insert_formats = array();

                        // Set id and insert date col/value/formats for update query
                        $db_insert_colvals[ 'id' ] = $i;
                        $db_insert_colvals[ 'created_at' ] = date( 'Y-m-d H:i:s' );

                        array_push( $db_insert_formats, '%d', '%s' );

                        // Set columns for insert query
                        foreach ( $db_cols as $key => $value ) {

                            // Set columns and values for update query
                            // There is none date of update when inserting new row
                            if ( $key != 'changed_at' ) {
                                $col_val = stripslashes( $_POST[ $key . '_' . $i ] );
                            }

                            $db_insert_colvals[ $key ] = $col_val;

                            // Set value formats for update query
                            array_push( $db_insert_formats, '%' . $value );

                        }

                        // Insert data
                        $wpdb->insert( $db_table,
                                       $db_insert_colvals,
                                       $db_insert_formats );

                        // Display information
                        if ( $wpdb->last_error ) {
                            // Error
                            echo '<span class="tsf-message error">Error: ' . $wpdb->last_error . '</span>';
                        } else {
                            // Success
                            if ( ( $var1_value == 0 ||
                                   $var2_value == '' ) ) {
                                echo '<span class="tsf-message success">Data for team and event #' . $i . ' were successfully updated but they are not complete.</span>';
                            } else {
                                echo '<span class="tsf-message success">Data for team and event #' . $i . ' were successfully updated.</span>';
                            }
                        }

                    }

                }

            }

        } else {
            echo '<span class="tsf-message error">Error: Team and event #' . $i . ' are in database already.</span>';
        }

    }

}