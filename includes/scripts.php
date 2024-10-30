<?php

/**
 * Scripts
 *
 * @package     CHWEBR
 * @subpackage  Functions
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

add_action( 'admin_enqueue_scripts', 'chwebr_load_admin_scripts', 100 );
add_action( 'wp_enqueue_scripts', 'chwebr_load_scripts', 10 );
add_action( 'wp_enqueue_scripts', 'chwebr_register_styles', 10 );
add_action( 'wp_enqueue_scripts', 'chwebr_load_inline_styles', 10 );
//add_action( 'amp_post_template_css', 'chwebr_amp_load_css', 10 );

/**
 * Load Scripts
 *
 * Enqueues the required scripts.
 *
 * @since 1.0
 * @global $chwebr_options
 * @global $post
 * @return void
 * @param string $hook Page hook
 */
function chwebr_load_scripts( $hook ) {
    global $chwebr_options, $post, $chwebr_sharecount;
    if( !apply_filters( 'chwebr_load_scripts', chwebrGetActiveStatus(), $hook ) ) {
        chwebdebug()->info( "chwebr_load_script not active" );
        return;
    }

    $url = chwebr_get_url();
    $title = urlencode( html_entity_decode( the_title_attribute( 'echo=0' ), ENT_COMPAT, 'UTF-8' ) );
    $title = str_replace( '#', '%23', $title );
    $titleclean = esc_html( $title );
    $image = "";
    $desc = "";

    if ( isset($post->ID) ){
    $image = chwebr_get_image( $post->ID );
    $desc = chwebr_get_excerpt_by_id( $post->ID );
    }
    // Rest API Not used any longer
    //$restapi = chwebr_allow_rest_api() ? "1" : "0";
    
    /* Load hashshags */
    $hashtag = !empty( $chwebr_options['chwebsocialsharer_hashtag'] ) ? $chwebr_options['chwebsocialsharer_hashtag'] : '';

    $js_dir = CHWEBR_PLUGIN_URL . 'assets/js/';
    // Use minified libraries if Chwebsocialshare debug mode is turned off
    $suffix = ( chwebrIsDebugMode() ) ? '' : '.min';

    isset( $chwebr_options['load_scripts_footer'] ) ? $in_footer = true : $in_footer = false;
    
      wp_enqueue_script( 'chwebr', $js_dir . 'chwebr' . $suffix . '.js', array('jquery'), CHWEBR_VERSION, $in_footer );
	   // wp_enqueue_script( 'chwebr', $js_dir . 'chwebnet' . $suffix . '.js', array('jquery'), CHWEBR_VERSION, $in_footer );

    //wp_enqueue_script( 'element-queries', $js_dir . 'ElementQueries' . '.js', array('jquery'), CHWEBR_VERSION, $in_footer );
    //wp_enqueue_script( 'resize-sensor', $js_dir . 'ResizeSensor' . '.js', array('jquery'), CHWEBR_VERSION, $in_footer );
    
    !isset( $chwebr_options['disable_sharecount'] ) ? $shareresult = getSharedcount( $url ) : $shareresult = 0;
    wp_localize_script( 'chwebr', 'chwebr', array(
        'shares' => $shareresult,
        'round_shares' => isset( $chwebr_options['chwebsocialsharer_round'] ),
        /* Do not animate shares on blog posts. The share count would be wrong there and performance bad */
        'animate_shares' => isset( $chwebr_options['animate_shares'] ) && is_singular() ? 1 : 0,
        'dynamic_buttons' => isset( $chwebr_options['dynamic_button_resize'] ) ? 1 : 0,
        'share_url' => $url,
        'title' => $titleclean,
        'image' => $image,
        'desc' => $desc,
        'hashtag' => $hashtag,
        'subscribe' => !empty( $chwebr_options['subscribe_behavior'] ) && $chwebr_options['subscribe_behavior'] === 'content' ? 'content' : 'link',
        'subscribe_url' => isset( $chwebr_options['subscribe_link'] ) ? $chwebr_options['subscribe_link'] : '',
        'activestatus' => chwebrGetActiveStatus(),
        'singular' => is_singular() ? 1 : 0,
        'twitter_popup' => isset( $chwebr_options['twitter_popup'] ) ? 0 : 1,
        //'restapi' => $restapi
        'refresh' => chwebr_is_cache_refresh() ? 1 : 0
    ) );
}

