<?php
/**
 * Plugin Name: Chess Scraper
 * Description: Scraping chess data for specific club and its league teams.
 * Author: tomm-tomm
 * Author URI: https://github.com/tomm-tomm/
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

 /*
  * Global constants
  */

define( 'PLUGIN_DIRECTORY', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_DIRECTORY_URI', plugin_dir_url( __FILE__ ) );

/*
 * Included files
 */

// Load style
require_once PLUGIN_DIRECTORY . '/settings/enqueuer.php';

// Load shortcode
require_once PLUGIN_DIRECTORY . '/listings/shortcode.php';

// Load admin stuff
if ( is_admin() ) {

    /* Create database tables */
    require_once PLUGIN_DIRECTORY . '/initialize/db-tables.php';

    /* Scraper */
    // Functions
    require_once PLUGIN_DIRECTORY . '/scraping/functions.php';

    /* Theme settings and logic */
    require_once PLUGIN_DIRECTORY . '/admin/menu.php';
    require_once PLUGIN_DIRECTORY . '/admin/form-builder.php';
    require_once PLUGIN_DIRECTORY . '/admin/db.php';
    require_once PLUGIN_DIRECTORY . '/admin/forms.php';

}