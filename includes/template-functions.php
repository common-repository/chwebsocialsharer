<?php

/**
 * Template Functions
 *
 * @package     CHWEBR
 * @subpackage  Functions/Templates
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;


/* Load Hooks
 * @since 2.0
 * return void
 */

add_shortcode( 'chwebsocialshare', 'chwebsocialshareShortcodeShow' );
add_filter( 'the_content', 'chwebsocialshare_filter_content', getExecutionOrder(), 1 );
add_filter( 'widget_text', 'do_shortcode' );
add_action( 'chwebsocialshare', 'chwebsocialshare' );
add_filter( 'chweb_share_title', 'chwebr_get_title', 10, 2 );


/* Get Execution order of injected Share Buttons in $content 
 *
 * @since 2.0.4
 * @return int
 */

function getExecutionOrder() {
    global $chwebr_options;
    isset( $chwebr_options['execution_order'] ) && is_numeric( $chwebr_options['execution_order'] ) ? $priority = trim( $chwebr_options['execution_order'] ) : $priority = 1000;
    return $priority;
}

/* 
 * Get chwebrShareObject 
 * depending on ChwebEngine (or sharedcount.com deprecated) is used
 * 
 * @since 2.0.9
 * @return object
 * @changed 3.1.8
 */

function chwebrGetShareObj( $url ) {
        if( !class_exists( 'RollingCurlX' ) ){
            require_once CHWEBR_PLUGIN_DIR . 'includes/libraries/RolingCurlX.php';
        }
        if( !class_exists( 'chwebengine' ) ){
            require_once(CHWEBR_PLUGIN_DIR . 'includes/chwebengine.php');
        }

        //chwebdebug()->info( 'chwebrGetShareObj() url: ' . $url );
        $chwebrSharesObj = new chwebengine( $url );
        return $chwebrSharesObj;

}

/*
 * Use the correct share method depending on chwebsocialshare networks enabled or not
 * 
 * @since 2.0.9
 * @returns int share count
 */

function chwebrGetShareMethod( $chwebrSharesObj ) {
    if( class_exists( 'ChwebsocialshareNetworks' ) ) {
        $chwebrShareCounts = $chwebrSharesObj->getAllCounts();
        return $chwebrShareCounts;
    }
    $chwebrShareCounts = $chwebrSharesObj->getFBTWCounts();
    return $chwebrShareCounts;
}

/**
 * Get share count for all non singular pages where $post is empty or a custom url is used E.g. category or blog list pages or for shortcodes
 * Uses transients 
 * 
 * @param string $url
 *  
 * @returns integer $shares
 */
function chwebrGetNonPostShares( $url ) {
    global $chwebr_error;
    
    
    // Expiration
    $expiration = chwebr_get_expiration();
    
    // Remove variables, parameters and trailingslash
    $url_clean = chwebr_sanitize_url( $url );

    // Get any existing copy of our transient data and fill the cache
    if( chwebr_force_cache_refresh() ) {
        
        // Its request limited
        if ( chwebr_is_req_limited() ){
            $shares = get_transient( 'chwebcount_' . md5( $url_clean ) );
            if( isset( $shares ) && is_numeric( $shares ) ) {
                CHWEBR()->logger->info( 'chwebrGetNonPostShares() get shares from get_transient. URL: ' . $url_clean . ' SHARES: ' . $shares );
                return $shares + getFakecount();
            } else {
                return 0 + getFakecount(); // we need a result
            }
        }

        // Regenerate the data and save the transient
        // Get the share Object
        $chwebrSharesObj = chwebrGetShareObj( $url_clean );
        // Get the share counts object
        $chwebrShareCounts = chwebrGetShareMethod( $chwebrSharesObj );

        // Set the transient and return shares
        set_transient( 'chwebcount_' . md5( $url_clean ), $chwebrShareCounts->total, $expiration );
        CHWEBR()->logger->info( 'chwebrGetNonPostShares set_transient - shares:' . $chwebrShareCounts->total . ' url: ' . $url_clean );
        return $chwebrShareCounts->total + getFakecount();
    } else {
        // Get shares from transient cache
        
        $shares = get_transient( 'chwebcount_' . md5( $url_clean ) );

        if( isset( $shares ) && is_numeric( $shares ) ) {
            CHWEBR()->logger->info( 'chwebrGetNonPostShares() get shares from get_transient. URL: ' . $url_clean . ' SHARES: ' . $shares );
            return $shares + getFakecount();
        } else {
            return 0 + getFakecount(); // we need a result
        }
    }
}


/*
 * Return the share count
 * 
 * @param string url of the page the share count is collected for
 * @returns int
 */

