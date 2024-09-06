<?php
/**
 * Create database tables after activating the plugin.
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

// Create db tables required by plugin
function chsc_create_db_tables() {

    // Array with list of db tables
    // Param #1: Table name
    // Param #2: Add single row in the table after creation (value: 1)
    // NOTE:
    // Some tables needs just one row
    $table_names_array = array(
        array( 'chess_scraper_team_settings', 0 ),
        array( 'chess_scraper_team_roster', 0 ),
        array( 'chess_scraper_event_settings', 0 ),
        array( 'chess_scraper_event_roster', 0 ),
        array( 'chess_scraper_event_schedule', 0 ),
        array( 'chess_scraper_event_ranking', 0 ),
        array( 'chess_scraper_event_fixtures', 0 ),
        array( 'chess_scraper_event_fixtures_boards', 0 ),
        array( 'chess_scraper_updates_allowed_ips', 1 ) );

    // Array with table columns
    $table_columns_array = array(
        /* team_settings */
        'id int(3) UNSIGNED NOT NULL AUTO_INCREMENT,
         team_id int(7) UNSIGNED NOT NULL,
         federation_id varchar(3) NOT NULL,
         created_at datetime NOT NULL,
         changed_at datetime NOT NULL,
         PRIMARY KEY  (id)',
         /* team_roster */
        'id int(4) UNSIGNED NOT NULL AUTO_INCREMENT,
         club_settings_id int(3) UNSIGNED NOT NULL,
         fide_id int(12) UNSIGNED NOT NULL,
         name varchar(64) NOT NULL,
         elo_standard int(4) UNSIGNED NOT NULL,
         elo_rapid int(4) UNSIGNED NOT NULL,
         elo_blitz int(4) UNSIGNED NOT NULL,
         ranking_active_local int(8) UNSIGNED NOT NULL,
         ranking_active_eu int(8) UNSIGNED NOT NULL,
         ranking_active_world int(8) UNSIGNED NOT NULL,
         ranking_all_local int(8) UNSIGNED NOT NULL,
         ranking_all_eu int(8) UNSIGNED NOT NULL,
         ranking_all_world int(8) UNSIGNED NOT NULL,
         PRIMARY KEY  (id),
         KEY club_settings_id (club_settings_id),
         KEY fide_id (fide_id)',
        /* event_settings */
        'id int(4) UNSIGNED NOT NULL AUTO_INCREMENT,
         event_id int(10) UNSIGNED NOT NULL,
         event_name varchar(128) NOT NULL,
         team_name varchar(128) NOT NULL,
         created_at datetime NOT NULL,
         changed_at datetime NOT NULL,
         PRIMARY KEY  (id)',
         /* event_roster */
        'id int(4) UNSIGNED NOT NULL AUTO_INCREMENT,
         event_settings_id int(3) UNSIGNED NOT NULL,
         fide_id int(12) UNSIGNED NOT NULL,
         name varchar(64) NOT NULL,
         order_no int(4) UNSIGNED NOT NULL,
         elo int(4) UNSIGNED NOT NULL,
         games int(2) UNSIGNED NOT NULL,
         points int(3) UNSIGNED NOT NULL,
         PRIMARY KEY  (id),
         KEY event_settings_id (event_settings_id)',
         /* event_schedule */
        'id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
         event_settings_id int(3) UNSIGNED NOT NULL,
         round int(2) UNSIGNED NOT NULL,
         date datetime NOT NULL,
         PRIMARY KEY  (id),
         KEY event_settings_id (event_settings_id)',
         /* event_ranking */
        'id int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
         event_settings_id int(3) UNSIGNED NOT NULL,
         team varchar(128) NOT NULL,
         ranking int(2) UNSIGNED NOT NULL,
         games int(2) UNSIGNED NOT NULL,
         wins int(2) UNSIGNED NOT NULL,
         draws int(2) UNSIGNED NOT NULL,
         losses int(2) UNSIGNED NOT NULL,
         tb1 float(3,1) NOT NULL,
         tb2 float(3,1) NOT NULL,
         tb3 float(3,1) NOT NULL,
         PRIMARY KEY  (id),
         KEY event_settings_id (event_settings_id)',
         /* event_fixtures */
        'id int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
         event_settings_id int(3) UNSIGNED NOT NULL,
         round int(2) NOT NULL,
         team_home varchar(128) NOT NULL,
         team_away varchar(128) NOT NULL,
         result varchar(32) NOT NULL,
         PRIMARY KEY  (id),
         KEY event_settings_id (event_settings_id)',
         /* event_fixtures_board */
        'id int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
         fixture_id int(10) UNSIGNED NOT NULL,
         home_player_name varchar(64) NOT NULL,
         home_player_elo int(4) NOT NULL,
         away_player_name varchar(64) NOT NULL,
         away_player_elo int(4) NOT NULL,
         result varchar(32) NOT NULL,
         PRIMARY KEY  (id),
         KEY fixture_id (fixture_id)',
        /* updates_allowed_ips */
        'id int(1) UNSIGNED NOT NULL AUTO_INCREMENT,
         ip varchar(39) NOT NULL,
         created_at datetime NOT NULL,
         changed_at datetime NOT NULL,
         PRIMARY KEY  (id)' );

    // Initialize WP database functions
    global $wpdb;

    // Load WP built-in dbDelta() function
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Create tables
    // Set charset
    $charset = 'utf8';

    // Table counter
    $tables_count = count( $table_names_array );

    // For each table do create table query
    for ( $i = 0; $i < $tables_count; $i++ ) {

        // Get names from arrays above
        $table_name = $wpdb->prefix . $table_names_array[ $i ][ 0 ];
        $table_column = $table_columns_array[ $i ];

        // Create table query
        $sql = "
        CREATE TABLE IF NOT EXISTS $table_name (
        $table_column
        ) DEFAULT CHARSET = $charset;";

        // Execute create table query
        dbDelta( $sql );

        // Insert single row for some tables
        if ( $table_names_array[ $i ][ 1 ] == 1 ) {

            $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO $table_name
                                ( id, created_at )
                     VALUES ( %d, %s )",
                            1,
                            date( 'Y-m-d H:i:s' )
                )
            );

        }

    }

}

// Call action after activating the plugin
add_action( 'plugin_loaded', 'chsc_create_db_tables' );


// Delete db staff after plugin deactivation
function chsc_drop_db_tables() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'chess_scraper_team_settings';
    $sql = "DROP TABLE IF EXISTS $table_name";

    $wpdb->query( $sql );

}

register_uninstall_hook( __FILE__, 'chsc_drop_db_tables' );