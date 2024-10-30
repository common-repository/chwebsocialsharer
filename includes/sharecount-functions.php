<?php

/**
 * Helper functions for retriviving the share counts from social networks
 *
 * @package     CHWEBR
 * @subpackage  Functions/sharecount
 * @copyright   Copyright (c) 2015, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Check if the facebook rate limit has been exceeded
 * @return boolean
 */
function chwebr_rate_limit_exceeded(){
    //return true; // Uncomment this for testing
    if (false === get_transient('chwebr_rate_limit')){
        return false;
    }
    return true;
}
/**
 * Check if the facebook access token has been expired
 * @return boolean
 */
function chwebr_is_access_token_expired(){
    global $chwebr_options;
    
    if (empty($chwebr_options['expire_fb_access_token'])){
        return false;
    }
    
    if (time()>= $chwebr_options['expire_fb_access_token']){
        return true;
    }
    return false;
}

    /**
     * Make sure that requests do not exceed 1req / 25second
     * @return boolean
     */
    function chwebr_is_req_limited() {
        global $chwebr_error;
        
        if (false === get_transient('chwebr_limit_req')) {
            set_transient('chwebr_limit_req', '1', 25);
            $chwebr_error[] = 'ChwebSocialShare: Temp Rate Limit not exceeded';
            return false;
        }
            $chwebr_error[] = 'ChwebSocialShare: Temp Rate Limit Exceeded';
            CHWEBR()->logger->info('ChwebSocialShare: Temp Rate Limit Exceeded');
        return true;
        
    }

/**
 * Check if cache time is expired and post must be refreshed
 * 
 * @global array $post
 * @return boolean 
 */
function chwebr_is_cache_refresh() {
    global $post, $chwebr_options;
    
    
    // Debug mode or cache activated
    if( CHWEBR_DEBUG || isset( $chwebr_options['disable_cache'] ) ) {
        CHWEBR()->logger->info( 'chwebr_is_cache_refresh: CHWEBR_DEBUG - refresh Cache' );
        return true;
    }
    
    // if it's a crawl deactivate cache
    if( isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'] ) ) {
        return false;
    }
    
    /*
     * Deactivate share count on:
     * - 404 pages
     * - search page
     * - empty url
     * - disabled permalinks
     * - admin pages
     * 
        Exit here to save cpu time
     */

    if( is_404() || is_search() || is_admin() || !chwebr_is_enabled_permalinks() ) {
        return false;
    }

    // New cache on singular pages
    // 
    // Refreshing cache on blog posts like categories will lead 
    // to high load and multiple API requests so we only check 
    // the main url on these other pages
    if( is_singular() ) {
        // last updated timestamp 
        $last_updated = get_post_meta( $post->ID, 'chwebr_timestamp', true );
        if( !empty( $last_updated ) ) {
            CHWEBR()->logger->info( 'chwebr_is_cache_refresh - is_singular() url: ' . get_permalink($post->ID) . ' : last updated:' . date( 'Y-m-d H:i:s', $last_updated ) );
        }
    } else if( chwebr_get_main_url() ) {

        // Get transient timeout and calculate last update time
        $url = chwebr_get_main_url();
        $transient = '_transient_timeout_chwebcount_' . md5( chwebr_get_main_url() );
        $last_updated = get_option( $transient ) - chwebr_get_expiration();
        if( !empty( $last_updated ) ) {
            CHWEBR()->logger->info( 'chwebr_is_cache_refresh() chwebr_get_main_url() url: ' . $url . ' last updated:' . date( 'Y-m-d H:i:s', $last_updated ) );
        }
    } else {
        // No valid URL so do not refresh cache
        CHWEBR()->logger->info( 'chwebr_is_cache_refresh: No valid URL - do not refresh cache' );
        return false;
    }

    // No timestamp so let's create cache for the first time
    if( empty( $last_updated ) ) {
        CHWEBR()->logger->info( 'chwebr_is_cache_refresh: No Timestamp. Refresh Cache' );
        return true;
    }

    // The caching expiration
    $expiration = chwebr_get_expiration();
    $next_update = $last_updated + $expiration;
    CHWEBR()->logger->info( 'chwebr_is_cache_refresh. Next update ' . date( 'Y-m-d H:i:s', $next_update ) . ' current time: ' . date( 'Y-m-d H:i:s', time() ) );

    // Refresh Cache when last update plus expiration is older than current time
    if( ($last_updated + $expiration) <= time() ) {
        CHWEBR()->logger->info( 'chwebr_is_cache_refresh: Refresh Cache!' );
        return true;
    }
}

