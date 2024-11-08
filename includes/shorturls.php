<?php

/**
 * Shorturl functions
 *
 * @package     CHWEBR
 * @subpackage  Functions/Shorturls
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Check if Google API Key is working properly
 * 
 * @since 3.0.0
 * @return string
 */

function chwebr_check_google_apikey() {

    global $chwebr_options;
    $appid = isset( $chwebr_options['google_app_id'] ) ? $appid = $chwebr_options['google_app_id'] : $appid = '';

    if( function_exists( 'curl_init' ) ) {
        $shorturl = new chwebr_google_shorturl( $appid );
        $statusArr = $shorturl->checkApiKey( 'http://www.google.de' );
    }

    isset( $statusArr['error']['errors'][0]['reason'] ) ? $statusArr['error']['errors'][0]['reason'] : $statusArr['error']['errors'][0]['reason'] = '';

    if( !empty( $statusArr['error']['errors'][0]['reason'] ) ) {
        return '<strong style="color:red;font-weight:bold;"> Notice: </strong>' . $statusArr['error']['errors'][0]['reason'];
    }
}

/*
 * Check if Bitly API Key is working properly
 * 
 * @since 3.0.0
 * @return mixed string | bool false when key is invalid
 */

function chwebr_check_bitly_apikey() {
    global $chwebr_options;

    $bitly_access_token = isset( $chwebr_options['bitly_access_token'] ) ? $bitly_access_token = $chwebr_options['bitly_access_token'] : '';
    // url to check
    $url = "http://www.google.de";
    
        $params = array();
        $params['access_token'] = $bitly_access_token;
        $params['longUrl'] = $url;
        //$results = bitly_get('link/lookup', $params);
        $bitly = new chwebr_bitly_shorturl();
        $results = $bitly->bitly_get( 'shorten', $params );

        if( !empty( $results['data']['url'] ) ) {
            return $results['data']['url'];
        } else {
            // Error
            return false;
        }

}

/**
 * Get shorturls from the post meta
 * 
 * @global array $post
 * @return string
 */
function chwebr_get_shorturl_singular( $url ) {
    global $chwebr_options, $post, $chwebr_custom_url;
    
    
    // no shorturl
    if( isset( $chwebr_options['chwebsu_methods'] ) && $chwebr_options['chwebsu_methods'] === 'disabled' ) {
        return $url;
    }
    
    // Use native WP Shortlinks | only when $chwebr_custom_url is empty. WP Shortlinks are not possible for non wordpress urls like www.google.com
    if( isset( $chwebr_options['chwebsu_methods'] ) && $chwebr_options['chwebsu_methods'] === 'wpshortlinks' && empty($chwebr_custom_url) ) {
        return wp_get_shortlink();
    }

    
    $shorturl = "";

    // Force cache rebuild
    if( chwebr_force_cache_refresh() ) {
        
        CHWEBR()->logger->info( 'chwebr_get_shorturl_singular() -> refresh cache' );

        // bitly shortlink
        if( isset( $chwebr_options['chwebsu_methods'] ) && $chwebr_options['chwebsu_methods'] === 'bitly' ) {
            $shorturl = chwebr_get_bitly_link( $url );
            CHWEBR()->logger->info('create shorturl singular: ' . $url . ' ' . $shorturl);
        }

        // Google shortlink
        if( isset( $chwebr_options['chwebsu_methods'] ) && $chwebr_options['chwebsu_methods'] === 'google' ) {
            $shorturl = chwebr_get_google_link( $url );
        }
        update_post_meta( $post->ID, 'chwebr_shorturl', $shorturl );
    } else {
        $shorturl = get_post_meta( $post->ID, 'chwebr_shorturl', true );
    }

    if( !empty( $shorturl ) && empty($chwebr_custom_url)) {
        return $shorturl;
    } else {
        return $url;
    }
}

/*
 * Get URL shortened URL
 * 
 * @since 1.0.0
 * @param $url
 * @return string
 */

function chwebr_get_shortened_url( $url ) {
    global $chwebr_options;

    // Return empty
    if( empty( $url ) ) {
        return "";
    }

    // Use native WP Shortlinks
    if( $chwebr_options['chwebsu_methods'] === 'wpshortlinks' ) {
        return wp_get_shortlink();
    }

    // If is_singular post store shorturl in custom post fields 
    // and return it from there so user has the power to change values 
    // on his own from the post edit page and the custom fields editor
    if( is_singular() ) {
        return chwebr_get_shorturl_singular( $url );
    }

    // Force cache rebuild
    if( chwebr_force_cache_refresh() ) {

        CHWEBR()->logger->info( 'chwebr_get_shorturl() -> refresh cache' );

        // bitly shortlink
        if( isset( $chwebr_options['chwebsu_methods'] ) && $chwebr_options['chwebsu_methods'] === 'bitly' ) {
            $shorturl = chwebr_get_bitly_link( $url );
            CHWEBR()->logger->info('create shorturl: ' . $url . ' ' . $shorturl);
        }

        // Google shortlink
        if( isset( $chwebr_options['chwebsu_methods'] ) && $chwebr_options['chwebsu_methods'] === 'google' ) {
            $shorturl = chwebr_get_google_link( $url );
        }

        // Get expiration time
        $expiration = chwebr_get_expiration();
        set_transient( 'chweb_url_' . md5( $url ), $shorturl, $expiration );
    } else {
        $shorturl = get_transient( 'chweb_url_' . md5( $url ) );
    }

    if( !empty( $shorturl ) ) {
        return $shorturl;
    } else {
        return $url;
    }
}

/**
 * Get Google short url's (result: goo.gl)
 * 
 * @param string $url
 * @return string
 */
function chwebr_get_google_link( $url ) {
    global $chwebr_options;

    $google_app_id = isset( $chwebr_options['google_app_id'] ) ? $chwebr_options['google_app_id'] : '';

    // get result and fill cache
    if( !empty( $google_app_id ) && !empty( $url ) ) {
        $shorturl = new chwebr_google_shorturl( $google_app_id );
        $shorturlres = $shorturl->shorten( $url, false );

        if( empty( $shorturlres ) ) {
            return $url;
        }

        unset( $shorturl );
        return $shorturlres;
    }
    return $url;
}

/*
 * Get shorturl generated by bitly
 * 
 * @return string
 */

function chwebr_get_bitly_link( $url ) {
    global $chwebr_options;

    $bitly_access_token = isset( $chwebr_options['bitly_access_token'] ) ? $bitly_access_token = $chwebr_options['bitly_access_token'] : '';

    if( !empty( $bitly_access_token ) && !empty( $url ) && !is_null( $url ) ) {

        $params = array();
        $params['access_token'] = $bitly_access_token;
        $params['longUrl'] = $url;
        //$results = bitly_get('link/lookup', $params);
        $bitly = new chwebr_bitly_shorturl();
        $results = $bitly->bitly_get( 'shorten', $params );

        if( !empty( $results['data']['url'] ) ) {
            return $results['data']['url'];
        }
    }
    return $url;
}