function getSharedcount( $url ) {
    global $chwebr_options, $post, $chwebr_sharecount, $chwebr_error; // todo test a global share count var if it reduces the amount of requests
    
    $chwebr_error[] = 'ChwebSocialShare: Trying to get share count';
        
    // Return global share count variable to prevent multiple execution
    if (is_array($chwebr_sharecount) && array_key_exists($url, $chwebr_sharecount) && !empty($chwebr_sharecount[$url]) && !chwebr_is_cache_refresh() ){
        return $chwebr_sharecount[$url] + getFakecount();
    }
   
    
    // Remove chwebr-refresh query parameter
    $url = chwebr_sanitize_url($url);
    
    /*
     * Deactivate share count on:
     * - 404 pages
     * - search page
     * - empty url
     * - disabled permalinks
     * - disabled share count setting
     * - deprecated: admin pages (we need to remove this for themes which are using a bad infinite scroll implementation where is_admin() is always true)
     */

       
    if( is_404() || is_search() || empty($url) || !chwebr_is_enabled_permalinks() || isset($chwebr_options['disable_sharecount']) ) {
        $chwebr_error[] = 'ChwebSocialShare: Trying to get share count deactivated';
        return apply_filters( 'filter_get_sharedcount', 0 );
    }

    /* 
     * Return share count on non singular pages when url is defined
       Possible: Category, blog list pages, non singular() pages. This store the shares in transients with chwebrGetNonPostShares();
     */


    if( !empty( $url ) && is_null( $post ) ) {
        $chwebr_error[] = 'ChwebSocialShare: URL or POST is empty. Return share count with chwebrGetNonPostShares';
        return apply_filters( 'filter_get_sharedcount', chwebrGetNonPostShares( $url ) );
    }

    /*
     * Refresh Cache
     */
    if( chwebr_force_cache_refresh() && is_singular() ) {
        
        $chwebr_error[] = 'ChwebSocialShare: Force Cache Refresh on singular()';
        
        // Its request limited
        if ( chwebr_is_req_limited() ){ 
            $chwebr_error[] = 'ChwebSocialShare: rate limit reached. Return Share from custom meta option';
            return get_post_meta( $post->ID, 'chwebr_shares', true ) + getFakecount();
        }

        // free some memory
        unset ( $chwebr_sharecount[$url] );
        
        // Write timestamp (Use this on top of this condition. If this is not on top following return statements will be skipped and ignored - possible bug?)
        update_post_meta( $post->ID, 'chwebr_timestamp', time() );

        CHWEBR()->logger->info( 'Refresh Cache: Update Timestamp: ' . time() );

        // Get the share Object
        $chwebrSharesObj = chwebrGetShareObj( $url );
        // Get the share count Method
        $chwebrShareCounts = chwebrGetShareMethod( $chwebrSharesObj );
        // Get stored share count
        $chwebrStoredShareCount = get_post_meta( $post->ID, 'chwebr_shares', true );

        // Create global sharecount
        $chwebr_sharecount = array($url => $chwebrShareCounts->total);
        /*
         * Update post_meta only when API is requested and
         * API share count is greater than real fresh requested share count ->
         */
        
        if( $chwebrShareCounts->total >= $chwebrStoredShareCount ) {
            update_post_meta( $post->ID, 'chwebr_shares', $chwebrShareCounts->total );
            update_post_meta( $post->ID, 'chwebr_jsonshares', json_encode( $chwebrShareCounts ) );
            CHWEBR()->logger->info( "Refresh Cache: Update database with share count: " . $chwebrShareCounts->total );
            
            /* return counts from getAllCounts() after DB update */
            return apply_filters( 'filter_get_sharedcount', $chwebrShareCounts->total + getFakecount() );
        }
        
        /* return previous counts from DB Cache | this happens when API has a hiccup and does not return any results as expected */
        return apply_filters( 'filter_get_sharedcount', $chwebrStoredShareCount + getFakecount() );
    } else {
        // Return cached results
        $cachedCountsMeta = get_post_meta( $post->ID, 'chwebr_shares', true );
        $cachedCounts = $cachedCountsMeta + getFakecount();
        CHWEBR()->logger->info( 'Cached Results: ' . $cachedCounts . ' url:' . $url );
        return apply_filters( 'filter_get_sharedcount', $cachedCounts );
    }
}

function chwebr_subscribe_button() {
    global $chwebr_options;
    if( $chwebr_options['networks'][2] ) {
        $subscribebutton = '<a href="javascript:void(0)" class="chwebicon-subscribe" id="chweb-subscribe-control"><span class="icon"><span class="text">' . __( 'Subscribe', 'chwebr' ) . '</span></span></a>';
    } else {
        $subscribebutton = '';
    }
    return apply_filters( 'chwebr_filter_subscribe_button', $subscribebutton );
}

/* Put the Subscribe container under the share buttons
 * @since 2.0.0.
 * @return string
 */

function chwebr_subscribe_content() {
    global $chwebr_options;
    if( isset( $chwebr_options['networks'][2] ) && isset( $chwebr_options['subscribe_behavior'] ) && $chwebr_options['subscribe_behavior'] === 'content' ) { //Subscribe content enabled
        $container = '<div class="chwebr-toggle-container">' . chwebr_cleanShortcode( 'chwebsocialshare', $chwebr_options['subscribe_content'] ) . '</div>';
    } else {
        $container = '';
    }
    return apply_filters( 'chwebr_toggle_container', $container );
}

/* Check if [chwebsocialshare] shortcode is used in subscribe field and deletes it
 * Prevents infinte loop
 * 
 * @since 2.0.9
 * @return string / shortcodes parsed
 */

function chwebr_cleanShortcode( $code, $content ) {
    global $shortcode_tags;
    $stack = $shortcode_tags;
    $shortcode_tags = array($code => 1);
    $content = strip_shortcodes( $content );
    $shortcode_tags = $stack;

    return do_shortcode( $content );
}

/* Round the totalshares
 * 
 * @since 1.0
 * @param $totalshares int
 * @return string
 */

function roundshares( $totalshares ) {
    if( $totalshares > 1000000 ) {
        $totalshares = round( $totalshares / 1000000, 1 ) . 'M';
    } elseif( $totalshares > 1000 ) {
        $totalshares = round( $totalshares / 1000, 1 ) . 'k';
    }
    return apply_filters( 'get_rounded_shares', $totalshares );
}

/* Return the more networks button
 * @since 2.0
 * @return string
 */

function onOffSwitch() {
    global $chwebr_options;
    
    // Get class names for buttons size
    $class_size = isset($chwebr_options['buttons_size']) ? ' ' . $chwebr_options['buttons_size'] : '';
    
    // Get class names for button style
    $class_style = isset($chwebr_options['chweb_style']) && $chwebr_options['chweb_style'] === 'shadow' ? ' chwebr-shadow' : '';
    
    $output = '<div class="onoffswitch' . $class_size . $class_style . '"></div>';
    return apply_filters( 'chwebsh_onoffswitch', $output );
}

