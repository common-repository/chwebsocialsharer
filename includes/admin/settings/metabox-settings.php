<?php

/*
 * Register the meta boxes
 * Used by CHWEBR_RWMB class
 * 
 * @package CHWEBR
 *
 * @3.0.0
 */

/**
 * Check if meta boxes are shown for a specific user role and 
 * Show meta box when a specific user role is not specified
 * 
 * @global array $chwebr_options
 * @return bool true when meta boxes should should be visible for a specific user role
 */
function chwebr_show_meta_box(){
    global $chwebr_options, $wp_roles;
    
    // Show meta boxes per default in any case when user roles are not defined
    if(!empty($chwebr_options) && !isset($chwebr_options['user_roles_for_sharing_options'])){
        return true;
    }
    
    // Get user roles and plugin settings
    $user = wp_get_current_user();

    // Loop through user roles
    foreach($user->roles as $role) {
        // Rule exists and it is set
        if( isset( $chwebr_options["user_roles_for_sharing_options"] ) && in_array( str_replace( ' ', null, strtolower( $role ) ), $chwebr_options["user_roles_for_sharing_options"] ) ) {
            // Garbage collection
            unset($user);
            return true;
        }
    }
    
    unset ($user);
    return false;
}

add_filter( 'chwebr_rwmb_meta_boxes', 'chwebr_meta_boxes' );
function chwebr_meta_boxes( $meta_boxes ) {
    global $chwebr_options, $post;
    $prefix = 'chwebr_';
    $post_types = isset( $chwebr_options['post_types'] ) ? $chwebr_options['post_types'] : array();
    foreach ( $post_types as $key => $value ):
        $post_type[] = $key;
    endforeach;
    $post_type[] = 'post';
    $post_type[] = 'page';
    //echo "<pre>";
//    echo(var_dump($post_type));
//        echo "</pre>";

    $twitter_handle = isset( $chwebr_options['chwebsocialsharer_hashtag'] ) ? $chwebr_options['chwebsocialsharer_hashtag'] : '';
    
    
    // Do not show meta boxes
    if( !chwebr_show_meta_box() ) {
        return apply_filters( 'chwebr_meta_box_settings', $meta_boxes, 10, 0 );
    }

    // Setup our meta box using an array
    $meta_boxes[0] = array(
        'id' => 'chwebr_meta',
        'title' => 'ChwebSocialShare Social Sharing Options',
        'pages' => $post_type,
        'context' => 'normal',
        'priority' => 'high',
        'fields' => array(
            // Setup the social media image
            array(
                'name' => '<span class="chwebicon chwebicon-share"></span> ' . __( 'Social Media Image', 'chwebr' ),
                'desc' => __( 'Optimal size for post shared images on Facebook, Google+ and LinkedIn is 1200px x 630px. Aspect ratio 1.9:1', 'chwebr' ),
                'id' => $prefix . 'og_image',
                'type' => 'image_advanced',
                'clone' => false,
                'max_file_uploads' => 1,
                'class' => 'chwebr-og-image'
            ),
            // Setup the social media title
            array(
                'name' => '<span class="chwebicon chwebicon-share"> </span> ' . __( 'Social Media Title', 'chwebr' ),
                'desc' => __( 'This title is used by the open graph meta tag og:title and will be used when users share your content on Facebook, LinkedIn, or Google+. Leave this blank to use ', 'chwebr' ) . (chwebr_yoast_active() ? __( 'Yoast Facebook / SEO title', 'chwebr' ) : 'the post title'),
                'id' => $prefix . 'og_title',
                'type' => 'textarea',
                'clone' => false,
                'class' => 'chwebr-og-title'
            ),
            // Setup the social media description
            array(
                'name' => '<span class="chwebicon chwebicon-share"></span> ' . __( 'Social Media Description', 'chwebr' ),
                'desc' => __( 'This description is used by the open graph meta tag og:description and will be used when users share your content on Facebook, LinkedIn, and Google Plus. Leave this blank to use ', 'chwebr' ) . (chwebr_yoast_active() ? __( 'Yoast Facebook open graph description or the post excerpt.', 'chwebr' ) : ' the post excerpt.'),
                'id' => $prefix . 'og_description',
                'type' => 'textarea',
                'clone' => false,
                'class' => 'chwebr-og-desc'
            ),
            array(
                'name' => 'divider',
                'id' => 'divider',
                'type' => 'divider'
            ),
            // Setup the pinterest optimized image
            array(
                'name' => '<span class="chwebicon chwebicon-pinterest"></span> ' . __( 'Pinterest Image', 'chwebr' ) . '<a class="chwebr-helper" href="#"></a><div class="chwebr-message" style="display: none;">'.sprintf(__('Get the <a href="%s" target="_blank">Network Add-On</a> to make use of the Pinterest Features','chwebr'),'').'</div>',
                'desc' => __( 'Pinned images need to be more vertical than horizontal in orientation. Use an aspect ratio of 2:3 to 1:3.5 and a minimum width of 600 pixels. So an image that is 600 pixels wide should be between 900 and 2100 pixels tall.', 'chwebr' ),
                'id' => $prefix . 'pinterest_image',
                'type' => 'image_advanced',
                'clone' => false,
                'max_file_uploads' => 1,
                'class' => 'chwebr-pinterest-image'
            ),
            // Setup the pinterest description
            array(
                'name' => '<span class="chwebicon chwebicon-pinterest"></span> ' . __('Pinterest Description', 'chwebr' ) . '<a class="chwebr-helper" href="#"></a><div class="chwebr-message" style="display: none;">'.sprintf(__('Get the <a href="%s" target="_blank">Network Add-On</a> to make use of the Pinterest Features','chwebr'),'').'</div>',
                'desc' => __( 'Place a customized message that will be used when this post is shared on Pinterest. Leave this blank to use the ', 'chwebr' ) . (chwebr_yoast_active() ? __( 'Yoast SEO title', 'chwebr' ) : __( 'the post title', 'chwebr' )),
                'id' => $prefix . 'pinterest_description',
                'type' => 'textarea',
                'clone' => false,
                'class' => 'chwebr-pinterest-desc'
            ),
            // Setup the Custom Tweet box
            array(
                'name' => '<span class="chwebicon chwebicon-twitter"></span> ' . __('Custom Tweet','chwebr'),
                'desc' =>  chwebr_twitter_desc(),
                'id' => $prefix . 'custom_tweet',
                'type' => 'textarea',
                'clone' => false,
                'class' => 'chwebr-custom-tweet'
            ),
            array(
                'id' => $prefix . 'position',
                'name' => __('Share Button Position','chwebr'),
                'type' => 'select',
                'placeholder' => __('Use Global Setting','chwebr'),
                'before' => '<div style="max-width:250px;float:left;">',
                'after' => '</div>',
                'options' => array(
                    'disable' => __('Disable Automatic Buttons','chwebr'),                      
                    'before' => __('Above Content','chwebr'),
                    'after' => __('Below Content','chwebr'),
                    'both' => __('Above & Below Content','chwebr'),
                    )
            ),
            array(
                'helper'=> '<a class="chwebr-helper" href="#" style="margin-left:-4px;"></a><div class="chwebr-message" style="display: none;">'.__('Validate open graph meta tags on your site. Incorrect data can result in wrong share description, title or images and should be fixed! In the facebook debugger click the link "Fetch new scrape information" to purge the facebook cache.','chwebr').'</div>',
                'id' => $prefix . 'validate_og',
                'before' => '<div style="max-width:250px;float:left;margin-top:45px;">',
                'after' => '</div>',
                'type' => 'validate_og'
            ),
            array(
                'name' => 'divider',
                'id' => 'divider',
                'type' => 'divider'
            ),
            array(
                'id' => $prefix . 'twitter_handle',
                'type' => 'hidden_data',
                'std' => $twitter_handle,
            ),
        )
    );

    return apply_filters( 'chwebr_meta_box_settings', $meta_boxes, 10, 0 );
}

/**
 * Check if Yoast is active
 *
 * @return boolean true when yoast is active
 */
function chwebr_yoast_active() {
    if( defined( 'WPSEO_VERSION' ) ) {
        return true;
    }
}


function chwebr_twitter_desc() {
    $str = "";
    if( chwebr_get_twitter_username() ) {
        $str .= __( 'Based on your username @', 'chwebr' ) . chwebr_get_twitter_username() . __( ' ,the shortened post url and the current content above', 'chwebr' );
    } else {
        $str .= __( 'Based on the shortened post url and the current content above', 'chwebr' );
    }
    $str .= __( ' your tweet has a maximum of 140 characters. ', 'chwebr' );
    if (!chwebr_yoast_active()){
        $str .= __( 'If this is left blank the post title will be used. ', 'chwebr' );
    }else{
        $str .= __( 'If this is left blank the Yoast Twitter Title or post title will be used. ', 'chwebr' );
    }

    return $str;
}
