<?php
/**
 * Admin form builder.
 *
 * @package Chess_Scraper
 * @version 1.0.0
 */

// Start form
// Params:
// $page => page name
// $section => section name
function chsc_admin_form_start( $page, $section ) {

    echo '
    <div class="theme-settings-form">
        <form action="?page=' . $page . '&amp;section=' . $section . '" method="post">';

}

// End form
function chsc_admin_form_end() {

    global $wpdb;

    echo '
    </form>
    </div>';

    // Clear DB cache
    $wpdb->flush();

}

// Start block
// Params:
// $title => block title
function chsc_admin_form_block_start( $title ) {

    echo '
    <div class="tsf-block">
        <h2>' . $title . '</h2>';

}

// End block
function chsc_admin_form_block_end() {

    echo '
    </div>';

}

// Text input
// Params:
// $label_name => label name
// $input_name => input name
// $input_value => input value
// $smaller => for smaller width of input (value: 1)
function chsc_admin_form_input_text( $label_name, $input_name, $input_value, $smaller = NULL, $note = NULL ) {

    // Set smaller class value
    if ( $smaller ) {
        $smaller = ' smaller';
    } else {
        $smaller = '';
    }

    // Set note
    if ( $note ) {
        $note_div = '
        <div class="tsf-note">' . $note . '</div>';
    } else {
        $note_div = '';
    }

    // Edit null value
    if ( $input_value == 0 ) {
        $input_value = '';
    }

    // Display input row
    echo '
    <div class="tsf-row">
        <div class="tsf-label">
            <label>' . $label_name . '</label>
        </div>
        <div class="tsf-input' . $smaller . '">
            <input name="' . $input_name . '" type="text" value="' . $input_value . '">
            ' . $note_div . '
        </div>
    </div>';

}

// Text with note
// Params:
// $text => text with note
function chsc_admin_form_note( $text, $additional_css = NULL ) {

    echo '
    <div class="tsf-note-row ' . $additional_css . '">
        ' . $text . '
    </div>';

}

// Text with shortcode
// Params:
// $text => text before shortcode
// $shortcode => shortcode
function chsc_admin_form_shortcode_note( $text, $shortcode, $additional_css = NULL ) {

    echo '
    <div class="tsf-row tsf-note-row ' . $additional_css . '">
        <span class=" bold">' . $text . '</span><br>' . $shortcode . '
    </div>';

}

// Select box
// Params:
// $label_name => label name
// $select_name => select name
// $select_values_array => array with values for select
// $selected_value => selected value
function chsc_admin_form_select( $label_name, $select_name, $select_values_array, $selected_value ) {

    echo '
    <div class="tsf-row">
        <div class="tsf-label">
            <label>' . $label_name . '</label>
        </div>
        <div class="tsf-input">
            <select name="' . $select_name . '">
                <option value="">---</option>';

            foreach ( $select_values_array as $key => $value ) {

                if ( $value == $selected_value ) {
                    $selected = ' selected';
                } else {
                    $selected = '';
                }

                echo '
                <option value="' . $value . '"' . $selected . '>' . $key . '</option>';

            }

    echo '
            </select>
        </div>
    </div>';

}

// Submit button
// Params:
// $input_value => input value
// $event_settings_id => $event_settings_id for update league data button
function chsc_admin_form_submit( $input_value, $event_settings_id = NULL ) {

    // Set update class
    if ( $event_settings_id != NULL ) {
        $update_class = 'update';
    } else {
        $update_class = '';
    }

    // Create button
    echo '
    <div class="tsf-submit ' . $update_class . '">';

    if ( $event_settings_id != NULL ) {
        // Update
        echo '<input name="submit" type="submit" value="' . $input_value . ' #' . $event_settings_id . '">';
    } else {
        // Default submit
        echo '<input name="submit" type="submit" value="' . $input_value . '">';
    }

    echo '</div>';

}

// Converter of date format
// Params:
// $db_date => datetime obtained from database row
function chsc_admin_last_import_info( $db_date ) {

    if ( $db_date != '0000-00-00 00:00:00' &&
         $db_date != '1970-01-01 12:00:00' ) {

        $converted_date = date( 'j. n. Y, g:i', strtotime( $db_date ) );
        chsc_admin_form_note( 'Last data import: ' . $converted_date, 'padding-top-16' );

    } else {
        chsc_admin_form_note( 'No data imported yet.', 'padding-top-16' );
    }

}