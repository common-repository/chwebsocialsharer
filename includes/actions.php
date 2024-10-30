<?php
/**
 * Front-end Actions
 *
 * @package     CHWEBR
 * @subpackage  Functions
 * @copyright   Copyright (c) 2015, Pippin Williamson, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.5.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Hooks CHWEBR actions, when present in the $_GET superglobal. Every chwebr_action
 * present in $_GET is called using WordPress's do_action function. These
 * functions are called on init.
 *
 * @since 1.0
 * @return void
*/
function chwebr_get_actions() {
	if ( isset($_GET['chwebr_action']) ) {
		do_action( 'chwebr_' .sanitize_text_field($_GET['chwebr_action']), $_GET );
	}
}
add_action( 'init', 'chwebr_get_actions' );

/**
 * Hooks CHWEBR actions, when present in the $_POST superglobal. Every chwebr_action
 * present in $_POST is called using WordPress's do_action function. These
 * functions are called on init.
 *
 * @since 1.0
 * @return void
*/
function chwebr_post_actions() {
	if ( isset($_POST['chwebr_action']) ) {
		do_action( 'chwebr_' . sanitize_text_field($_POST['chwebr_action']), $_POST );
	}
}
add_action( 'init', 'chwebr_post_actions' );

/**
 * Force cache refresh via GET REQUEST
 * 
 * @global array $chwebr_options
 * @return boolean true for cache refresh
 */
function chwebr_force_cache_refresh() {
    global $chwebr_options;
    
    // Needed for testing (phpunit)
    if (CHWEBR_DEBUG || isset( $chwebr_options['disable_cache'] ) ){
        chwebr()->logger->info('chwebr_force_cache_refresh() -> Debug mode enabled');
        return true;
    }
    
    $caching_method = !empty($chwebr_options['caching_method']) ? $chwebr_options['caching_method'] : 'refresh_loading';
    
    // Old method and less performant - Cache is rebuild during pageload
    if($caching_method == 'refresh_loading'){
        if (chwebr_is_cache_refresh()){
            return true;
        }
    }
    
    // New method - Cache will be rebuild after complete pageloading and will be initiated via ajax.
    if( isset( $_GET['chwebr-refresh'] ) && $caching_method == 'async_cache' ) {
        CHWEBR()->logger->info('Force Cache Refresh');
        return true;
    }
}
add_action( 'init', 'chwebr_force_cache_refresh' );
 