/* Return the second more networks button after 
 * last hidden additional service. initial status: hidden
 * Become visible with click on plus icon
 * 
 * @since 2.0
 * @return string
 */

function onOffSwitch2() {
    global $chwebr_options;
    
// Get class names for buttons size
    $class_size = isset($chwebr_options['buttons_size']) ? ' ' . $chwebr_options['buttons_size'] : '';
    
    // Get class names for button style
    $class_style = isset($chwebr_options['chweb_style']) && $chwebr_options['chweb_style'] === 'shadow' ? ' chwebr-shadow' : '';
    
    $output = '<div class="onoffswitch2' .$class_size . $class_style . '" style="display:none;"></div>';
    return apply_filters( 'chwebsh_onoffswitch2', $output );
}

/*
 * Delete all services from array which are not enabled
 * @since 2.0.0
 * @return callback
 */

function isStatus( $var ) {
    return (!empty( $var["status"] ));
}

/*
 * Array of all available network share urls
 * 
 * @param string $name id of the network
 * @param bool $is_shortcode true when function is used in shortcode [chwebsocialshare]
 * 
 * @since 2.1.3
 * @return string
 */

function arrNetworks( $name, $is_shortcode ) {
    global $chwebr_custom_url, $chwebr_custom_text, $chwebr_twitter_url;

    if( $is_shortcode ) {
        $url = !empty( $chwebr_custom_url ) ? urlencode($chwebr_custom_url) : urlencode(chwebr_get_url());
        $title = !empty( $chwebr_custom_text ) ? $chwebr_custom_text : chwebr_get_title();
        $twitter_title = !empty( $chwebr_custom_text ) ? $chwebr_custom_text : chwebr_get_twitter_title();
    }
    if( !$is_shortcode ) {
        $url = urlencode(chwebr_get_url());
        $title = chwebr_get_title();
        $twitter_title = chwebr_get_twitter_title();
    }

    $via = chwebr_get_twitter_username() ? '&via=' . chwebr_get_twitter_username() : '';
    
     $networks_arr = array(
	    'facebook' => 'http://www.facebook.com/sharer.php?u=' . $url,
        'twitter' => 'https://twitter.com/intent/tweet?text=' . $twitter_title . '&url=' . $chwebr_twitter_url . $via,
        'subscribe' => '#',
        'url' => $url,
        'title' => $title,
        'google' => 'https://plus.google.com/share?text=' . $title . '&amp;url=' . $url,
        'pinterest' => 'https://pinterest.com/pin/create/button/?url=' . $url . '&amp;media=' . urlencode(chwebnet_get_pinterest_image()) . '&amp;description=' . urlencode(chwebnet_get_pinterest_desc()),
        'digg' => 'http://digg.com/submit?phase=2%20&amp;url=' . $url . '&amp;title=' . $title,
        'linkedin' => 'https://www.linkedin.com/shareArticle?trk=' . $title . '&amp;url=' . $url,
        'linkedin ' => 'https://www.linkedin.com/shareArticle?trk=' . $title . '&amp;url=' . $url, // Blank character fix ()
        'reddit' => 'http://www.reddit.com/submit?url=' . $url . '&amp;title=' . $title, 
        'reddit ' => 'http://www.reddit.com/submit?url=' . $url . '&amp;title=' . $title, // Blank character fix ()
        'stumbleupon' => 'http://www.stumbleupon.com/submit?url=' . $url,
        'stumbleupon ' => 'http://www.stumbleupon.com/submit?url=' . $url, // Blank character fix ()
        'vk' => 'http://vkontakte.ru/share.php?url=' . $url . '&amp;item=' . $title,
        'print' => 'http://www.printfriendly.com/print/?url=' . $url . '&amp;item=' . $title,
        'delicious' => 'https://delicious.com/save?v=5&amp;noui&amp;jump=close&amp;url=' . $url . '&amp;title=' . $title,
        'buffer' => 'https://bufferapp.com/add?url=' . $url . '&amp;text=' . $title,
        'weibo' => 'http://service.weibo.com/share/share.php?url=' . $url . '&amp;title=' . $title,
        'pocket' => 'https://getpocket.com/save?title=' . $title . '&amp;url=' . $url,
        'xing' => 'https://www.xing.com/social_plugins/share?h=1;url=' . $url . '&amp;title=' . $title,
        'tumblr' => 'https://www.tumblr.com/share?v=3&amp;u='. $url . '&amp;t=' . $title,
        'mail' => 'mailto:?subject=' . $subject . '&amp;body=' . $body . $url,
        'meneame' => 'http://www.meneame.net/submit.php?url=' . $url . '&amp;title=' . $title,
        'odnoklassniki' => 'http://www.odnoklassniki.ru/dk?st.cmd=addShare&amp;st.s=1&amp;st._surl=' . $url . '&amp;title=' . $title,
        'managewp' => 'http://managewp.org/share/form?url=' . $url . '&amp;title=' . $title,
        'mailru' => 'http://connect.mail.ru/share?share_url=' . $url,
        'line' => 'http://line.me/R/msg/text/?' . $title .'%20'. $url,
        'yummly' => 'http://www.yummly.com/urb/verify?url=' . $url . '&amp;title=' . $title,
        'frype' => 'http://www.draugiem.lv/say/ext/add.php?title='. $title .'&amp;link='.$url,
        'skype' => 'https://web.skype.com/share?url='.$url.'&lang=en-en',
        'telegram' => 'https://telegram.me/share/url?url='.$url.'&text=' . $title,
        'flipboard' => 'https://share.flipboard.com/bookmarklet/popout?v=2&title=' . urlencode($title) . '&url=' . $url,
        'hackernews' => 'http://news.ycombinator.com/submitlink?u='.$url.'&t='.urlencode($title),
        );
    
    
    // Delete custom text
    unset ($chwebr_custom_text);
    // Delete custom url 
    unset ($chwebr_custom_url);

    $networks = apply_filters( 'chwebr_array_networks', $networks_arr );
    return isset( $networks[$name] ) ? $networks[$name] : '';
}
/**
 * Get Pinterest image
 * 
 * @global obj $chwebr_meta_tags
 * @return string
 */