/**
 * Check via ajax if cache should be updated
 * 
 * @deprecated not used
 * @return string numerical 
 */
function chwebr_ajax_refresh_cache() {
    if( chwebr_is_cache_refresh() ) {
        wp_die( '1' );
    } else {
        wp_die( '0' );
    }
}

add_action( 'wp_ajax_chwebr_refresh_cache', 'chwebr_ajax_refresh_cache' );
add_action( 'wp_ajax_nopriv_chwebr_refresh_cache', 'chwebr_ajax_refresh_cache' );

/**
 * Get expiration time for new Asyn Cache Method
 * 
 * @since 3.0.0
 * @return int
 */
function chwebr_get_expiration_method_async() {
    // post age in seconds
    $post_age = floor( date( 'U' ) - get_post_time( 'U', true ) );

    if( isset( $post_age ) && $post_age > 5184000 ) {
        // Post older than 60 days - expire cache after 12 hours
        $seconds = 43200;
    } else if( isset( $post_age ) && $post_age > 75600 ) {
        // Post older than 21 days - expire cache after 4 hours.
        $seconds = 14400;
    } else {
        // expire cache after one hour
        $seconds = 3600;
    }

    return $seconds;
}

/**
 * Get expiration time for old method "Refresh On Loading"
 * 
 * @since 3.0.0
 * @return int
 */
function chwebr_get_expiration_method_loading() {
    global $chwebr_options;
    // Get the expiration time
    $seconds = isset( $chwebr_options['chwebsocialsharer_cache'] ) ? ( int ) ($chwebr_options['chwebsocialsharer_cache']) : 300;

    return $seconds;
}

/**
 * Get expiration time
 * 
 * @return int
 */
function chwebr_get_expiration() {
    global $chwebr_options;
    $expiration = (isset( $chwebr_options['caching_method'] ) && $chwebr_options['caching_method'] == 'async_cache') ? chwebr_get_expiration_method_async() : chwebr_get_expiration_method_loading();

    // Set expiration time to zero if debug mode is enabled or cache deactivated
    if( CHWEBR_DEBUG || isset( $chwebr_options['disable_cache'] ) ) {
        $expiration = 0;
    }

    return ( int ) $expiration;
}

/**
 * Check if we can use the REST API
 * 
 * @deprecated not used
 * @return boolean true
 */
//function chwebr_allow_rest_api() {
//    if( version_compare( get_bloginfo( 'version' ), '4.4.0', '>=' ) ) {
//        return true;
//    }
//}

/**
 * Check via REST API if cache should be updated
 * 
 * @since 3.0.0
 * @deprecated not used
 * @return string numerical 
 */
//function chwebr_restapi_refresh_cache( $request ) {
//    if( chwebr_is_cache_refresh() ) {
//        return '1';
//    } else {
//        return '0';
//    }
//}

/**
 * Register the API route
 * Used in WP 4.4 and later The WP REST API got a better performance than native ajax endpoints
 * Endpoint: /wp-json/chwebsocialshare/v1/verifycache/
 * 
 * @since 3.0.0
 * @deprecated not used
 * */
//if( chwebr_allow_rest_api() ) {
//    add_action( 'rest_api_init', 'chwebr_rest_routes' );
//}
//
//function chwebr_rest_routes() {
//    register_rest_route( 'chwebsocialshare/v1', '/verifycache/', array(
//        'methods' => \WP_REST_Server::READABLE,
//        'callback' => 'chwebr_restapi_refresh_cache'
//            )
//    );
//}

/**
 * Check if permalinks are enabled
 * 
 * @return boolean true when enabled
 */
function chwebr_is_enabled_permalinks() {
    $permalinks = get_option('permalink_structure');
    if (!empty($permalinks)) {
        return true;
    }
    return false;
}

/**
 * Return the current main url
 * 
 * @return mixed string|bool current url or false
 */
function chwebr_get_main_url() {
    global $wp;

    $url = home_url( add_query_arg( array(), $wp->request ) );
    if( !empty( $url ) ) {
        return chwebr_sanitize_url( $url );
    }
}

/**
 * Sanitize url and remove chwebsocialshare specific url parameters
 * 
 * @param string $url
 * @return string $url
 */
function chwebr_sanitize_url( $url ) {
    if( empty( $url ) ) {
        return "";
    }

    $url1 = str_replace( '?chwebr-refresh', '', $url );
    $url2 = str_replace( '&chwebr-refresh', '', $url1 );
    $url3 = str_replace( '%26chwebr-refresh', '', $url2 );
    
    return $url3;
}
