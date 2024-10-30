<?php
/**
 * Admin Options Page
 *
 * @package     CHWEBR
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/* Returns list elements for jQuery tab navigation 
 * based on header callback
 * 
 * @since 2.1.2
 * @todo Use sprintf to sanitize  $field['id'] instead using str_replace() Should be much faster? 
 * @return string
 */

function getTabHeader( $page, $section ) {
    global $chwebr_options;
    global $wp_settings_fields;

    if( !isset( $wp_settings_fields[$page][$section] ) )
        return;

    echo '<ul id="chwebr-tabs-sidebar">';
    foreach ( ( array ) $wp_settings_fields[$page][$section] as $field ) {
        $sanitizedID = str_replace( '[', '', $field['id'] );
        $sanitizedID = str_replace( ']', '', $sanitizedID );
        if( strpos( $field['callback'], 'header' ) !== false ) {
            echo '<li class="chwebr-tabs" id="' . $sanitizedID . '-nav"><a href="#' . $sanitizedID . '">' . $field['title'] . '</a></li>';
        }
    }
    echo '</ul>';
}

/**
 * Print out the settings fields for a particular settings section
 *
 * Part of the Settings API. Use this in a settings page to output
 * a specific section. Should normally be called by do_settings_sections()
 * rather than directly.
 *
 * @global $wp_settings_fields Storage array of settings fields and their pages/sections
 * @return string
 *
 * @since 2.1.2
 *
 * @param string $page Slug title of the admin page who's settings fields you want to show.
 * @param section $section Slug title of the settings section who's fields you want to show.
 * 
 * Copied from WP Core 4.0 /wp-admin/includes/template.php do_settings_fields()
 * We use our own function to be able to create jQuery tabs with easytabs()
 * 
 *  We dont use tables here any longer. Are we stuck in the nineties?
 * @todo Use sprintf to sanitize  $field['id'] instead using str_replace() Should be faster?
 * @todo some media queries for better responisbility
 */
function chwebr_do_settings_fields( $page, $section ) {
    global $wp_settings_fields;
    $header = false;
    $firstHeader = false;

    if( !isset( $wp_settings_fields[$page][$section] ) )
        return;

    // Check first if any callback header registered
    foreach ( ( array ) $wp_settings_fields[$page][$section] as $field ) {
        strpos( $field['callback'], 'header' ) !== false ? $header = true : $header = false;

        if( $header === true )
            break;
    }

    foreach ( ( array ) $wp_settings_fields[$page][$section] as $field ) {

        $sanitizedID = str_replace( '[', '', $field['id'] );
        $sanitizedID = str_replace( ']', '', $sanitizedID );

        // Check if header has been created previously
        if( strpos( $field['callback'], 'header' ) !== false && $firstHeader === false ) {
            echo '<div id="' . $sanitizedID . '">';
            echo '<table class="form-table"><tbody>';
            $firstHeader = true;
        } elseif( strpos( $field['callback'], 'header' ) !== false && $firstHeader === true ) {
            // Header has been created previously so we have to close the first opened div
            echo '</table></div><div id="' . $sanitizedID . '">';
            echo '<table class="form-table"><tbody>';
        }
        //if( (!empty( $field['args']['label_for'] ) || empty( $field['args']['desc'] )) )  {
        if( strpos( $field['callback'], 'header' ) !== false ){
            // Do not return header_callback Its only needed for creating the navigation entries
        }
        // The headline
        else if( strpos( $field['callback'], 'headline' ) !== false )  {
            echo '<tr class="row"><th class="row th">';
            echo '<div class="col-title"><h2>' . $field['title'] . '</h2></div>';
            echo '</th>';
            echo '<td>';
            call_user_func( $field['callback'], $field['args'] );
            echo '</td>';
            echo '</tr>';
        // The Settings
        } else {
            echo '<tr class="row"><th class="row th">';
            echo '<div class="col-title">' . $field['title'];
            // Do not show the helper text when its empty
            if (!empty($field['args']['desc']) ){
                echo '<a class="chwebr-helper" href="#"></a>';
                echo '<div class="chwebr-message">' . $field['args']['desc'] . '</div>';
            }
            echo '</div>';
            echo '</th>';
            echo '<td>';
            call_user_func( $field['callback'], $field['args'] );
            echo '</td>';
            echo '</tr>';
        }
    }
    echo '</tbody></table>';
    if( $header === true ) {
        echo '</div>';
    }
}

/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @since 1.0
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */
function chwebr_options_page() {
    global $chwebr_options;

    $active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], chwebr_get_settings_tabs() ) ? $_GET['tab'] : 'general';

    ob_start();
    ?>
    <div class="chwebr_admin">
        <span class="chwebsocialsharelogo"> <?php echo __( 'ChwebSocialShare ', 'chwebr' ); ?></span><span class="chwebr-version"><?php echo CHWEBR_VERSION; ?></span>
        <div class="about-text" style="clear:both;">
            <ul id="chweb-social-admin-head">
                <?php echo chwebr_share_buttons(); ?>
            </ul>

        </div>

        <h2 class="chwebr nav-tab-wrapper">
            <?php
            foreach ( chwebr_get_settings_tabs() as $tab_id => $tab_name ) {

                $tab_url = esc_url( add_query_arg( array(
                    'settings-updated' => false,
                    'tab' => $tab_id
                        ) ) );

                $active = $active_tab == $tab_id ? ' nav-tab-active' : '';

                echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
                echo esc_html( $tab_name );
                echo '</a>';
            }
            ?>
        </h2>
        <div id="chwebr_container" class="chwebr_container">
                    <?php getTabHeader( 'chwebr_settings_' . $active_tab, 'chwebr_settings_' . $active_tab ); ?>   
            <div class="chwebr-panel-container"> <!-- new //-->
                <form method="post" action="options.php">
                    <?php
                    settings_fields( 'chwebr_settings' );
                    chwebr_do_settings_fields( 'chwebr_settings_' . $active_tab, 'chwebr_settings_' . $active_tab );
                    ?>
                    <!--</table>-->

                    <?php
                    // do not show save button on add-on page
                    if( $active_tab !== 'addons' )
                        submit_button();
                    ?>
                </form>
                                    <div><?php echo chwebr_admin_rate_us(); ?></div>
            </div> <!-- new //-->
        </div><!-- #tab_container-->
       
    </div><!-- .chwebr_admin -->
    <?php
    echo ob_get_clean();
}