function chwebnet_get_pinterest_image() {
    global $post, $chwebr_meta_tags;
    if( is_singular() && class_exists( 'CHWEBR_HEADER_META_TAGS' ) && method_exists($chwebr_meta_tags, 'get_pinterest_image_url') ) {
        $image =  $chwebr_meta_tags->get_pinterest_image_url();
    }else{
        $image = function_exists( 'MASHOG' ) ? MASHOG()->MASHOG_OG_Output->_add_image() : chwebr_get_image( $post->ID );
    }
    return $image;
}
/**
 * Get Pinterest description
 * 
 * @global obj $chwebr_meta_tags
 * @return type
 */
function chwebnet_get_pinterest_desc() {
    global $post, $chwebr_meta_tags;
    if( is_singular() && class_exists( 'CHWEBR_HEADER_META_TAGS' ) && method_exists($chwebr_meta_tags, 'get_pinterest_description') ) {
        global $chwebr_meta_tags;
        return $chwebr_meta_tags->get_pinterest_description();
    } else{
        return chwebr_get_excerpt_by_id($post);
    }
}
/* Returns all available networks
 * 
 * @since 2.0
 * @param bool true when used from shortcode [chwebsocialshare]
 * @param int number of visible networks
 * @return string html
 */

function chwebr_getNetworks( $is_shortcode = false, $services = 0 ) {
    global $chwebr_options, $chwebr_custom_url, $enablednetworks, $chwebr_twitter_url;
    
    
    // define globals
    if( $is_shortcode ) {
        $chwebr_twitter_url = !empty( $chwebr_custom_url ) ? chwebr_get_shorturl( $chwebr_custom_url ) : chwebr_get_twitter_url();

    }else{
        $chwebr_twitter_url = chwebr_get_twitter_url();
    }
    
    // Get class names for buttons size
    $class_size = isset($chwebr_options['buttons_size']) ? ' ' . $chwebr_options['buttons_size'] : '';
    
    // Get class names for buttons margin
    $class_margin = isset($chwebr_options['button_margin']) ? '' : ' chweb-nomargin';

    // Get class names for center align
    $class_center = isset($chwebr_options['text_align_center']) ? ' chweb-center' : '';
    
    // Get class names for button style
    $class_style = isset($chwebr_options['chweb_style']) && $chwebr_options['chweb_style'] === 'shadow' ? ' chwebr-shadow' : '';

    $output = '';
    $startsecondaryshares = '';
    $endsecondaryshares = '';

    /* content of 'more services' button */
    $onoffswitch = '';

    /* counter for 'Visible Services' */
    $startcounter = 1;

    $maxcounter = isset( $chwebr_options['visible_services'] ) ? $chwebr_options['visible_services'] : 0;
    $maxcounter = ($maxcounter === 'all') ? 'all' : ($maxcounter + 1); // plus 1 to get networks correct counted (array's starting counting from zero)
    $maxcounter = apply_filters( 'chwebr_visible_services', $maxcounter );

    /* Overwrite maxcounter with shortcode attribute */
    $maxcounter = ($services === 0) ? $maxcounter : $services;

    /* our list of available services, includes the disabled ones! 
     * We have to clean this array first!
     */
    $getnetworks = isset( $chwebr_options['networks'] ) ? $chwebr_options['networks'] : '';
    //echo '<pre>'.var_dump($getnetworks) . '</pre>';

    /* Delete disabled services from array. Use callback function here. Do this only once because array_filter is slow! 
     * Use the newly created array and bypass the callback function
     */
    if( is_array( $getnetworks ) ) {
        if( !is_array( $enablednetworks ) ) {
            $enablednetworks = array_filter( $getnetworks, 'isStatus' );
        } else {
            $enablednetworks = $enablednetworks;
        }
    } else {
        $enablednetworks = $getnetworks;
    }
    
    // Start Primary Buttons
    //$output .= '<div class="chwebr-primary-shares">';
    
    if( !empty( $enablednetworks ) ) {
        foreach ( $enablednetworks as $key => $network ):
                
            if( $maxcounter !== 'all' && $maxcounter < count( $enablednetworks ) ) { // $maxcounter + 1 for correct comparision with count()
                if( $startcounter == $maxcounter ) {
                    $onoffswitch = onOffSwitch(); // Start More Button
                    //$startsecondaryshares = '</div>'; // End Primary Buttons
                    $startsecondaryshares .= '<div class="secondary-shares" style="display:none;">'; // Start secondary-shares
                } else {
                    $onoffswitch = '';
                    $onoffswitch2 = '';
                    $startsecondaryshares = '';
                }
                if( $startcounter === (count( $enablednetworks )) ) {
                    $endsecondaryshares = '</div>';
                } else {
                    $endsecondaryshares = '';
                }
            }
            //if( $enablednetworks[$key]['name'] != '' ) {
            if( isset($enablednetworks[$key]['name']) && !empty($enablednetworks[$key]['name']) ) {
                /* replace all spaces with $nbsp; This prevents error in css style content: text-intend */
                $name = preg_replace( '/\040{1,}/', '&nbsp;', $enablednetworks[$key]['name'] ); // The custom share label
            } else {
                $name = ucfirst( $enablednetworks[$key]['id'] ); // Use the id as share label. Capitalize it!
            }
            
            $enablednetworks[$key]['id'] == 'whatsapp' ? $display = 'style="display:none;"' : $display = ''; // Whatsapp button is made visible via js when opened on mobile devices

            // Lets use the data attribute to prevent that pininit.js is overwriting our pinterest button - PR: https://secure.helpscout.net/conversation/257066283/954/?folderId=924740
            if ('pinterest' === $enablednetworks[$key]['id'] && !chwebr_is_amp_page() ) {
                $output .= '<a ' . $display . ' class="chwebicon-' . $enablednetworks[$key]['id'] . $class_size . $class_margin . $class_center . $class_style . '" href="#" data-chwebr-url="'. arrNetworks( $enablednetworks[$key]['id'], $is_shortcode ) . '" target="_blank" rel="nofollow"><span class="icon"></span><span class="text">' . $name . '</span></a>';
            } else {
                $output .= '<a ' . $display . ' class="chwebicon-' . $enablednetworks[$key]['id'] . $class_size . $class_margin . $class_center . $class_style . '" href="' . arrNetworks( $enablednetworks[$key]['id'], $is_shortcode ) . '" target="_blank" rel="nofollow"><span class="icon"></span><span class="text">' . $name . '</span></a>';
            }
            $output .= $onoffswitch;
            $output .= $startsecondaryshares;

            $startcounter++;
        endforeach;
        $output .= onOffSwitch2();
        $output .= $endsecondaryshares;
    }

    return apply_filters( 'return_networks', $output );
}

