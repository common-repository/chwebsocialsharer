<?php
/**
 * Contextual Help
 *
 * @package     CHWEBR
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Settings contextual help.
 *
 * @access      private
 * @since       1.0
 * @return      void
 */
function chwebr_settings_contextual_help() {
	$screen = get_current_screen();

	/*if ( $screen->id != 'chwebr-settings' )
		return;
*/
	$screen->set_help_sidebar(
		'<p><strong>' . $screen->id . sprintf( __( 'For more information:', 'chwebr' ) . '</strong></p>' .
		'<p>' . sprintf( __( 'Visit the <a href="%s">documentation</a> on the Chwebsocialshare website.', 'chwebr' ), esc_url( 'https://www.chaudharyweb.com/' ) ) ) . '</p>' .
		'<p>' . sprintf(
					__( '<a href="%s">Post an issue</a> on <a href="%s">Chwebsocialshare</a>. View <a href="%s">extensions</a>.', 'chwebr' ),
					esc_url( 'https://www.chaudharyweb.com/contact-support/' ),
					esc_url( 'https://www.chaudharyweb.com' ),
					esc_url( 'https://www.chaudharyweb.com/downloads' )
				) . '</p>'
	);

	$screen->add_help_tab( array(
		'id'	    => 'chwebr-settings-general',
		'title'	    => __( 'General', 'chwebr' ),
		'content'	=> '<p>' . __( 'This screen provides the most basic settings for configuring Chwebsocialshare.', 'chwebr' ) . '</p>'
	) );


	

	do_action( 'chwebr_settings_contextual_help', $screen );
}
add_action( 'load-chwebr-settings', 'chwebr_settings_contextual_help' );