/**
 * Register CSS Styles
 *
 * Checks the styles option and hooks the required filter.
 *
 * @since 1.0
 * @global $chwebr_options
 * @return void
 */
function chwebr_register_styles( $hook ) {
    if( !apply_filters( 'chwebr_register_styles', chwebrGetActiveStatus(), $hook ) ) {
        return;
    }
    global $chwebr_options;

    if( isset( $chwebr_options['disable_styles'] ) ) {
        return;
    }

    // Use minified libraries if Chwebsocialshare debug mode is turned off
    $suffix = ( chwebrIsDebugMode() ) ? '' : '.min';
    $file = 'chwebr' . $suffix . '.css';

    $url = CHWEBR_PLUGIN_URL . 'assets/css/' . $file;
    wp_enqueue_style( 'chwebr-styles', $url, array(), CHWEBR_VERSION );
	 // Use minified libraries if Chwebsocialshare debug mode is turned off
    $suffix = ( chwebrIsDebugMode() ) ? '' : '.min';
    $file = 'chwebnet' . $suffix . '.css';

    $url = CHWEBR_PLUGIN_URL . 'assets/css/' . $file;
    wp_enqueue_style( 'chwebr-styles', $url, array(), CHWEBR_VERSION );
}

/**
 * Load Admin Scripts
 *
 * Enqueues the required admin scripts.
 *
 * @since 1.0
 * @global $post
 * @param string $hook Page hook
 * @return string custom css into
 */
function chwebr_load_admin_scripts( $hook ) {
    if( !apply_filters( 'chwebr_load_admin_scripts', chwebr_is_admin_page(), $hook ) ) {
        return;
    }
    global $chwebr_options;

    $js_dir = CHWEBR_PLUGIN_URL . 'assets/js/';
    $css_dir = CHWEBR_PLUGIN_URL . 'assets/css/';

    // Use minified libraries if Chwebsocialshare debug mode is turned off
    $suffix = ( chwebrIsDebugMode() ) ? '' : '.min';
    
    wp_enqueue_script( 'chwebr-admin-scripts', $js_dir . 'chwebr-admin' . $suffix . '.js', array('jquery'), CHWEBR_VERSION, false );
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'media-upload' ); //Provides all the functions needed to upload, validate and give format to files.
    wp_enqueue_script( 'thickbox' ); //Responsible for managing the modal window.
    wp_enqueue_style( 'thickbox' ); //Provides the styles needed for this window.
    wp_enqueue_style( 'chwebr-admin', $css_dir . 'chwebr-admin' . $suffix . '.css', CHWEBR_VERSION );
    wp_register_style( 'jquery-chosen', $css_dir . 'chosen' . $suffix . '.css', array(), CHWEBR_VERSION );
    wp_enqueue_style( 'jquery-chosen' );

    wp_register_script( 'jquery-chosen', $js_dir . 'chosen.jquery' . $suffix . '.js', array('jquery'), CHWEBR_VERSION );
    wp_enqueue_script( 'jquery-chosen' );
}

/**
 * Get Share Count Color incl. compatibility mode for earlier version
 * 
 * @global $chwebr_options $chwebr_options
 * @return string
 */
function chwebr_get_share_color(){
    global $chwebr_options;
    // Compatibility mode. Early values were stored including #
    // New values are stored without #
    
    $value = !empty($chwebr_options['share_color']) ? $chwebr_options['share_color'] : '';
    return str_replace('#', '', $value); 
}

/**
 * Add Custom Styles with WP wp_add_inline_style Method
 *
 * @since 1.0
 * 
 * @return string
 */
