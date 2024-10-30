<?php

/**
 * Scripts
 *
 * @package     CHWEBR
 * @subpackage  AMP Functions
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

add_action( 'amp_post_template_css', 'chwebr_amp_load_css', 10 );

/**
 * Check if current page is AMP page
 * 
 * @return boolean
 */
function chwebr_is_amp_page(){
    // Defined in https://wordpress.org/plugins/amp/ is_amp_endpoint()
    
    if (  function_exists('is_amp_endpoint') && is_amp_endpoint()){
        return true;
    }
    return false;
}

/**
 * Load AMP (Accelerated Mobile Pages) CSS
 * 
 * @return string css
 */
function chwebr_amp_load_css() {
    global $chwebr_options;

    $share_color = !empty( $chwebr_options['share_color'] ) ? '.chwebr-count {color:' . $chwebr_options['share_color'] . '}' : '';
    $custom_css = isset( $chwebr_options['custom_css'] ) ? $chwebr_options['custom_css'] : '';
    $amp_css = isset( $chwebr_options['amp_css'] ) ? $chwebr_options['amp_css'] : '';
    
    $css = "@font-face {
  font-family: 'chwebr-font';
  src: url('" . CHWEBR_PLUGIN_URL . "assets/css/fonts/chwebr-font.eot?62884501');
  src: url('" . CHWEBR_PLUGIN_URL . "assets/css/fonts/chwebr-font.eot?62884501#iefix') format('embedded-opentype'),
       url('" . CHWEBR_PLUGIN_URL . "assets/css/fonts/chwebr-font.woff?62884501') format('woff'),
       url('" . CHWEBR_PLUGIN_URL . "assets/css/fonts/chwebr-font.ttf?62884501') format('truetype'),
       url('" . CHWEBR_PLUGIN_URL . "assets/css/fonts/chwebr-font.svg?62884501#chwebr-font') format('svg');
  font-weight: normal;
  font-style: normal;
}";
    
    // Get default css file
    $css .= file_get_contents( CHWEBR_PLUGIN_DIR . '/assets/css/chwebr-amp.css' );
    

    // add custom css
    $css .= $custom_css;

    // add AMP custom css
    $css .= $amp_css;

    // STYLES
    $css .= $share_color;

    if( !empty( $chwebr_options['border_radius'] ) && $chwebr_options['border_radius'] != 'default' ) {
        $css .= '
        [class^="chwebicon-"], .onoffswitch-label, .onoffswitch2-label {
            border-radius: ' . $chwebr_options['border_radius'] . 'px;
        }';
    }
    if( !empty( $chwebr_options['chweb_style'] ) && $chwebr_options['chweb_style'] == 'gradiant' ) {
        $css .= '
    .chwebr-buttons a {
        background-image: -webkit-linear-gradient(bottom,rgba(0, 0, 0, 0.17) 0%,rgba(255, 255, 255, 0.17) 100%);
        background-image: -moz-linear-gradient(bottom,rgba(0, 0, 0, 0.17) 0%,rgba(255, 255, 255, 0.17) 100%);
        background-image: linear-gradient(bottom,rgba(0,0,0,.17) 0%,rgba(255,255,255,.17) 100%);}';
    }
    // Get css for small buttons
    $css .= '[class^="chwebicon-"] .text, [class*=" chwebicon-"] .text{
        text-indent: -9999px;
        line-height: 0px;
        display: block;
        } 
    [class^="chwebicon-"] .text:after, [class*=" chwebicon-"] .text:after {
        content: "";
        text-indent: 0;
        font-size:13px;
        display: block;
    }
    [class^="chwebicon-"], [class*=" chwebicon-"] {
        width:25%;
        text-align: center;
    }
    [class^="chwebicon-"] .icon:before, [class*=" chwebicon-"] .icon:before {
        float:none;
        margin-right: 0;
    }
    .chwebr-buttons a{
       margin-right: 3px;
       margin-bottom:3px;
       min-width: 0px;
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
    // hide plus and subscribe button 
    // on AMP we disable js
    $css .= '.onoffswitch2, .onoffswitch{display:none}';

    // Hide subscribe button when it's not a link
    $css .= isset( $chwebr_options['subscribe_behavior'] ) && $chwebr_options['subscribe_behavior'] === 'content' ? '.chwebicon-subscribe{display:none;}' : '';

    // Make sure the share buttons are not moving under the share count when decreasing width
    $css .= '.chwebr-buttons{display:table;}';

    // Float the second shares box
    $css .= '.secondary-shares{float:left;}';

    // Hide the view count
    $css .= '.chwebpv{display:none;}';

    echo $css;
}