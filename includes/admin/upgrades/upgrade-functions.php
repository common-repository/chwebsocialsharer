<?php
/**
 * Upgrade Functions
 *
 * @package     CHWEBR
 * @subpackage  Admin/Upgrades
 * @copyright   Copyright (c) 2016, Ren´é Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.3.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Perform automatic upgrades when necessary
 *
 * @since 3.3.4
 * @return void
*/
function chwebr_do_automatic_upgrades() {

	$did_upgrade = false;
	$chwebr_version = preg_replace( '/[^0-9.].*/', '', get_option( 'chwebr_version' ) );

	if( version_compare( $chwebr_version, '1.0.0', '<' ) ) {
		chwebr_upgrade_v1a();
	}
	if( version_compare( $chwebr_version, '1.0.0', '<' ) ) {
		chwebr_upgrade_v1();
	}
        // Check if version number in DB is lower than version number in current plugin
	if( version_compare( $chwebr_version, CHWEBR_VERSION, '<' ) ) {

		// Let us know that an upgrade has happened
		$did_upgrade = true;

	}

        // Update Version number
	if( $did_upgrade ) {

		update_option( 'chwebr_version', preg_replace( '/[^0-9.].*/', '', CHWEBR_VERSION ) );

	}

}
add_action( 'admin_init', 'chwebr_do_automatic_upgrades' );


/**
 * Store default settings
 */
function chwebr_upgrade_v1a() {
    
    // Show Rating Div
    add_option( 'chwebr_RatingDiv', 'no' );
    // Show facebook access token notice
    add_option( 'chwebr_update_notice_101', 'yes' ); 
    
}



/**
 * Enable the margin option
 */
function chwebr_upgrade_v1() {
    
    // Try to load some settings.
    $settings = get_option( 'chwebr_settings' );
    // Enable the Margin Option. 
    if( !array_key_exists( 'button_margin', $settings ) ) {
        $button_margin = array('button_margin' => '1');
        $settings_upgrade = array_merge( $button_margin, $settings );
        update_option( 'chwebr_settings', $settings_upgrade );
    }
}