function chwebr_load_inline_styles() {
    global $chwebr_options;

    /* VARS */
    
    $is_share_color = chwebr_get_share_color();
    $share_color = !empty( $is_share_color ) ? '.chwebr-count {color:#' . $is_share_color . ';}' : '';
    isset( $chwebr_options['custom_css'] ) ? $custom_css = $chwebr_options['custom_css'] : $custom_css = '';
    isset( $chwebr_options['small_buttons'] ) ? $smallbuttons = true : $smallbuttons = false;
    $button_width = isset( $chwebr_options['button_width'] ) ? $chwebr_options['button_width'] : null;

    /* STYLES */
    $chwebr_custom_css = $share_color;
    
    if( !empty( $chwebr_options['border_radius'] ) && $chwebr_options['border_radius'] != 'default' ) {
        $chwebr_custom_css .= '
        [class^="chwebicon-"], .onoffswitch-label, .onoffswitch2-label, .onoffswitch {
            border-radius: ' . $chwebr_options['border_radius'] . 'px;
        }';
    }
    if( !empty( $chwebr_options['chweb_style'] ) && $chwebr_options['chweb_style'] == 'gradiant' ) {
        $chwebr_custom_css .= '.chwebr-buttons a {
        background-image: -webkit-linear-gradient(bottom,rgba(0, 0, 0, 0.17) 0%,rgba(255, 255, 255, 0.17) 100%);
        background-image: -moz-linear-gradient(bottom,rgba(0, 0, 0, 0.17) 0%,rgba(255, 255, 255, 0.17) 100%);
        background-image: linear-gradient(bottom,rgba(0,0,0,.17) 0%,rgba(255,255,255,.17) 100%);}';
    }
    if( $smallbuttons === true ) {
        $chwebr_custom_css .= '[class^="chwebicon-"] .text, [class*=" chwebicon-"] .text{
    text-indent: -9999px !important;
    line-height: 0px;
    display: block;
    } 
    [class^="chwebicon-"] .text:after, [class*=" chwebicon-"] .text:after {
        content: "" !important;
        text-indent: 0;
        font-size:13px;
        display: block !important;
    }
    [class^="chwebicon-"], [class*=" chwebicon-"] {
        width:25%;
        text-align: center !important;
    }
    [class^="chwebicon-"] .icon:before, [class*=" chwebicon-"] .icon:before {
        float:none;
        margin-right: 0;
    }
    .chwebr-buttons a{
       margin-right: 3px;
       margin-bottom:3px;
       min-width: 0;
       width: 41px;
    }
    .onoffswitch, 
    .onoffswitch-inner:before, 
    .onoffswitch-inner:after 
    .onoffswitch2,
    .onoffswitch2-inner:before, 
    .onoffswitch2-inner:after  {
        margin-right: 0px;
        width: 41px;
        line-height: 41px;
    }';
    } else {
        // need this to make sure the min-width value is not overwriting the responsive add-on settings if available
        //if ($button_width && !chwebr_is_active_responsive_addon() ){
        //$chwebr_custom_css .= '.chwebr-buttons a {min-width: ' . $button_width . 'px}';
        //}
        if( $button_width ) {
            $chwebr_custom_css .= '@media only screen and (min-width:568px){.chwebr-buttons a {min-width: ' . $button_width . 'px;}}';
        }
    }

    $chwebr_custom_css .= $custom_css;

    wp_add_inline_style( 'chwebr-styles', $chwebr_custom_css );
}

/**
 * Load AMP (Accelerated Mobile Pages) CSS
 * 
 * @return string css
 */