/*
 * Render template
 * Returns share buttons and share count
 * 
 * @since 1.0
 * @returns string html
 */

function chwebsocialshareShow() {
    global $chwebr_options;
    
    $class_stretched = isset($chwebr_options['responsive_buttons']) ? 'chwebr-stretched' : '';

    $return = '<aside class="chwebr-container chwebr-main ' . $class_stretched . '">'
            . chwebr_content_above() .
            '<div class="chwebr-box">'
                . apply_filters( 'chwebr_sharecount_filter', chwebr_render_sharecounts() ) .
                '<div class="chwebr-buttons">'
                . chwebr_getNetworks() .
                '</div>
            </div>
                <div style="clear:both;"></div>'
            . chwebr_subscribe_content()
            . chwebr_content_below() .
            '</aside>
            <!-- Share buttons by chaudharyweb.com - Version: ' . CHWEBR_VERSION . '-->';
    return apply_filters( 'chwebr_output_buttons', $return );
}

/**
 * Render the sharecount template
 * 
 * @param string $customurl default empty
 * @param string alignment default left
 * @return string html
 */
function chwebr_render_sharecounts( $customurl = '', $align = 'left' ) {
    global $chwebr_options;

    if( isset( $chwebr_options['disable_sharecount'] ) || !chwebr_curl_installed() || !chwebr_is_enabled_permalinks() ) {
        return;
    }

    $url = empty( $customurl ) ? chwebr_get_url() : $customurl;
    $sharetitle = isset( $chwebr_options['sharecount_title'] ) ? $chwebr_options['sharecount_title'] : __( 'SHARES', 'chwebr' );

    $shares = getSharedcount( $url );
    $sharecount = isset( $chwebr_options['chwebsocialsharer_round'] ) ? roundshares( $shares ) : $shares;

    // do not show shares after x shares
    if( chwebr_hide_shares( $shares ) ) {
        return;
    }
    
    // Get class names for buttons size
    $class_size = isset($chwebr_options['buttons_size']) ? ' ' . $chwebr_options['buttons_size'] : '';

    $html = '<div class="chwebr-count'.$class_size . '" style="float:' . $align . ';"><div class="counts chwebrcount">' . $sharecount . '</div><span class="chwebr-sharetext">' . $sharetitle . '</span></div>';
    return apply_filters('chwebr_share_count', $html);
}

/*
 * Shortcode function
 * Select Share count from database and returns share buttons and share counts
 * 
 * @since 1.0
 * @returns string
 */

function chwebsocialshareShortcodeShow( $args ) {
    global $chwebr_options, $chwebr_custom_url, $chwebr_custom_text;

    $sharecount = '';

    //Filter shortcode args to add an option for developers to change (add) some args
    apply_filters( 'chwebr_shortcode_atts', $args );

    extract( shortcode_atts( array(
        'cache' => '3600',
        'shares' => 'true',
        'buttons' => 'true',
        'services' => '0', //default is by admin option - plus 1 because array starts counting from zero
        'align' => 'left',
        'text' => '', // $text
        'url' => '' // $url
                    ), $args ) );
    
    // Visible services
    $count_services = !empty($services) ? $services : 0;
    
    // Define custom url var to share
    //$chwebr_custom_url = empty( $url ) ? chwebr_get_url() : $url;
    // The global available custom url to share
    $chwebr_custom_url = !empty( $url ) ? $url : '';
    // local url
    $chwebr_url = empty( $url ) ? chwebr_get_url() : $url;

    // Define custom text to share
    $chwebr_custom_text = !empty( $text ) ? $text : false;

    if( $shares != 'false' ) {
        $sharecount = chwebr_render_sharecounts( $chwebr_url, $align );
        // shortcode [chwebsocialshare shares="true" buttons="false"] 
        if( $shares === "true" && $buttons === 'false' ) {
            return $sharecount;
        }
    }
    
    $class_stretched = isset($chwebr_options['responsive_buttons']) ? 'chwebr-stretched' : '';

    $return = '<aside class="chwebr-container chwebr-main ' . $class_stretched . '">'
            . chwebr_content_above() .
            '<div class="chwebr-box">'
            . $sharecount .
            '<div class="chwebr-buttons">'
            . chwebr_getNetworks( true, $count_services ) .
            '</div></div>
                    <div style="clear:both;"></div>'
            . chwebr_subscribe_content()
            . chwebr_content_below() .
            '</aside>
            <!-- Share buttons made by chaudharyweb.com - Version: ' . CHWEBR_VERSION . '-->';

    return apply_filters( 'chwebr_output_buttons', $return );
}

