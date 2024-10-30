<?php
/**
 * Admin Plugins
 *
 * @package     CHWEBR
 * @subpackage  Admin/Plugins
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Plugins row action links
 *
 * @author Michael Cannon <mc@aihr.us>
 * @since 2.0
 * @param array $links already defined action links
 * @param string $file plugin file path and name being processed
 * @return array $links
 */
function chwebr_plugin_action_links( $links, $file ) {
	$settings_link = '<a href="' . admin_url( 'options-general.php?page=chwebr-settings' ) . '">' . esc_html__( 'General Settings', 'chwebr' ) . '</a>';
	if ( $file == 'chwebsocialsharer/chwebsocialshare.php' )
		array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'chwebr_plugin_action_links', 10, 2 );


/**
 * Plugin row meta links
 *
 * @author Michael Cannon <mc@aihr.us>
 * @since 2.0
 * @param array $input already defined meta links
 * @param string $file plugin file path and name being processed
 * @return array $input
 */
function chwebr_plugin_row_meta( $input, $file ) {
	if ( $file != 'chwebsocialsharer/chwebsocialshare.php' )
		return $input;

	$links = array(
		'<a href="' . admin_url( 'options-general.php?page=chwebr-settings' ) . '">' . esc_html__( 'Getting Started', 'chwebr' ) . '</a>',
		'<a href="https://www.chaudharyweb.com/downloads/">' . esc_html__( 'Add Ons', 'chwebr' ) . '</a>',
	);

	$input = array_merge( $input, $links );

	return $input;
}
add_filter( 'plugin_row_meta', 'chwebr_plugin_row_meta', 10, 2 );