//function chwebr_amp_load_css() {
//    global $chwebr_options;
//
//    $share_color = !empty( $chwebr_options['share_color'] ) ? '.chwebr-count {color:' . $chwebr_options['share_color'] . '}' : '';
//    $custom_css = isset( $chwebr_options['custom_css'] ) ? $chwebr_options['custom_css'] : '';
//    $amp_css = isset( $chwebr_options['amp_css'] ) ? $chwebr_options['amp_css'] : '';
//    
//    $css = "@font-face {
//  font-family: 'chwebr-font';
//  src: url('" . CHWEBR_PLUGIN_URL . "/assets/css/fonts/chwebr-font.eot?29924580');
//  src: url('" . CHWEBR_PLUGIN_URL . "/assets/css/fonts/chwebr-font.eot?29924580#iefix') format('embedded-opentype'),
//       url('" . CHWEBR_PLUGIN_URL . "/assets/css/fonts/chwebr-font.woff2?29924580') format('woff2'),
//       url('" . CHWEBR_PLUGIN_URL . "/assets/css/fonts/chwebr-font.woff?29924580') format('woff'),
//       url('" . CHWEBR_PLUGIN_URL . "/assets/css/fonts/chwebr-font.ttf?29924580') format('truetype'),
//       url('" . CHWEBR_PLUGIN_URL . "/assets/css/fonts/chwebr-font.svg?29924580#chwebr-font') format('svg');
//  font-weight: normal;
//  font-style: normal;
//}";
//    
//    // Get default css file
//    $css .= file_get_contents( CHWEBR_PLUGIN_DIR . '/assets/css/chwebr-amp.css' );
//    
//
//    // add custom css
//    $css .= $custom_css;
//
//    // add AMP custom css
//    $css .= $amp_css;
//
//    // STYLES
//    $css .= $share_color;
//
//    if( !empty( $chwebr_options['border_radius'] ) && $chwebr_options['border_radius'] != 'default' ) {
//        $css .= '
//        [class^="chwebicon-"], .onoffswitch-label, .onoffswitch2-label {
//            border-radius: ' . $chwebr_options['border_radius'] . 'px;
//        }';
//    }
//    if( !empty( $chwebr_options['chweb_style'] ) && $chwebr_options['chweb_style'] == 'gradiant' ) {
//        $css .= '
//    .chwebr-buttons a {
//        background-image: -webkit-linear-gradient(bottom,rgba(0, 0, 0, 0.17) 0%,rgba(255, 255, 255, 0.17) 100%);
//        background-image: -moz-linear-gradient(bottom,rgba(0, 0, 0, 0.17) 0%,rgba(255, 255, 255, 0.17) 100%);
//        background-image: linear-gradient(bottom,rgba(0,0,0,.17) 0%,rgba(255,255,255,.17) 100%);}';
//    }
//    // Get css for small buttons
//    $css .= '[class^="chwebicon-"] .text, [class*=" chwebicon-"] .text{
//        text-indent: -9999px;
//        line-height: 0px;
//        display: block;
//        } 
//    [class^="chwebicon-"] .text:after, [class*=" chwebicon-"] .text:after {
//        content: "";
//        text-indent: 0;
//        font-size:13px;
//        display: block;
//    }
//    [class^="chwebicon-"], [class*=" chwebicon-"] {
//        width:25%;
//        text-align: center;
//    }
//    [class^="chwebicon-"] .icon:before, [class*=" chwebicon-"] .icon:before {
//        float:none;
//        margin-right: 0;
//    }
//    .chwebr-buttons a{
//       margin-right: 3px;
//       margin-bottom:3px;
//       min-width: 0px;
//       width: 41px;
//    }
//
//    .onoffswitch, 
//    .onoffswitch-inner:before, 
//    .onoffswitch-inner:after 
//    .onoffswitch2,
//    .onoffswitch2-inner:before, 
//    .onoffswitch2-inner:after  {
//        margin-right: 0px;
//        width: 41px;
//        line-height: 41px;
//    }';
//    // hide plus and subscribe button 
//    // on AMP we disable js
//    $css .= '.onoffswitch2, .onoffswitch{display:none}';
//
//    // Hide subscribe button when it's not a link
//    $css .= isset( $chwebr_options['subscribe_behavior'] ) && $chwebr_options['subscribe_behavior'] === 'content' ? '.chwebicon-subscribe{display:none;}' : '';
//
//    // Make sure the share buttons are not moving under the share count when decreasing width
//    $css .= '.chwebr-buttons{display:table;}';
//
//    // Float the second shares box
//    $css .= '.secondary-shares{float:left;}';
//
//    // Hide the view count
//    $css .= '.chwebpv{display:none;}';
//
//    echo $css;
//}

/*
 * Check if debug mode is enabled
 * 
 * @since 2.2.7
 * @return bool true if Chwebsocialshare debug mode is on
 */

function chwebrIsDebugMode() {
    global $chwebr_options;

    $debug_mode = isset( $chwebr_options['debug_mode'] ) ? true : false;
    return $debug_mode;
}

/**
 * Check if responsive add-on is installed and activated
 * 
 * @return true if add-on is installed
 */
function chwebr_is_active_responsive_addon() {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if( is_plugin_active( 'chwebsocialshare-responsive/chwebsocialshare-responsive.php' ) ) {
        return true;
    }
    return false;
}
