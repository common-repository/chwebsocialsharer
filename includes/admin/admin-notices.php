<?php

/**
 * Admin Notices
 *
 * @package     CHWEBR
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Check if at least one social network is enabled
 * 
 * @global array $chwebr_options
 * @return boolean false when no network is enabled
 */
function chwebr_check_active_networks() {
    global $chwebr_options;

    $networks = isset( $chwebr_options['networks'] ) ? $chwebr_options['networks'] : false;

    if( isset( $networks ) && is_array( $networks ) )
        foreach ( $networks as $key => $value ) {
            if( isset( $networks[$key]['status'] ) )
                return true;
        }

    return false;
}

/**
 * Admin Messages
 *
 * @since 2.2.3
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */
function chwebr_admin_messages() {
    global $chwebr_options;

    if( !current_user_can( 'update_plugins' ) ){
        return;
    }
    
    // Rate Limit warning
    if( chwebr_is_admin_page() && chwebr_rate_limit_exceeded() ) {
        echo '<div class="error">';
        echo '<p>' . sprintf(__('Your website exceeded the Facebook rate limit. Share count requests to Facebook will be delayed for 60min and the Facebook Share Count will not grow during this time. If you see this notice often consider to change <strong>ChwebSocialShare Caching Method</strong> to <a href="%s">Refresh while Loading</a> and use a higher cache expiration. ChwebSocialShare tries again to request shares in ' . chwebrGetRemainingRateLimitTime() , 'chwebr'), admin_url() . 'admin.php?page=chwebr-settings') . '</p>';
        echo '</div>';
    }
//    // Access Token expired
//    if( chwebr_is_access_token_expired() ) {
//        echo '<div class="error">';
//        echo '<p>' . sprintf(__('Your Facebook Access Token has been expired. You need to <a href="%s">generate a new one</a> or your ChwebSocialShare Facebook Shares will not be refreshed', 'chwebr'), admin_url() . 'admin.php?page=chwebr-settings') . '</p>';
//        echo '</div>';
//    }
    
    // Cache warning
    if( chwebr_is_deactivated_cache() ) {
        echo '<div class="error">';
        echo '<p>' . sprintf(__('Attention: The Chwebsocialshare Cache is deactivated. <a href="%s">Activate it</a> or share count requests to social networks will be rate limited.', 'chwebr'), admin_url() . 'admin.php?page=chwebr-settings#chwebr_settingsdebug_header') . '</p>';
        echo '</div>';
    }
    // Cache warning
    if( chwebr_is_deleted_cache() ) {
        echo '<div class="error">';
        echo '<p>' . sprintf(__('Attention: The Chwebsocialshare Cache is permanetely purged. <a href="%s">Disable this</a> or share count requests to social networks will be rate limited.', 'chwebr'), admin_url() . 'admin.php?page=chwebr-settings#chwebr_settingsdebug_header') . '</p>';
        echo '</div>';
    }
    
    //chwebr_update_notice_101();
    
    if( chwebr_is_admin_page() && !function_exists( 'curl_init' ) ) {
        echo '<div class="error">';
        echo '<p>' . sprintf(__('ChwebSocialShare needs the PHP extension cURL which is not installed on your server. Please <a href="%s" target="_blank">install and activate</a> it to be able to collect share count of your posts.', 'chwebr'), 'https://www.google.com/search?btnG=1&pws=0&q=enable+curl+on+php') . '</p>';
        echo '</div>';
    }

    // notice no Networks enabled    
    if( chwebr_is_admin_page() && !chwebr_check_active_networks() ) {
        echo '<div class="error">';
        echo '<p>' . sprintf( __( 'No Social Networks enabled. Go to <a href="%s"> Chwebsocialshare->Settings->Social Networks</a> and enable at least one Social Network.', 'chwebr' ), admin_url( 'admin.php?page=chwebr-settings&tab=networks#chwebr_settingsservices_header' ) ) . '</p>';
        echo '</div>';
    }
    // Share bar add-on notice    
    if( chwebr_is_admin_page() && chwebr_incorrect_sharebar_version() ) { 
        echo '<div class="error">';
        echo '<p>' . sprintf( __( 'Your Sharebar Add-On version is not using new short url mechanism of ChwebSocialShare 3.X. Please <a href="%s" target="blank"> update the Sharebar Add-On</a> to at least version 1.2.5. if you want to make sure that twitter short urls will not stop working in one of the next updates. This requires a valid license of the Sharebar Add-On', 'chwebr' ), 'https://www.chaudharyweb.com/downloads/sticky-sharebar/?utm_source=insideplugin&utm_medium=userwebsite&utm_content=update_sharebar&utm_campaign=freeplugin' ) . '</p>';
        echo '</div>';
    }
    // Floating Sidebar add-on notice    
    if( chwebr_is_admin_page() && chwebr_incorrect_sidebar_version() ) {
        echo '<div class="error">';
        echo '<p>' . sprintf( __( 'Your Floating Sidebar Add-On version is not using new short url mechanism of ChwebSocialShare 3.X. Please <a href="%s" target="blank"> update the Floating Sidebar Add-On</a> to at least version 1.2.6. if you want to make sure that twitter short urls will not stop working in one of the next updates. This requires a valid license of the Floating Sidebar Add-On', 'chwebr' ), 'https://www.chaudharyweb.com/downloads/floating-sidebar/?utm_source=insideplugin&utm_medium=userwebsite&utm_content=update_sharebar&utm_campaign=freeplugin' ) . '</p>';
        echo '</div>';
    }
    // Check google API key  
    if( chwebr_is_admin_page() && ( chwebr_check_google_apikey() && isset( $chwebr_options['chwebsu_methods'] ) && $chwebr_options['chwebsu_methods'] === 'google' ) ) {
        echo '<div class="error">';
        echo '<p>' . sprintf( __( 'Google API key is invalid. Go to <a href="%s"><i>Chwebsocialshare->Settings->Short URL Integration</i></a> and check the Google API key.', 'chwebr' ), admin_url( 'admin.php?page=chwebr-settings#chwebr_settingsshorturl_header' ) ) . '</p>';
        echo '</div>';
    }
    // Check Bitly API key  
    if( chwebr_is_admin_page() && (false === chwebr_check_bitly_apikey() && isset( $chwebr_options['chwebsu_methods'] ) && $chwebr_options['chwebsu_methods'] === 'bitly' ) ) {
        echo '<div class="error">';
        echo '<p>' . sprintf( __( 'Bitly Access Token is invalid or bitly.com endpoint can not be reached. Go to <a href="%s"><i>Chwebsocialshare->Settings->Short URL Integration</i></a> and check the Bitly API key.', 'chwebr' ), admin_url( 'admin.php?page=chwebr-settings#chwebr_settingsshorturl_header' ) ) . '</p>';
        echo '</div>';
    }
    // Notice ChwebSocialShare Open Graph Add-On installed and activated
    if( class_exists( 'ChwebsocialshareOpenGraph' ) ) {
        echo '<div class="error">';
        echo '<p>' . sprintf( __( '<strong>Important:</strong> Deactivate the ChwebSocialShare Open Graph Add-On. It is not longer needed and having it activated leads to duplicate open graph tags on your site. Go to <a href="%s"> Plugin Settings</a> ', 'chwebr' ), admin_url( 'plugins.php' ) ) . '</p>';
        echo '</div>';
    }
    // Notice ChwebSocialShare ShortURL Add-On installed and activated
    if( class_exists( 'ChwebsocialshareShorturls' ) ) {
        echo '<div class="error">';
        echo '<p>' . sprintf( __( '<strong>Important:</strong> Deactivate the ChwebSocialShare Shorturls Add-On. It is not longer needed and already built in ChwebSocialShare. Deactivate it from <a href="%s"> Plugin Settings</a> ', 'chwebr' ), admin_url( 'plugins.php' ) ) . '</p>';
        echo '</div>';
    }
    // Share count is deactivated when permalinks are not used
    if( chwebr_is_admin_page() && !chwebr_is_enabled_permalinks() ) {
        echo '<div class="error">';
        echo '<p>' . sprintf( __( '<strong>No Share Count aggregation possible!</strong> <a href="%s">Permalinks</a> must be enabled to count shares. Share count is deactivated until you have fixed this.', 'chwebr' ), admin_url( 'options-permalink.php' ) ) . '</p>';
        echo '</div>';
    }
    
    // Show save notice
    if( isset( $_GET['chwebr-message'] ) ) {
        switch ( $_GET['chwebr-message'] ) {
            case 'settings-imported' :
                echo '<div class="updated">';
                echo '<p>' . __( 'The settings have been imported', 'chwebr' ) . '</p>';
                echo '</div>';
                break;
        }
    }


    // Please rate us
    $install_date = get_option( 'chwebr_installDate' );
    $display_date = date( 'Y-m-d h:i:s' );
    $datetime1 = new DateTime( $install_date );
    $datetime2 = new DateTime( $display_date );
    $diff_intrval = round( ($datetime2->format( 'U' ) - $datetime1->format( 'U' )) / (60 * 60 * 24) );
    if( $diff_intrval >= 7 && get_option( 'chwebr_RatingDiv' ) == "no" ) {
        echo '<div class="chwebr_fivestar update-nag" style="box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);">
    	<p>Awesome, you\'ve been using <strong>Chwebsocialshare Social Sharing Plugin</strong> for more than 1 week. <br> May i ask you to give it a <strong>5-star rating</strong> on Wordpress? </br>
        This will help to spread its popularity and to make this plugin a better one.
        <br><br>Your help is much appreciated. Thank you very much,<br> ~Rajesh Chaudhary
        <ul>
            <li class="float:left"><a href="https://wordpress.org/support/plugin/chwebsocialsharer/reviews/?filter=5#new-post" class="thankyou button button-primary" target="_new" title=Yes, ChwebSocialShare Increased My Shares" style="color: #ffffff;-webkit-box-shadow: 0 1px 0 #256e34;box-shadow: 0 1px 0 #256e34;font-weight: normal;float:left;margin-right:10px;">I Like ChwebSocialShare - It Increased My Shares</a></li>
            <li><a href="javascript:void(0);" class="chwebrHideRating button" title="I already did" style="">I already rated it</a></li>
            <li><a href="javascript:void(0);" class="chwebrHideRating" title="No, not good enough" style="">No, not good enough, i do not like to rate it!</a></li>
        </ul>
    </div>
    <script>
    jQuery( document ).ready(function( $ ) {

    jQuery(\'.chwebrHideRating\').click(function(){
        var data={\'action\':\'hideRating\'}
             jQuery.ajax({
        
        url: "' . admin_url( 'admin-ajax.php' ) . '",
        type: "post",
        data: data,
        dataType: "json",
        async: !0,
        success: function(e) {
            if (e=="success") {
               jQuery(\'.chwebr_fivestar\').slideUp(\'fast\');
			   
            }
        }
         });
        })
    
    });
    </script>
    ';
    }
    // Disabled since 2.4.7
    //chwebr_update_notices();
}
add_action( 'admin_notices', 'chwebr_admin_messages' );

/**
 * Check if sharebar add-on version is fully supported
 * 
 * @return boolean true if incorrect
 */
function chwebr_incorrect_sharebar_version() {
    if( defined( 'MASHBAR_VERSION' ) ) {
        return version_compare( MASHBAR_VERSION, '1.2.5', '<' );
    } else {
        return false;
    }
}
/**
 * Check if sharebar add-on version is fully supported
 * 
 * @return boolean true if incorrect
 */
function chwebr_incorrect_sidebar_version() {
    if( defined( 'MASHFS_VERSION' ) ) {
        return version_compare(MASHFS_VERSION, '1.1.6', '<');
    } else {
        return false;
    }
}

/* Hide the update notice div
 * 
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2015, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.4.0
 * 
 * @return json string
 * 
 */

function chwebr_hide_update_notice() {
    if( !empty( $_POST['action'] ) && $_POST['action'] === 'chwebr_hide_notice' && !empty( $_POST['id'] ) ) {
        //echo $_POST['action'] . '_' . $_POST['id'];
        update_option( 'chwebr_update_notice_' . $_POST['id'], 'no' );
        $result = array('success');
        echo json_encode( $result );
        exit;
    }
}

add_action( 'wp_ajax_chwebr_hide_notice', 'chwebr_hide_update_notice' );

/**
 * Return update notice 101
 * @since 3.2.0
 */
function chwebr_update_notice_101() {
    
        if( !chwebr_is_admin_page() ) {
            return false;
        }
    
    $notice_id = '101'; //unique id of our notice
    $message = sprintf(__( 'Admin notices are pain but read this one or you will miss how to fix your facebook share counts in ChwebSocialShare: <p><strong style="font-weight:bold;">Go to <a href="%1s">Settings->Networks</a> and request your access token via facebook login - That\'s all. '
            . '<a href="#" id="chwebr_notice_101_resp"> Whats also new? </a> </strong>'
                . '<div style="display:none;" id="chwebr_notice_101_more">'
                . '<ul style="font-weight:600;">'
                . '<li>- Full Width Responsive Buttons (Enable them from <a href="%2s">Visual Setting</a>)<li>'
                . '<li>- Most Shared Posts Widget incl. Thumbnails</li>'
                . '<li>- Cumulate Http(s) Shares - Move your site to ssl without loosing shares</li>'
                . '</div>'
            , 'chwebr' ), 
            admin_url() . 'admin.php?page=chwebr-settings#chwebr_settingsservices_header',
            admin_url() . 'admin.php?page=chwebr-settings#chwebr_settingsstyle_header'
            );
      
        if( get_option( 'chwebr_update_notice_' . $notice_id ) === 'yes' ) {
  
        // admin notice after updating Chwebsocialshare
        echo '<div class="chwebr_update_notice_'. $notice_id .' update-nag">' . $message . 
        '<p><a href="javascript:void(0);" class="chwebr_hide_'. $notice_id .'" title="I got it" style="text-decoration:none;">- Ok, Do Not Show Again</a></a>'
        . '</div>'
        . '<script>
    jQuery( document ).ready(function( $ ) {
        jQuery(\'.chwebr_hide_'. $notice_id .'\').click(function(){
            var data={
            \'action\':\'chwebr_hide_notice\',
            \'id\':\'101\',
            }
            jQuery.ajax({
                url: "' . admin_url( 'admin-ajax.php' ) . '",
                type: "post",
                data: data,
                dataType: "json",
                async: !0,
                success: function(e) {
                    if (e=="success") {
                       jQuery(\'.chwebr_update_notice_'. $notice_id .'\').hide();	   
                    }
                }
            });
        })
        jQuery(\'#chwebr_notice_101_resp\').click(function(e){
        e.preventDefault();
            jQuery(\'#chwebr_notice_101_more\').show()
        });
        
});
    </script>';
    }
}

/* Hide the rating div
 * 
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.2.3
 * 
 * @return json string
 * 
 */

function chwebr_HideRatingDiv() {
    update_option( 'chwebr_RatingDiv', 'yes' );
    echo json_encode( array("success") );
    exit;
}

add_action( 'wp_ajax_hideRating', 'chwebr_HideRatingDiv' );

/**
 * Admin Add-ons Notices
 *
 * @since 1.0
 * @return void
 */
function chwebr_admin_addons_notices() {
    add_settings_error( 'chwebr-notices', 'chwebr-addons-feed-error', __( 'There seems to be an issue with the server. Please try again in a few minutes.', 'chwebr' ), 'error' );
    settings_errors( 'chwebr-notices' );
}

/**
 * Dismisses admin notices when Dismiss links are clicked
 *
 * @since 1.8
 * @return void
 */
function chwebr_dismiss_notices() {

    $notice = isset( $_GET['chwebr_notice'] ) ? $_GET['chwebr_notice'] : false;
    if( !$notice )
        return; // No notice, so get out of here

    update_user_meta( get_current_user_id(), '_chwebr_' . $notice . '_dismissed', 1 );

    wp_redirect( esc_url( remove_query_arg( array('chwebr_action', 'chwebr_notice') ) ) );
    exit;
}

add_action( 'chwebr_dismiss_notices', 'chwebr_dismiss_notices' );

/*
 * Show big colored update information below the official update notification in /wp-admin/plugins
 * @since 2.0.8
 * @return void
 * 
 */

function chwebr_in_plugin_update_message( $args ) {
    $transient_name = 'chwebr_upgrade_notice_' . $args['Version'];

    if( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {

        $response = wp_remote_get( 'https://plugins.svn.wordpress.org/chwebsocialsharer/trunk/readme.txt' );

        if( !is_wp_error( $response ) && !empty( $response['body'] ) ) {

            // Output Upgrade Notice
            $matches = null;
            $regexp = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( CHWEBR_VERSION ) . '\s*=|$)~Uis';
            $upgrade_notice = '';

            if( preg_match( $regexp, $response['body'], $matches ) ) {
                $version = trim( $matches[1] );
                $notices = ( array ) preg_split( '~[\r\n]+~', trim( $matches[2] ) );

                if( version_compare( CHWEBR_VERSION, $version, '<' ) ) {

                    $upgrade_notice .= '<div class="chwebr_plugin_upgrade_notice" style="padding:10px;background-color:#58C1FF;color: #FFF;">';

                    foreach ( $notices as $index => $line ) {
                        $upgrade_notice .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}" style="text-decoration:underline;color:#ffffff;">${1}</a>', $line ) );
                    }

                    $upgrade_notice .= '</div> ';
                }
            }

            set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
        }
    }

    echo wp_kses_post( $upgrade_notice );
}

add_action( 'in_plugin_update_message-chwebsocialsharer/chwebsocialshare.php', 'chwebr_in_plugin_update_message' );

/**
 * Get remaining time in seconds of the rate limit transient
 * @return type
 */
function chwebrGetRemainingRateLimitTime() {
    $trans_time = get_transient( 'timeout_chwebr_rate_limit' );

    if( false !== $trans_time ) {
        $rest = abs(time() - $trans_time);
        
        if ($rest < 60){
            return $rest . ' seconds.';
        } else {
            $minutes = floor($rest / 60) . ' minutes.';
            return $minutes;
        }
    }
    return 0 . 'seconds';
}