/* Returns active status of Chwebsocialshare.
 * Used for scripts.php $hook
 * @since 2.0.3
 * @return bool True if CHWEBR is enabled on specific page or post.
 * @TODO: Check if shortcode [chwebsocialshare] is used in widget
 */

function chwebrGetActiveStatus() {
    global $chwebr_options, $post;

    $frontpage = isset( $chwebr_options['frontpage'] ) ? true : false;
    $current_post_type = get_post_type();
    $enabled_post_types = isset( $chwebr_options['post_types'] ) ? $chwebr_options['post_types'] : array();
    $singular = isset( $chwebr_options['singular'] ) ? true : false;
    $loadall = isset( $chwebr_options['loadall'] ) ? $loadall = true : $loadall = false;


    if( chwebr_is_excluded() ) {
        chwebdebug()->info( "chwebr_is_excluded()" );
        return apply_filters( 'chwebr_active', false );
    }

    if( $loadall ) {
        chwebdebug()->info( "load all chwebr scripts" );
        return apply_filters( 'chwebr_active', true );
    }

    // Load on frontpage
    if( $frontpage === true && is_front_page() ) {
        chwebdebug()->info( "allow frontpage and is frontpage" );
        return apply_filters( 'chwebr_active', true );
    }

    // Load scripts when shortcode is used
    /* Check if shortcode is used */
    if( function_exists( 'has_shortcode' ) && is_object( $post ) && has_shortcode( $post->post_content, 'chwebsocialshare' ) ) {
        chwebdebug()->info( "has_shortcode" );
        return apply_filters( 'chwebr_active', true );
    }

    // No scripts on non singular page
    if( !is_singular() && !$singular ) {
        chwebdebug()->info( "No scripts on non singular page" );
        return apply_filters( 'chwebr_active', false );
    }


    // Load scripts when post_type is defined (for automatic embeding)
    if( in_array( $current_post_type, $enabled_post_types ) ) {
        chwebdebug()->info( "automatic post_type enabled" );
        return apply_filters( 'chwebr_active', true );
    }

    chwebdebug()->info( "chwebrGetActiveStatus false" );
    return apply_filters( 'chwebr_active', false );
}

/**
 * Get the post meta value of position
 * 
 * @global int $post
 * @return mixed string|bool false
 */
function chwebr_get_post_meta_position() {
    global $post;
    
    if( isset( $post->ID ) && !empty($post->ID) ) {
        $check_position_meta_post = get_post_meta( $post->ID, 'chwebr_position', true );
        if( !empty( $check_position_meta_post ) ) {
            return $check_position_meta_post;
        }else{
            return false;
        }
    }
    return false;
}

/* Returns Share buttons on specific positions
 * Uses the_content filter
 * @since 1.0
 * @return string
 */

function chwebsocialshare_filter_content( $content ) {
    global $chwebr_options, $post, $wp_current_filter, $wp;
    
    // Default position
    $position = !empty( $chwebr_options['chwebsocialsharer_position'] ) ? $chwebr_options['chwebsocialsharer_position'] : '';
    // Check if we have a post meta setting which overrides the global position than we use that one instead
    if ( true == ($position_meta = chwebr_get_post_meta_position() ) ){
        $position = $position_meta;
    }

    
    $enabled_post_types = isset( $chwebr_options['post_types'] ) ? $chwebr_options['post_types'] : null;
    $current_post_type = get_post_type();
    $frontpage = isset( $chwebr_options['frontpage'] ) ? true : false;
    $excluded = isset( $chwebr_options['excluded_from'] ) ? $chwebr_options['excluded_from'] : null;
    $singular = isset( $chwebr_options['singular'] ) ? $singular = true : $singular = false;

    
    if( isset($chwebr_options['is_main_query']) && !is_main_query() ) {
        return $content;
    }
     
    if( chwebr_is_excluded() ){
        return $content;
    }
    
    if (is_feed()){
        return $content;
    }

    if( $frontpage == false && is_front_page() ) {
        return $content;
    }

    if( !is_singular() == 1 && $singular !== true ) {
        return $content;
    }

    if( $enabled_post_types == null or ! in_array( $current_post_type, $enabled_post_types ) ) {
        return $content;
    }

    if( in_array( 'get_the_excerpt', $wp_current_filter ) ) {
        return $content;
    }
    
    // Get one instance (prevents multiple similar calls)
    $chwebr_instance = apply_filters('chwebr_the_content', chwebsocialshareShow());
    switch ( $position ) {
        case 'manual':
            break;

        case 'both':
            $content = $chwebr_instance . $content . $chwebr_instance;
            break;

        case 'before':
            $content = $chwebr_instance . $content;
            break;

        case 'after':
            $content .= $chwebr_instance;
            break;
        
        case 'disable':
            break;
    }
    return $content;
}

/* Template function chwebsocialshare() 
 * @since 2.0.0
 * @return string
 */

function chwebsocialshare() {
    //global $atts;
    echo chwebsocialshareShow();
}

/* Deprecated: Template function chwebsocialsharer()
 * @since 1.0
 * @return string
 */

