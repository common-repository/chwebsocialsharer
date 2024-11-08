<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Get the current user data
$user = wp_get_current_user();

?>

<div id="chwebr-sidebar">

	<a class="chwebr-banner" target="_blank" href="https://www.chaudharyweb.com/pricing/?utm_source=insideplugin&utm_medium=userwebsite&utm_content=sidebar&utm_campaign=freeplugin"><img src="<?php echo CHWEBR_PLUGIN_URL . 'assets/images/upgrade_to_pro.png'; ?>" width="300" height="250" alt="<?php _e( 'Increase your Shares and Social Traffic', 'chwebr' ); ?>" /></a>

	<form method="post" action="<?php echo $post; ?>" target="_blank" class="subscribe block">
		<h2><?php _e( 'Get 20% Off!', 'chwebr' ); ?></h2>

		<?php $user = wp_get_current_user(); ?>

		<p class="interesting">
			<?php echo wptexturize( __( "Submit your name and email and we'll send you a coupon for 20% off your upgrade to the pro version.", 'chwebr' ) ); ?>
		</p>

		<div class="field">
			<input type="email" name="email" value="<?php echo esc_attr( $user->user_email ); ?>" placeholder="<?php _e( 'Your Email', 'chwebr' ); ?>"/>
		</div>

		<div class="field">
			<input type="text" name="firstname" value="<?php echo esc_attr( trim( $user->user_firstname ) ); ?>" placeholder="<?php _e( 'First Name', 'chwebr' ); ?>"/>
		</div>

		<div class="field">
			<input type="text" name="lastname" value="<?php echo esc_attr( trim( $user->user_lastname ) ); ?>" placeholder="<?php _e( 'Last Name', 'chwebr' ); ?>"/>
		</div>

		<input type="hidden" name="campaigns[]" value="4" />
		<input type="hidden" name="source" value="8" />

		<div class="field submit-button">
			<input type="submit" class="button" value="<?php _e( 'Send me the coupon', 'chwebr' ); ?>"/>
		</div>

		<p class="promise">
			<?php _e( 'Your email will not be used for anything else and you can unsubscribe with 1-click anytime.', 'chwebr' ); ?>
		</p>
                <p style="text-align: center;margin-top:25px;"><?php echo sprintf(__( '<a href="%s" target="_new" style="font-weight:bold;color:#00adff;border: 1px solid #00adff;padding:6px;">Visit Our Affiliate Program', 'chwebr'), 'https://www.chaudharyweb.com/become-partner/?utm_source=chwebradmin&utm_medium=website&utm_campaign=see_our_affiliate_program' ); ?></a></p>
                
                
	</form>

	<div class="block testimonial">
		<p class="stars">
			<span class="dashicons dashicons-star-filled"></span>
			<span class="dashicons dashicons-star-filled"></span>
			<span class="dashicons dashicons-star-filled"></span>
			<span class="dashicons dashicons-star-filled"></span>
			<span class="dashicons dashicons-star-filled"></span>
		</p>

		<p class="quote">
			&#8220;Really happy with @Chwebsocialshare. This brilliant WordPress plugin helped increase @iCulture shares by 30%. Highly recommended.&#8221;
		</p>

		<p class="author">&mdash; Jean-Paul Horn</p>

		<p class="via"><a target="_blank" href="https://twitter.com/JeanPaulH/status/726084101145550850">via Twitter</a></p>
	</div>
</div>