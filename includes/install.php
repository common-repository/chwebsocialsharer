<?php

/**
 * Install Function
 *
 * @package     CHWEBR
 * @subpackage  Functions/Install
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/* 
 * Install Multisite
 * check first if multisite is enabled
 * @since 2.1.1
 * 
 */

register_activation_hook( CHWEBR_PLUGIN_FILE, 'chwebr_install_multisite' );

function chwebr_install_multisite( $networkwide ) {
    global $wpdb;

    if( function_exists( 'is_multisite' ) && is_multisite() ) {
        // check if it is a network activation - if so, run the activation function for each blog id
        if( $networkwide ) {
            $old_blog = $wpdb->blogid;
            // Get all blog ids
            $blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach ( $blogids as $blog_id ) {
                switch_to_blog( $blog_id );
                chwebr_install();
            }
            switch_to_blog( $old_blog );
            return;
        }
    }
    chwebr_install();
}

/**
 * Install
 *
 * Runs on plugin install to populates the settings fields for those plugin
 * pages. After successful install, the user is redirected to the CHWEBR Welcome
 * screen.
 *
 * @since 2.0
 * @global $wpdb
 * @global $chwebr_options
 * @global $wp_version
 * @return void
 */
function chwebr_install() {
    
    // Disable ChwebSocialShare Open Graph plugin
    if (class_exists( 'ChwebsocialshareOpenGraph' )){
    deactivate_plugins( '/chwebsocialshare-opengraph/chwebsocialshare-opengraph.php' );
    }
    // Deactivate Shorturl Add-On because it is integrated in ChwebSocialShare 3.0
    deactivate_plugins( '/chwebsocialshare-shorturls/chwebsocialshare-shorturls.php' );
    
    // Get current version number
    $current_version = get_option( 'chwebr_version' );

    // Try to load some settings. If there are no ones we write some default settings:
    $settings = get_option( 'chwebr_settings' );
    
    // Write default settings. Check first if there are no settings
    if( !$settings || count( $settings ) === 0 ) {
        $settings_default = array(
            'visible_services' => '1',
            'networks' => array(
                0 => array(
                    'id' => 'facebook',
                    'status' => '1',
                    'name' => 'Share'
                ),
                1 => array(
                    'id' => 'twitter',
                    'status' => '1',
                    'name' => 'Tweet'
                )
            ),
            'post_types' => array('post'=>'post'),
            'chwebsocialsharer_position' => 'before',
            'loadall' => '1',
            'twitter_card' => '1',
            'open_graph' => '1',
            'chwebr_sharemethod' => 'chwebengine',
            'caching_method' => 'async_cache',
            'chwebsu_methods' => 'disabled',
            'responsive_buttons' => '1',
            'button_margin' => '1',
            'text_align_center' => '1',
            'chwebsocialsharer_round' => '1',
        );
        update_option( 'chwebr_settings', $settings_default );
    }
 
    // Add Upgraded From Option
    if( $current_version ) {
        update_option( 'chwebr_version_upgraded_from', $current_version );
    }

    // Update the current version number
    update_option( 'chwebr_version', CHWEBR_VERSION );
    
    // Add plugin installation date and variable for rating div
    add_option( 'chwebr_installDate', date( 'Y-m-d h:i:s' ) );
    add_option( 'chwebr_RatingDiv', 'no' );
    add_option( 'chwebr_update_notice_101', 'yes' ); // Show facebook access token notice

    /* 
     * Setup the default network options
     * Store our initial social networks in separate option row.
     */
    $networks = array(
                'Facebook',
                'Twitter',
                'Subscribe',
                'Google',
                'Pinterest',
                'Digg',
                'Linkedin',
                'Reddit',
                'Stumbleupon',
                'Vk',
                'Print',
                'Delicious',
                'Buffer',
                'Weibo',
                'Pocket',
                'Tumblr',
                'Mail',
                'Meneame',
                'Odnoklassniki',
                'Managewp',
                'Mailru',    
                'Line',
                'yummly',
                'frype',
                'skype',
                'Telegram',
                'Flipboard',
                'Hackernews'
            );
        update_option( 'chwebr_networks', $networks );
    

    // Bail if activating from network, or bulk
    if( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
        return;
    }

    // Add the transient to redirect / not for multisites
    set_transient( '_chwebr_activation_redirect', true, 120 );
}

/**
 * Post-installation
 *
 * Runs just after plugin installation and exposes the
 * chwebr_after_install hook.
 *
 * @since 2.0
 * @return void
 */
function chwebr_after_install() {

    if( !is_admin() ) {
        return;
    }

    $activation_pages = get_transient( '_chwebr_activation_pages' );

    // Exit if not in admin or the transient doesn't exist
    if( false === $activation_pages ) {
        return;
    }

    // Delete the transient
    delete_transient( '_chwebr_activation_pages' );

    do_action( 'chwebr_after_install', $activation_pages );
}

add_action( 'admin_init', 'chwebr_after_install' );