function chwebsocialsharer() {
    //global $atts;
    echo chwebsocialshareShow();
}

/**
 * Get Thumbnail featured image if existed
 *
 * @since 1.0
 * @param int $postID
 * @return string
 */
function chwebr_get_image( $postID ) {
    global $post;

    if( !isset( $post ) ) {
        return '';
    }

    if( has_post_thumbnail( $post->ID ) ) {
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
        return $image[0];
    }
}

add_action( 'chwebr_get_image', 'chwebr_get_image' );

/**
 * Get the excerpt
 *
 * @since 1.0
 * @param int $postID
 * @changed 3.0.0
 * @return string
 */
function chwebr_get_excerpt_by_id( $post_id ) {
    // Check if the post has an excerpt
    if( has_excerpt() ) {
        return get_the_excerpt();
    }

    if( !isset( $post_id ) ) {
        return "";
    }

    $the_post = get_post( $post_id ); //Gets post ID

    /*
     * If post_content isn't set
     */
    if( !isset( $the_post->post_content ) ) {
        return "";
    }

    $the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
    $excerpt_length = 35; //Sets excerpt length by word count
    $the_excerpt = strip_tags( strip_shortcodes( $the_excerpt ) ); //Strips tags and images
    $words = explode( ' ', $the_excerpt, $excerpt_length + 1 );
    if( count( $words ) > $excerpt_length ) :
        array_pop( $words );
        array_push( $words, '…' );
        $the_excerpt = implode( ' ', $words );
    endif;
    $the_excerpt = '<p>' . $the_excerpt . '</p>';
    return wp_strip_all_tags( $the_excerpt );
}

add_action( 'chwebr_get_excerpt_by_id', 'chwebr_get_excerpt_by_id' );

/**
 * Create a factor for calculating individual fake counts 
 * based on the number of word within a page title
 *
 * @since 2.0
 * @return int
 */
function chwebr_get_fake_factor() {
    // str_word_count is not working for hebraic and arabic languages
    //$wordcount = str_word_count(the_title_attribute('echo=0')); //Gets title to be used as a basis for the count
    $wordcount = count( explode( ' ', the_title_attribute( 'echo=0' ) ) );
    $factor = $wordcount / 10;
    return apply_filters( 'chwebr_fake_factor', $factor );
}

/*
 * Sharecount fake number
 * 
 * @since 2.0.9
 * @return int
 * 
 */

function getFakecount() {
    global $chwebr_options, $wp;
    $fakecountoption = 0;
    if( isset( $chwebr_options['fake_count'] ) ) {
        $fakecountoption = $chwebr_options['fake_count'];
    }
    $fakecount = round( $fakecountoption * chwebr_get_fake_factor(), 0 );
    return $fakecount;
}

/*
 * Hide sharecount until number of shares exceed
 * 
 * @since 2.0.7
 * 
 * @param int number of shares
 * @return bool true when shares are hidden
 * 
 */

function chwebr_hide_shares( $sharecount ) {
    global $chwebr_options, $post;

    if( empty( $chwebr_options['hide_sharecount'] ) ) {
        return false;
    }

    $url = get_permalink( isset( $post->ID ) );
    $sharelimit = isset( $chwebr_options['hide_sharecount'] ) ? $chwebr_options['hide_sharecount'] : 0;

    if( $sharecount >= $sharelimit ) {
        return false;
    }
    // Hide share count per default when it is not a valid number
    return true;
}

/* Additional content above share buttons 
 * 
 * @return string $html
 * @scince 2.3.2
 */

function chwebr_content_above() {
    global $chwebr_options;
    $html = !empty( $chwebr_options['content_above'] ) ? '<div class="chwebr_above_buttons">' . chwebr_cleanShortcode('chwebsocialshare', $chwebr_options['content_above']) . '</div>' : '';
    return apply_filters( 'chwebr_above_buttons', $html );
}

/* Additional content above share buttons 
 * 
 * @return string $html
 * @scince 2.3.2
 */

function chwebr_content_below() {
    global $chwebr_options;
    $html = !empty( $chwebr_options['content_below'] ) ? '<div class="chwebr_below_buttons">' . chwebr_cleanShortcode('chwebsocialshare', $chwebr_options['content_below']) . '</div>' : '';
    return apply_filters( 'chwebr_below_buttons', $html );
}

/**
 * Check if buttons are excluded from a specific post id
 * 
 * @return true if post is excluded
 */
function chwebr_is_excluded() {
    global $post, $chwebr_options;

    if( !isset( $post ) ) {
        return false;
    }

    $excluded = isset( $chwebr_options['excluded_from'] ) ? $chwebr_options['excluded_from'] : null;

    // Load scripts when page is not excluded
    if( strpos( $excluded, ',' ) !== false ) {
        $excluded = explode( ',', $excluded );
        if( in_array( $post->ID, $excluded ) ) {
            chwebdebug()->info( "is excluded" );
            return true;
        }
    }
    if( $post->ID == $excluded ) {
        chwebdebug()->info( "is single excluded" );
        return true;
    }

    return false;
}


/**
 * Return general post title
 * 
 * @param string $title default post title
 * @global obj $chwebr_meta_tags
 * 
 * @return string the default post title, shortcode title or custom twitter title
 */
function chwebr_get_title() {
    global $post, $chwebr_meta_tags;
    if( is_singular() && method_exists($chwebr_meta_tags, 'get_og_title')) {
        $title = $chwebr_meta_tags->get_og_title();
        $title = html_entity_decode( $title, ENT_QUOTES, 'UTF-8' );
        $title = urlencode( $title );
        $title = str_replace( '#', '%23', $title );
        $title = esc_html( $title );
    } else {
        $title = chwebr_get_document_title();
        $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        $title = urlencode($title);
        $title = str_replace('#', '%23', $title);
        $title = esc_html($title);
    }
    return apply_filters( 'chwebr_get_title', $title );
}

