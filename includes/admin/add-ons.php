<?php
/**
 * Admin Add-ons
 *
 * @package     CHWEBR
 * @subpackage  Admin/Add-ons
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add-ons
 *
 * Renders the add-ons content.
 *
 * @since 1.1.8
 * @return void
 */
function chwebr_add_ons_page() {
	ob_start(); ?>
	<div class="wrap" id="chwebr-add-ons">
		<h2>
			<?php _e( 'Donations to Chwebsocialshare', 'chwebr' ); ?>
			&nbsp;&mdash;&nbsp;<a href="https://www.chaudharyweb.com" class="button-primary" title="<?php _e( 'Visit Website', 'chwebr' ); ?>" target="_blank"><?php _e( 'See Details', 'chwebr' ); ?></a>
		</h2>
		<p>
	
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">

    <input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="QU2FVGWHVVG6L">

    <!-- Specify details about the contribution -->
    <input type="hidden" name="item_name" value="development">
    <input type="hidden" name="item_number" value="Chwebsocialshare">
    <input type="hidden" name="amount" value="25.00">
    <input type="hidden" name="currency_code" value="USD">

    <!-- Display the payment button. -->
    <input type="image" name="submit"
    src="https://www.paypalobjects.com/webstatic/en_US/i/btn/png/btn_donate_92x26.png"
    alt="Pay">
    <img alt="" width="1" height="1"
    src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" >
</form>
		
		<?php // _e( 'These add-ons extend the functionality of Chwebsocialshare.', 'chwebr' ); ?></p>
		<?php ///echo chwebr_add_ons_get_feed(); ?>
	</div>
	<?php
	echo ob_get_clean();
}

/**
 * Add-ons Get Feed
 *
 * Gets the add-ons page feed.
 *
 * @since 1.1.8
 * @return void
 */
function chwebr_add_ons_get_feed() {
	if ( false === ( $cache = get_transient( 'chwebsocialshare_add_ons_feed' ) ) ) {
		$feed = wp_remote_get( 'https://www.chaudharyweb.com/?feed=addons', array( 'sslverify' => false ) );
		if ( ! is_wp_error( $feed ) ) {
			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$cache = wp_remote_retrieve_body( $feed );
				set_transient( 'chwebsocialshare_add_ons_feed', $cache, 3600 );
			}
		} else {
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the Chwebsocialshare addon list from the server. Please try again later.', 'chwebr' ) . '
                                   <br>Visit instead the Chwebsocialshare Addon Website <a href="https://www.chaudharyweb.com" class="button-primary" title="Chwebsocialshare Add ons" target="_blank"> Get Add-Ons  </a></div>';
		}
	}
	return $cache;
}