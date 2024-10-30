<?php
/**
 * Uninstall Chwebsocialshare
 *
 * @package     CHWEBR
 * @subpackage  Uninstall
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Load CHWEBR file
include_once( 'chwebsocialshare.php' );

global $wpdb, $chwebr_options;

if( chwebr_get_option( 'uninstall_on_delete' ) ) {
	/** Delete all the Plugin Options */
	delete_option( 'chwebr_settings' );
        delete_option( 'chwebr_networks');
        delete_option( 'chwebr_installDate');
        delete_option( 'chwebr_RatingDiv');
        delete_option( 'chwebr_version');
        delete_option( 'chwebr_version_upgraded_from');
        delete_option( 'chwebr_update_notice');
        delete_option( 'chwebr_tracking_notice');
        delete_option( 'widget_chwebr_mostshared_posts_widget');
        delete_option( 'chwebr_tracking_last_send');
        delete_option( 'chwebr_update_notice_101');
        

        /* Delete all post meta options */
        delete_post_meta_by_key( 'chwebr_timestamp' );
        delete_post_meta_by_key( 'chwebr_shares' );
        delete_post_meta_by_key( 'chwebr_jsonshares' );
        
        //delete transients
        delete_transient('chwebr_rate_limit');
        delete_transient('chwebr_limit_req');
        
        wp_clear_scheduled_hook('chwebsocialsharer_transients_cron');
}
