<?php
/**
 * Enqueue styles.
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

// Register CSS style
function chsc_register_styles() {

    // Main CSS
    wp_enqueue_style( 'chsc-style', PLUGIN_DIRECTORY_URI . 'style.css' );

}

add_action( 'wp_enqueue_scripts', 'chsc_register_styles' );