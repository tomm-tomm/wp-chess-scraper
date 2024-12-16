<?php
/**
 * Display imported data by section and event id.
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

// Associate shortcode name with function
add_shortcode( 'chess-scraper', 'chsc_show_data' );

// Main function
// Display data by section and event id set in shortcode
function chsc_show_data( $atts ) {

    extract( shortcode_atts( array(
        'section' => 'Insert section name',
        'settings_id' => 'Insert settings id'
    ), $atts ) );

    // Sanitize input vars
    filter_input( INPUT_GET, $section, FILTER_UNSAFE_RAW );
    filter_input( INPUT_GET, $settings_id, FILTER_SANITIZE_NUMBER_INT );

    // Initalize WP database functions
    global $wpdb;

    // Initialize content variable
    $html = '';

    /***** Club roster *****/
    // NOTE: Doesn't work because chess.sk changed HTML structure

    /*if ( $section == 'club-roster' ) {

        // Show non-default subpage
        if ( isset( $_GET[ 'action' ] ) ) {

            /*
             * Player profile
             *//*
            if ( $_GET[ 'action' ] == 'profile' ) {

                // Select player data
                $row = $wpdb->get_row(
                           $wpdb->prepare( "SELECT *
                                            FROM {$wpdb->prefix}chess_scraper_team_roster
                                            WHERE fide_id = %d",
                                            stripslashes( $_GET[ 'fide_id' ] ) ) );

                // If MySQL error
                if ( $wpdb->last_error ) {
                    $html .= '<div class="cs-message error">' . $wpdb->last_error . '</div>';
                } else {

                        // If MySQL success
                        // If there are rows
                        if ( $wpdb->num_rows ) {

                            // Set vars
                            $name = $row->name;

                            $elo_standard = $row->elo_standard;
                            $elo_standard = ( $elo_standard ? $elo_standard : '-' );

                            $elo_rapid = $row->elo_rapid;
                            $elo_rapid = ( $elo_rapid > 0 ? $elo_rapid : '-' );

                            $elo_blitz = $row->elo_blitz;
                            $elo_blitz = ( $elo_blitz > 0 ? $elo_blitz : '-' );

                            $ranking_active_local = $row->ranking_active_local;
                            $ranking_active_local = ( $ranking_active_local > 0 ? $ranking_active_local : '-' );

                            $ranking_active_eu = $row->ranking_active_eu;
                            $ranking_active_eu = ( $ranking_active_eu > 0 ? $ranking_active_eu : '-' );

                            $ranking_active_world = $row->ranking_active_world;
                            $ranking_active_world = ( $ranking_active_world > 0 ? $ranking_active_world : '-' );

                            $ranking_all_local = $row->ranking_all_local;
                            $ranking_all_local = ( $ranking_all_local > 0 ? $ranking_all_local : '-' );

                            $ranking_all_eu = $row->ranking_all_eu;
                            $ranking_all_eu = ( $ranking_all_eu > 0 ? $ranking_all_eu : '-' );

                            $ranking_all_world = $row->ranking_all_world;
                            $ranking_all_world = ( $ranking_all_world > 0 ? $ranking_all_world : '-' );

                            // Display profile
                            $html .=
                            '<h2 class="cs-title">' . $name . '</h2>

                             <h3 class="cs-title">
                                ELO
                             </h3>
                             <div class="cs-wrapper player-profile">
                                <div class="cs-table">
                                    <div class="cs-row cs-header">
                                        <div class="cs-cell ">Standard</div>
                                        <div class="cs-cell">Rapid</div>
                                        <div class="cs-cell">Blitz</div>
                                    </div>
                                    <div class="cs-row">
                                        <div class="cs-cell">' . $elo_standard . '</div>
                                        <div class="cs-cell">' . $elo_rapid . '</div>
                                        <div class="cs-cell">' . $elo_blitz . '</div>
                                    </div>
                                </div>
                             </div>

                             <h3 class="cs-title">Ranking</h3>
                             <h4 class="cs-title">Active</h4>
                             <div class="cs-wrapper player-profile">
                                <div class="cs-table">
                                    <div class="cs-row cs-header">
                                        <div class="cs-cell">Local</div>
                                        <div class="cs-cell">EU</div>
                                        <div class="cs-cell">World</div>
                                    </div>
                                    <div class="cs-row">
                                        <div class="cs-cell">' . $ranking_active_local . '</div>
                                        <div class="cs-cell">' . $ranking_active_eu . '</div>
                                        <div class="cs-cell">' . $ranking_active_world . '</div>
                                    </div>
                                </div>
                             </div>

                             <h4 class="cs-title">All</h4>
                             <div class="cs-wrapper player-profile">
                                <div class="cs-table">
                                    <div class="cs-row cs-header">
                                        <div class="cs-cell">Local</div>
                                        <div class="cs-cell">EU</div>
                                        <div class="cs-cell">World</div>
                                    </div>
                                    <div class="cs-row">
                                        <div class="cs-cell">' . $ranking_all_local . '</div>
                                        <div class="cs-cell">' . $ranking_all_eu . '</div>
                                        <div class="cs-cell">' . $ranking_all_world . '</div>
                                    </div>
                                </div>
                             </div>';

                        } else {

                            // If there are no rows
                            $html .= '<div class="cs-message error">Player wasn\'t found.</div>';

                        }

                }

            }

        } else {
            // Show default subpage

            /*
             * Team roster
             *//*

             // Table header
            $html .=
            '<div class="cs-wrapper club-roster">
                <div class="cs-table">
                    <div class="cs-row cs-header">
                        <div class="cs-cell">#</div>
                        <div class="cs-cell">Name</div>
                        <div class="cs-cell">ELO</div>
                    </div>';

            // Initalize row number
            $i = 0;

            // Select players data
            foreach ( $wpdb->get_results( "SELECT fide_id, name, elo_standard
                                           FROM {$wpdb->prefix}chess_scraper_team_roster
                                           WHERE club_settings_id = $settings_id" ) as $key => $row ) {

                // Increments row number
                $i++;

                // If MySQL error
                if ( $wpdb->last_error ) {
                    $html .= '<div class="cs-message error">' . $wpdb->last_error . '</div>';
                } else {

                    // If MySQL success
                    // If there are rows
                    if ( $wpdb->num_rows ) {

                        // Set values
                        $fide_id = $row->fide_id;
                        $name = $row->name;
                        $elo_standard = $row->elo_standard;

                        // Display data
                        $html .= '
                        <div class="cs-row">
                            <div class="cs-cell">' . $i . '</div>
                            <div class="cs-cell"><a href="https://ratings.fide.com/profile/' . $fide_id . '" target="_blank">' . $name . '</a></div>
                            <div class="cs-cell">' . $elo_standard . '</div>
                        </div>';

                        // NOTE:
                        // This is backup for link related with profile subpage
                        // Changed because profile subpage gets data from chess.sk website and it doesn't work yet because of changed site structure
                        // <div class="cs-cell"><a href="?action=profile&amp;fide_id=' . $fide_id . '">' . $name . '</a></div>

                    } else {
                        // No rows found
                        $html .= '
                        <div class="cs-message error">Error: No data uploaded. Upload it first.</div>';
                    }

                }

            }

            $html .= '
                </div>
            </div>';

        }

            // Source URL
            $html .= '
            <div class="cs-data-info">Údaje sú získavané z webu <a href="https://ratings.fide.com" target="_blank">ratings.fide.com</a>.</div>';

            // NOTE:
            // This is backup for link related with profile subpage
            // Changed because profile subpage gets data from chess.sk website and it doesn't work yet because of changed site structure
            // <div class="cs-data-info">Údaje sú získavané z webov <a href="https://chess.sk" target="_blank">chess.sk</a> a <a href="https://ratings.fide.com" target="_blank">ratings.fide.com</a>.</div>';

    } else*/ if ( $section == 'league-roster' ) {
        /***** League roster *****/

        // Show non-default subpage
        // NOTE: Doesn't work because chess.sk changed HTML structure
        /*if ( isset( $_GET[ 'action' ] ) ) {

            /*
             * Player profile
             *//*
            if ( $_GET[ 'action' ] == 'profile' ) {

                // Select player data
                $row = $wpdb->get_row(
                           $wpdb->prepare( "SELECT *
                                            FROM {$wpdb->prefix}chess_scraper_team_roster
                                            WHERE fide_id = %d",
                                            stripslashes( $_GET[ 'fide_id' ] ) ) );

                // If MySQL error
                if ( $wpdb->last_error ) {
                    $html .= '<div class="cs-message error">' . $wpdb->last_error . '</div>';
                } else {

                        // If MySQL success
                        // If there are rows
                        if ( $wpdb->num_rows ) {

                            // Set vars
                            $name = $row->name;

                            $elo_standard = $row->elo_standard;
                            $elo_standard = ( $elo_standard ? $elo_standard : '-' );

                            $elo_rapid = $row->elo_rapid;
                            $elo_rapid = ( $elo_rapid > 0 ? $elo_rapid : '-' );

                            $elo_blitz = $row->elo_blitz;
                            $elo_blitz = ( $elo_blitz > 0 ? $elo_blitz : '-' );

                            $ranking_active_local = $row->ranking_active_local;
                            $ranking_active_local = ( $ranking_active_local > 0 ? $ranking_active_local : '-' );

                            $ranking_active_eu = $row->ranking_active_eu;
                            $ranking_active_eu = ( $ranking_active_eu > 0 ? $ranking_active_eu : '-' );

                            $ranking_active_world = $row->ranking_active_world;
                            $ranking_active_world = ( $ranking_active_world > 0 ? $ranking_active_world : '-' );

                            $ranking_all_local = $row->ranking_all_local;
                            $ranking_all_local = ( $ranking_all_local > 0 ? $ranking_all_local : '-' );

                            $ranking_all_eu = $row->ranking_all_eu;
                            $ranking_all_eu = ( $ranking_all_eu > 0 ? $ranking_all_eu : '-' );

                            $ranking_all_world = $row->ranking_all_world;
                            $ranking_all_world = ( $ranking_all_world > 0 ? $ranking_all_world : '-' );

                            // Display profile
                            $html .=
                            '<h2 class="cs-title">' . $name . '</h2>

                             <h3 class="cs-title">
                                ELO
                             </h3>
                             <div class="cs-wrapper player-profile">
                                <div class="cs-table">
                                    <div class="cs-row cs-header">
                                        <div class="cs-cell ">Standard</div>
                                        <div class="cs-cell">Rapid</div>
                                        <div class="cs-cell">Blitz</div>
                                    </div>
                                    <div class="cs-row">
                                        <div class="cs-cell">' . $elo_standard . '</div>
                                        <div class="cs-cell">' . $elo_rapid . '</div>
                                        <div class="cs-cell">' . $elo_blitz . '</div>
                                    </div>
                                </div>
                             </div>

                             <h3 class="cs-title">Ranking</h3>
                             <h4 class="cs-title">Active</h4>
                             <div class="cs-wrapper player-profile">
                                <div class="cs-table">
                                    <div class="cs-row cs-header">
                                        <div class="cs-cell">Local</div>
                                        <div class="cs-cell">EU</div>
                                        <div class="cs-cell">World</div>
                                    </div>
                                    <div class="cs-row">
                                        <div class="cs-cell">' . $ranking_active_local . '</div>
                                        <div class="cs-cell">' . $ranking_active_eu . '</div>
                                        <div class="cs-cell">' . $ranking_active_world . '</div>
                                    </div>
                                </div>
                             </div>

                             <h4 class="cs-title">All</h4>
                             <div class="cs-wrapper player-profile">
                                <div class="cs-table">
                                    <div class="cs-row cs-header">
                                        <div class="cs-cell">Local</div>
                                        <div class="cs-cell">EU</div>
                                        <div class="cs-cell">World</div>
                                    </div>
                                    <div class="cs-row">
                                        <div class="cs-cell">' . $ranking_all_local . '</div>
                                        <div class="cs-cell">' . $ranking_all_eu . '</div>
                                        <div class="cs-cell">' . $ranking_all_world . '</div>
                                    </div>
                                </div>
                             </div>';

                        } else {

                            // If there are no rows
                            $html .= '<div class="cs-message error">Player wasn\'t found.</div>';

                        }

                }

                // Source URL
                $html .= '
                <div class="cs-data-info">Údaje sú získavané z webu <a href="https://ratings.fide.com" target="_blank">ratings.fide.com</a>.</div>';

                // NOTE:
                // This is backup for link related with profile subpage
                // Changed because profile subpage gets data from chess.sk website and it doesn't work yet because of changed site structure
                // <div class="cs-data-info">Údaje sú získavané z webov <a href="https://chess.sk" target="_blank">chess.sk</a> a <a href="https://ratings.fide.com" target="_blank">ratings.fide.com</a>.</div>';

            }

        } else {*/
            // Show default subpage

            /*
             * Team roster
             */

             // Table header
            $html .=
            '<div class="cs-wrapper league-roster">
                <div class="cs-table">
                    <div class="cs-row cs-header">
                        <div class="cs-cell">#</div>
                        <div class="cs-cell">Name</div>
                        <div class="cs-cell">ELO</div>
                        <div class="cs-cell">Games</div>
                        <div class="cs-cell">Points</div>
                    </div>';

            // Select players data
            foreach ( $wpdb->get_results( "SELECT fide_id, name, order_no, elo, games, points
                                           FROM {$wpdb->prefix}chess_scraper_event_roster
                                           WHERE event_settings_id = $settings_id
                                           ORDER BY order_no" ) as $key => $row ) {

                // If MySQL error
                if ( $wpdb->last_error ) {
                    $html .= '<div class="cs-message error">' . $wpdb->last_error . '</div>';
                } else {

                    // If MySQL success
                    // If there are rows
                    if ( $wpdb->num_rows ) {

                        // Set values
                        $fide_id = $row->fide_id;
                        $name = $row->name;
                        $order_no = $row->order_no;
                        $elo = $row->elo;
                        $games = $row->games;
                        $points = $points_fmtd = str_replace('.',',',$row->points );

                        // Display data
                        $html .= '
                        <div class="cs-row">
                            <div class="cs-cell">' . $order_no . '</div>
                            <div class="cs-cell"><a href="https://ratings.fide.com/profile/' . $fide_id . '" target="_blank">' . $name . '</a></div>
                            <div class="cs-cell">' . $elo . '</div>
                            <div class="cs-cell">' . $games . '</div>
                            <div class="cs-cell">' . $points . '</div>
                        </div>';

                        // NOTE:
                        // This is backup for link related with profile subpage
                        // Changed because profile subpage gets data from chess.sk website and it doesn't work yet because of changed site structure
                        // <div class="cs-cell"><a href="?action=profile&amp;fide_id=' . $fide_id . '">' . $name . '</a></div>

                    } else {
                        // No rows found
                        $html .= '
                        <div class="cs-message error">Error: No data uploaded. Upload it first.</div>';
                    }

                }

            }

            $html .= '
                </div>
            </div>';

        //}

    } else if ( $section == 'league-details' ) {
        /***** League ranking table and fixtures *****/

            /*
             * Ranking table
             */

             // Table header
            $html .=
            '<div class="cs-wrapper ranking-table">
                <div class="cs-table">
                    <div class="cs-row cs-header">
                        <div class="cs-cell">#</div>
                        <div class="cs-cell">Tím</div>
                        <div class="cs-cell">Kolá</div>
                        <div class="cs-cell">Výhry</div>
                        <div class="cs-cell">Remízy</div>
                        <div class="cs-cell">Prehry</div>
                        <div class="cs-cell">TB1</div>
                        <div class="cs-cell">TB2</div>
                        <div class="cs-cell">TB3</div>
                    </div>';

            // Get your team
            // For highligting the team in the ranking table
            $row = $wpdb->get_row( "SELECT team_name
                                    FROM {$wpdb->prefix}chess_scraper_event_settings
                                    WHERE id = $settings_id" );

                // If MySQL error
                if ( $wpdb->last_error ) {
                    $html .= '<div class="cs-message error">' . $wpdb->last_error . '</div>';
                } else {

                    // If MySQL success
                    // If there are rows
                    if ( $wpdb->num_rows ) {
                        $team_name = $row->team_name;
                    } else {
                        $team_name = '';
                    }

                }

            // Select team data
            foreach ( $wpdb->get_results( "SELECT ranking, team, games, wins, draws, losses, tb1, tb2, tb3
                                           FROM {$wpdb->prefix}chess_scraper_event_ranking
                                           WHERE event_settings_id = $settings_id
                                           ORDER BY ranking" ) as $key => $row ) {

                // If MySQL error
                if ( $wpdb->last_error ) {
                    $html .= '<div class="cs-message error">' . $wpdb->last_error . '</div>';
                } else {

                    // If MySQL success
                    // If there are rows
                    if ( $wpdb->num_rows ) {

                        // Set values
                        $ranking = $row->ranking;
                        $team = $row->team;
                        $games = $row->games;
                        $wins = $row->wins;
                        $draws = $row->draws;
                        $losses = $row->losses;
                        $tb1 = $row->tb1;
                        $tb2 = $row->tb2;
                        $tb3 = $row->tb3;

                        // Highlight your team
                        if ( $team_name == $team ) {
                            $highlight = ' bold';
                        } else {
                            $highlight = '';
                        }

                        // Display data
                        $html .= '
                        <div class="cs-row' . $highlight . '">
                            <div class="cs-cell">' . $ranking . '</div>
                            <div class="cs-cell">' . $team . '</div>
                            <div class="cs-cell">' . $games . '</div>
                            <div class="cs-cell">' . $wins . '</div>
                            <div class="cs-cell">' . $draws . '</div>
                            <div class="cs-cell">' . $losses . '</div>
                            <div class="cs-cell">' . $tb1 . '</div>
                            <div class="cs-cell">' . $tb2 . '</div>
                            <div class="cs-cell">' . $tb3 . '</div>
                        </div>';

                    } else {
                        // No rows found
                        $html .= '
                        <div class="cs-message error">Error: No data uploaded. Upload it first.</div>';
                    }

                }

            }

            $html .= '
                </div>
            </div>';

            /*
             * Fixtures
             */

            // Select schedule data
            foreach ( $wpdb->get_results( "SELECT round, date
                                           FROM {$wpdb->prefix}chess_scraper_event_schedule
                                           WHERE event_settings_id = $settings_id
                                           ORDER BY round" ) as $key => $row ) {

                // If MySQL error
                if ( $wpdb->last_error ) {
                    $html .= '<div class="cs-message error">' . $wpdb->last_error . '</div>';
                } else {

                    // If MySQL success
                    // If there are rows
                    if ( $wpdb->num_rows ) {

                        // Set values
                        $round = $row->round;
                        $date = $row->date;
                        $date = date( 'j. n. Y, H:i', strtotime( $date ) );

                        // Display round and date
                        $html .= '
                        <div class="cs-wrapper fixtures">
                            <div class="cs-table">
                                <div class="cs-row cs-header">
                                    <div class="cs-cell">Kolo ' . $round . ':&nbsp;&nbsp; ' . $date . '</div>
                                    <div class="cs-cell"></div>
                                </div>';

                        // Select fixtures data
                        $fixture_row = $wpdb->get_results( "SELECT id, team_home, team_away, result
                                                            FROM {$wpdb->prefix}chess_scraper_event_fixtures
                                                            WHERE event_settings_id = $settings_id AND
                                                                  round = $round" );

                        // If MySQL error
                        if ( $wpdb->last_error ) {
                            $html .= '<div class="cs-message error">' . $wpdb->last_error . '</div>';
                        } else {

                            // If MySQL success
                            // If there are rows
                            if ( $wpdb->num_rows ) {

                                // Set values
                                $fixture_id = $fixture_row[ 0 ]->id;
                                $team_home = $fixture_row[ 0 ]->team_home;
                                $team_away = $fixture_row[ 0 ]->team_away;
                                $team_result = $fixture_row[ 0 ]->result;

                                // Display fixture
                                $html .= '
                                        <div class="cs-row">
                                            <div class="cs-cell bold">' . $team_home . ' - ' . $team_away . '</div>
                                            <div class="cs-cell bold">' . $team_result . '</div>
                                        </div>';


                                // Select fixture boards data
                                foreach ( $wpdb->get_results( "SELECT home_player_name, home_player_elo, away_player_name, away_player_elo, result
                                                               FROM {$wpdb->prefix}chess_scraper_event_fixtures_boards
                                                               WHERE fixture_id = $fixture_id
                                                               ORDER BY id" ) as $key => $fixture_board_row ) {

                                    // If MySQL error
                                    if ( $wpdb->last_error ) {
                                        $html .= '<div class="cs-message error">' . $wpdb->last_error . '</div>';
                                    } else {

                                        // If MySQL success
                                        // If there are rows
                                        if ( $wpdb->num_rows ) {

                                            // Set values
                                            $home_player_name = $fixture_board_row->home_player_name;
                                            $home_player_elo = $fixture_board_row->home_player_elo;
                                            $away_player_name = $fixture_board_row->away_player_name;
                                            $away_player_elo = $fixture_board_row->away_player_elo;
                                            $player_result = $fixture_board_row->result;

                                            // Display fixture board
                                            $html .= '
                                                    <div class="cs-row">
                                                        <div class="cs-cell">' . $home_player_name . ' (' . $home_player_elo . ') - ' . $away_player_name . ' (' . $away_player_elo . ')</div>
                                                        <div class="cs-cell">' . $player_result . '</div>
                                                    </div>';

                                        }

                                    }

                                }

                            }

                        }

                        // End table
                        $html .= '
                            </div>
                        </div>';

                    } else {
                        // No rows found
                        $html .= '
                        <div class="cs-message error">Error: No data uploaded. Upload it first.</div>';
                    }

                }

            }

            // Source URL
            $html .= '
            <div class="cs-data-info">Údaje sú získavané z webu <a href="https://chess-results.com" target="_blank">chess-results.com</a>.</div>';

    }

    // Return whole HTML
    return $html;

}