/**
 * Return twitter custom title
 * 
 * @global object $chwebr_meta_tags
 * @changed 3.0.0
 * 
 * @return string the custom twitter title
 */
function chwebr_get_twitter_title() {
    global $chwebr_meta_tags;
    // $chwebr_meta_tags is only available on singular pages
    if( is_singular() && method_exists($chwebr_meta_tags, 'get_twitter_title') ) {
        $title = $chwebr_meta_tags->get_twitter_title();
        $title = html_entity_decode( $title, ENT_QUOTES, 'UTF-8' );
        $title = urlencode( $title );
        $title = str_replace( '#', '%23', $title );
        $title = str_replace( '+', '%20', $title );
        $title = str_replace('|','',$title);
        $title = esc_html( $title );
       
    } else {
        // title for non singular pages
        $title = chwebr_get_title();
        $title = str_replace( '+', '%20', $title );
        $title = str_replace('|','',$title);
    }
    return apply_filters('chwebr_twitter_title', $title);
}

/* 
 * Get URL to share
 * 
 * @return url  $string
 * @scince 2.2.8
 */

function chwebr_get_url() {
    global $post;
    
    if( isset($post->ID )) {
        // The permalink for singular pages!
        // Do not check here for is_singular() (like e.g. the sharebar addon does.)
        // Need to check for post id because on category and archiv pages 
        // we want the pageID within the loop instead the first appearing one.
        $url = chwebr_sanitize_url(get_permalink( $post->ID ));
    } else {
         // The main URL
        $url = chwebr_get_main_url();
    }
    
    return apply_filters( 'chwebr_get_url', $url );
}

/* 
 * Get twitter URL to share
 * 
 * @return url  $string
 * @scince 2.2.8
 */

function chwebr_get_twitter_url() {
    if( function_exists( 'chwebr_get_shorturl_singular' ) ) {
        $url = chwebr_get_shorturl_singular( chwebr_get_url() );
    } else if( function_exists( 'chwebsuGetShortURL' ) ) { // compatibility mode for ChwebSocialShare earlier than 3.0
        $get_url = chwebr_get_url();
        $url = chwebsuGetShortURL( $get_url );
    } else {
        $url = chwebr_get_url();
    }
    return apply_filters( 'chwebr_get_twitter_url', $url );
}

/**
 * Wrapper for chwebr_get_shorturl_singular()
 * 
 * @param string $url
 * @return string
 */
function chwebr_get_shorturl( $url ) {

    if( !empty( $url ) ) {
        $url = chwebr_get_shorturl_singular( $url );
    } else {
        $url = "";
    }

    return $url;
}


/**
 * Get sanitized twitter handle
 * 
 * @global array $chwebr_options
 * @return mixed string | bool false
 */
function chwebr_get_twitter_username() {
    global $chwebr_options;

    if( empty( $chwebr_options['chwebsocialsharer_hashtag'] ) ) {
        return;
    }

    // If plugin is not running on chaudharyweb.com or dev environment replace @chwebsocialshare
    if( $_SERVER['HTTP_HOST'] !== 'www.chaudharyweb.com' && $_SERVER['HTTP_HOST'] !== 'src.wordpress-develop.dev' ) {
        //Sanitize it
        $replace_first = str_ireplace( 'chwebsocialshare', '', $chwebr_options['chwebsocialsharer_hashtag'] );
        $replace_second = str_ireplace( '@', '', $replace_first );
        return $replace_second;
    } else {
        return $chwebr_options['chwebsocialsharer_hashtag'];
    }
}

/**
 * Returns document title for the current page.
 *
 * @since 3.0
 *
 * @global int $post Page number of a list of posts.
 *
 * @return string Tag with the document title.
 */
function chwebr_get_document_title() {
    
    /**
     * Filter the document title before it is generated.
     *
     * Passing a non-empty value will short-circuit wp_get_document_title(),
     * returning that value instead.
     *
     * @since 4.4.0
     *
     * @param string $title The document title. Default empty string.
     */

    // If it's a 404 page, use a "Page not found" title.
    if( is_404() ) {
        $title = __( 'Page not found' );

        // If it's a search, use a dynamic search results title.
    } elseif( is_search() ) {
        /* translators: %s: search phrase */
        $title = sprintf( __( 'Search Results for &#8220;%s&#8221;' ), get_search_query() );

        // If on a post type archive, use the post type archive title.
    } elseif( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
        
        // If on a taxonomy archive, use the term title.
    } elseif( is_tax() ) {
        $title = single_term_title( '', false );

        /*
         * If we're on the blog page that is not the homepage or
         * a single post of any post type, use the post title.
         */
    //} elseif( !is_home() || is_singular() ) {
    } elseif( is_singular() ) {
        $title = the_title_attribute('echo=0');

        // If on the front page, use the site title.
    } elseif( is_front_page() ) {
        $title = get_bloginfo( 'name', 'display' );
        
        // If on a category or tag archive, use the term title.   
    } elseif( is_category() || is_tag() ) {
        $title = single_term_title( '', false );

        // If on an author archive, use the author's display name.
    } elseif( is_author() && $author = get_queried_object() ) {
        $title = $author->display_name;

        // If it's a date archive, use the date as the title.
    } elseif( is_year() ) {
        $title = get_the_date( _x( 'Y', 'yearly archives date format' ) );
    } elseif( is_month() ) {
        $title = get_the_date( _x( 'F Y', 'monthly archives date format' ) );
    } elseif( is_day() ) {
        $title = get_the_date();
    }

    $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
    return $title;
}