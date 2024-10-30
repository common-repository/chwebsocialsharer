<?php
/**
 * Admin Footer
 *
 * @package     CHWEBR
 * @subpackage  Admin/Footer
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add rating links to the settings footer
 *
 * @since	1.0.0
 * @return      string
 */
function chwebr_admin_rate_us() {
	if ( chwebr_is_admin_page() ) {

		$rate_text = sprintf( __( 'Please do us a BIG favor and give us a 5 star rating <a href="%1$s" target="blank">here.</a> Need help? Read our <a href="%2$s" target="blank">Documentation</a><br>If you`re not happy, please <a href="%3$s" target="blank">get in touch with us</a>, so that we can sort it out. Thank you!', 'chwebr' ),
			'',
                        '',
			'https://www.chaudharyweb.com'
		);
                

		return $rate_text;
	}
}
