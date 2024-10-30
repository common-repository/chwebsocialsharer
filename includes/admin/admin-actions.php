<?php
/**
 * Admin Actions
 *
 * @package     CHWEBR
 * @subpackage  Admin/Actions
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Processes all CHWEBR actions sent via POST and GET by looking for the 'chwebr-action'
 * request and running do_action() to call the function
 *
 * @since 1.0
 * @return void
 */
function chwebr_process_actions() {
	if ( isset( $_POST['chwebr-action'] ) ) {
		do_action( 'chwebr_' . $_POST['chwebr-action'], $_POST );
	}

	if ( isset( $_GET['chwebr-action'] ) ) {
		do_action( 'chwebr_' . $_GET['chwebr-action'], $_GET );
	}
}
add_action( 'admin_init', 'chwebr_process_actions' );

/**
 * Arrange order of social network array when it is draged and droped
 * 
 * @global array $chwebr_options
 */
function chwebr_save_order(){
        global $chwebr_options;
        // Get all settings
        
        $current_list = get_option('chwebr_networks');
        $new_order = $_POST['chwebr_list'];
        $new_list = array();
        
        /* First write the sort order */
        foreach ($new_order as $n){
            if (isset($current_list[$n])){
                $new_list[$n] = $current_list[$n];
                
            }
        }
        /* Update sort order of networks */
        update_option('chwebr_networks', $new_list);
        die();
}
add_action ('wp_ajax_chwebr_update_order', 'chwebr_save_order');

/**
 * Force Facebook to rescrape site content after saving post
 * 
 * @todo check if blocking=>false is working as expected
 * @global array $post
 */
function chwebr_rescrape_fb_debugger(){
    global $post;
    if (!isset($post)){
        return;
    }
    $url = get_permalink($post->ID);
    $args = array('timeout' => 5, 'blocking' => false);
    $body = wp_remote_retrieve_body( wp_remote_get('https://graph.facebook.com/?id=' . $url, $args) );
}
add_action('save_post', 'chwebr_rescrape_fb_debugger' );

/**
 * Purge the ChwebSocialShare Cache
 * 
 * @global array $post
 * @return bool false
 */
function chwebr_purge_cache(){
    global $post;
    
    if (!isset($post)){
        return;
    }
    
    update_post_meta($post->ID, 'chwebr_timestamp', '');
}
add_action('save_post', 'chwebr_purge_cache' );

/**
 * Create bitly or google shorturls and store them initially in post meta
 * 
 * @global array $post
 * @return string
 * 
 * @deprecated since 3.1.2
 */
//function chwebr_create_shorturls() {
//    global $chwebr_options, $post;
//    
//    if (!isset($post)){
//        return;
//    }
//
//    $shorturl = "";
//    $url = get_permalink($post->ID);
//
//    // bitly shortlink
//    if( isset( $chwebr_options['chwebsu_methods'] ) && $chwebr_options['chwebsu_methods'] === 'bitly' ) {
//        $shorturl = chwebr_get_bitly_link( $url );
//    }
//
//    // Google shortlink
//    if( isset( $chwebr_options['chwebsu_methods'] ) && $chwebr_options['chwebsu_methods'] === 'google' ) {
//        $shorturl = chwebr_get_google_link( $url );
//    }
//    if (!empty($shorturl)){
//    update_post_meta( $post->ID, 'chwebr_shorturl', $shorturl );
//    }
//}
