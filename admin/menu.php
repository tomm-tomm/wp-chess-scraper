<?php
/**
 * Admin additional menu.
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

/*
 * Menu and submenu
 */
function chsc_admin_settings() {

    add_menu_page( 'Theme settings',
                   'Theme settings',
                   'manage_options',
                   'default',
                   'chsc_admin_page_default' );

    $submenu_default = add_submenu_page( 'default',
                                         'Club',
                                         'Club',
                                         'manage_options',
                                         'default',
                                         'chsc_admin_page_default' );

    $submenu_league = add_submenu_page( 'default',
                                        'League',
                                        'League',
                                        'manage_options',
                                        'league',
                                        'chsc_admin_page_league' );

    $submenu_updates = add_submenu_page( 'default',
                                         'Updates',
                                         'Updates',
                                         'manage_options',
                                         'updates',
                                         'chsc_admin_page_updates' );

    // Call custom CSS for theme pages
    add_action( 'admin_print_styles-' . $submenu_default, 'chsc_custom_admin_css' );
    add_action( 'admin_print_styles-' . $submenu_league, 'chsc_custom_admin_css' );
    add_action( 'admin_print_styles-' . $submenu_updates, 'chsc_custom_admin_css' );

}

add_action( 'admin_menu', 'chsc_admin_settings' );

// Custom CSS for theme pages
function chsc_custom_admin_css() {

    $custom_css_file = PLUGIN_DIRECTORY_URI . 'admin/styles/style-custom-admin.css';
    wp_enqueue_style( 'chsc-custom-admin-css', $custom_css_file );

}

/*
 * Menu and submenu callback functions
 */

// Default page
function chsc_admin_page_default() {
    ?>

    <h1><?php esc_html_e( 'Club settings' ); ?></h1>

    <?php
    // Load default page
    chsc_admin_load_page( 'default' );

}

// League page
function chsc_admin_page_league() {
    ?>

    <h1><?php esc_html_e( 'League settings' ); ?></h1>

    <?php
    // Load default page
    chsc_admin_load_page( 'league' );

}

// Updates page
function chsc_admin_page_updates() {
    ?>

    <h1><?php esc_html_e( 'Updates settings' ); ?></h1>

    <?php
    // Load import page
    chsc_admin_load_page( 'updates' );

}

/*
 * Page loader
 */
function chsc_admin_load_page( $page, $section = NULL ) {

    // Initalize WP database functions
    global $wpdb;

    // Section lists
    $section_list = array( 'default' => array( 'identifiers' => 'Identifiers' ),
                           'league'  => array( 'identifiers' => 'Identifiers' ),
                           'updates' => array( 'settings' => 'Settings' ) );

    // Set sections
    if ( !isset( $_GET[ 'section' ] ) ) {
        $section = array_key_first( $section_list[ $page ] );
    } else {
        $section = $_GET[ 'section' ];
    }

    // Build navigation
    chsc_admin_nav_builder( $page, $section, $section_list );

    // Load page
    require_once( 'forms/' . $page . '-' . $section . '.php' );

}

/*
 * Navigation builder
 */

// Build menu
function chsc_admin_nav_builder( $page, $section, $section_list ) {

    // Start menu
    echo '
    <nav class="theme-settings-menu">
        <ul>';

    // Create menu list
    foreach ( $section_list[ $page ] as $key => $value ) {

        if ( $section == $key ) {
            $active = ' class="active"';
        } else {
            $active = '';
        }

        echo '
        <li><a' . $active . ' href="?page=' . $page . '&amp;section=' . $key . '">' . $value . '</a></li>';

    }

    // End menu
    echo '
        </ul>
    </nav>';

}