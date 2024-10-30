<?php
/**
 * Register Settings
 *
 * @package     CHWEBR
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 1.0.0
 * @return mixed
 */
function chwebr_get_option( $key = '', $default = false ) {
    global $chwebr_options;
    $value = !empty( $chwebr_options[$key] ) ? $chwebr_options[$key] : $default;
    $value = apply_filters( 'chwebr_get_option', $value, $key, $default );
    return apply_filters( 'chwebr_get_option_' . $key, $value, $key, $default );
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 1.0
 * @return array CHWEBR settings
 */
function chwebr_get_settings() {
    $settings = get_option( 'chwebr_settings' );


    if( empty( $settings ) ) {
        // Update old settings with new single option

        $general_settings = is_array( get_option( 'chwebr_settings_general' ) ) ? get_option( 'chwebr_settings_general' ) : array();
        $visual_settings = is_array( get_option( 'chwebr_settings_visual' ) ) ? get_option( 'chwebr_settings_visual' ) : array();
        $networks = is_array( get_option( 'chwebr_settings_networks' ) ) ? get_option( 'chwebr_settings_networks' ) : array();
        $ext_settings = is_array( get_option( 'chwebr_settings_extensions' ) ) ? get_option( 'chwebr_settings_extensions' ) : array();
        $license_settings = is_array( get_option( 'chwebr_settings_licenses' ) ) ? get_option( 'chwebr_settings_licenses' ) : array();
        $addons_settings = is_array( get_option( 'chwebr_settings_addons' ) ) ? get_option( 'chwebr_settings_addons' ) : array();

        $settings = array_merge( $general_settings, $visual_settings, $networks, $ext_settings, $license_settings, $addons_settings );

        update_option( 'chwebr_settings', $settings );
    }
    return apply_filters( 'chwebr_get_settings', $settings );
}

/**
 * Add all settings sections and fields
 *
 * @since 1.0
 * @return void
 */
function chwebr_register_settings() {

    if( false == get_option( 'chwebr_settings' ) ) {
        add_option( 'chwebr_settings' );
    }

    foreach ( chwebr_get_registered_settings() as $tab => $settings ) {

        add_settings_section(
            'chwebr_settings_' . $tab, __return_null(), '__return_false', 'chwebr_settings_' . $tab
        );

        foreach ( $settings as $option ) {

            $name = isset( $option['name'] ) ? $option['name'] : '';

            add_settings_field(
                'chwebr_settings[' . $option['id'] . ']', $name, function_exists( 'chwebr_' . $option['type'] . '_callback' ) ? 'chwebr_' . $option['type'] . '_callback' : 'chwebr_missing_callback', 'chwebr_settings_' . $tab, 'chwebr_settings_' . $tab, array(
                    'id' => isset( $option['id'] ) ? $option['id'] : null,
                    'desc' => !empty( $option['desc'] ) ? $option['desc'] : '',
                    'name' => isset( $option['name'] ) ? $option['name'] : null,
                    'section' => $tab,
                    'size' => isset( $option['size'] ) ? $option['size'] : null,
                    'options' => isset( $option['options'] ) ? $option['options'] : '',
                    'std' => isset( $option['std'] ) ? $option['std'] : '',
                    'textarea_rows' => isset( $option['textarea_rows'] ) ? $option['textarea_rows'] : ''
                )
            );
        }
    }

    // Creates our settings in the options table
    register_setting( 'chwebr_settings', 'chwebr_settings', 'chwebr_settings_sanitize' );
}

add_action( 'admin_init', 'chwebr_register_settings' );


/**
 * Retrieve the array of plugin settings
 *
 * @since 1.8
 * @return array
 */
function chwebr_get_registered_settings() {
    
    /**
     * 'Whitelisted' CHWEBR settings, filters are provided for each settings
     * section to allow extensions and other plugins to add their own settings
     */
    $chwebr_settings = array(
        /** General Settings */
        'general' => apply_filters( 'chwebr_settings_general', array(
                'general_header' => array(
                    'id' => 'general_header',
                    'name' => '<strong>' . __( 'General', 'chwebr' ) . '</strong>',
                    'desc' => __( '', 'chwebr' ),
                    'type' => 'header'
                ),
//                'chwebr_sharemethod' => array(
//                    'id' => 'chwebr_sharemethod',
//                    'name' => __( 'Share counts', 'chwebr' ),
//                    'desc' => __( '<i>ChwebEngine</i> collects shares by direct request to social networks. <br><br>Shares are collected for Facebook, Twitter, LinkedIn, Google+, Pinterest, Stumbleupon, Buffer, VK. <p></p>Twitter count is aggreagated via <a href="http://newsharecounts.com" target="_blank" rel="external nofollow">newsharecounts.com</a>. You must sign up with your Twitter account for this free service to get the twitter share count. Visit the site newsharecount.com, fill in your website domain and click on <i>Sign in with Twitter</i>. Thats it!', 'chwebr' ),
//                    'type' => 'select',
//                    'options' => array(
//                        'chwebengine' => 'ChwebEngine (including twitter count)',
//                        'sharedcount' => 'Sharedcount.com (Not working any longer)'
//                    )
//                ),
//                'chwebsocialsharer_apikey' => array(
//                    'id' => 'chwebsocialsharer_apikey',
//                    'name' => __( 'Sharedcount.com API Key', 'chwebr' ),
//                    'desc' => __( 'Get it at <a href="https://www.sharedcount.com" target="_blank">SharedCount.com</a> for 10.000 free daily requests.', 'chwebr' ),
//                    'type' => 'text',
//                    'size' => 'medium'
//                ),
//                'chwebsocialsharer_sharecount_domain' => array(
//                    'id' => 'chwebsocialsharer_sharecount_domain',
//                    'name' => __( 'Sharedcount.com endpint', 'chwebr' ),
//                    'desc' => __( 'The SharedCount Domain your API key is configured to query. For example, free.sharedcount.com. This may update automatically if configured incorrectly.', 'chwebr' ),
//                    'type' => 'text',
//                    'size' => 'medium',
//                    'std' => 'free.sharedcount.com'
//                ),
                'caching_method' => array(
                    'id' => 'caching_method',
                    'name' => __( 'Caching Method', 'chwebr' ),
                    'desc' => sprintf(__( 'The <i>Async Cache Refresh</i> method never adds additonal load time for a visitor and refreshes the cache asyncronously in the background. <br><br>- New posts are updated at each hour. <br>- Posts older than 3 weeks are updated every 4 hours<br>- Post older than 2 months are updated every 12 hours<br><br> <i>Refresh while loading</i> rebuilds expired cache while page is loading and adds a little extra time during inital page load. <br><br><strong>If shares are not updating</strong> or site is heavy cached try <i>Refresh while loading!</i> That\'s the default method ChwebSocialShare was using before version 3.0<br><br>Shares still not shown? <a href="%1s" target="_blank">Read this first!</a>', 'chwebr' ), ''),
                    'type' => 'select',
                    'options' => array(
                        'async_cache' => 'Async Cache Refresh',
                        'refresh_loading' => 'Refresh while loading'
                    )
                ),
                'chwebsocialsharer_cache' => array(
                    'id' => 'chwebsocialsharer_cache',
                    'name' => __( 'Cache expiration', 'chwebr' ),
                    'desc' => __( 'Shares are counted for posts after a certain time and counts are not updated immediately. Sharedcount.com uses his own cache (30 - 60min). <p><strong>Default: </strong>5 min. <strong>Recommended: </strong>30min and more', 'chwebr' ),
                    'type' => 'select',
                    'options' => chwebr_get_expiretimes()
                ),
                'facebook_count' => array(
                    'id' => 'facebook_count_mode',
                    'name' => __( 'Facebook Count', 'chwebr' ),
                    'desc' => __( 'Get the Facebook total count including "likes" and "shares" or get only the pure share count', 'chwebr' ),
                    'type' => 'select',
                    'options' => array(
                        'shares' => 'Shares',
                        //'likes' => 'Likes', not used any longer
                        'total' => 'Shares + Comments'
                    )
                ),
                'cumulate_http_https' => array(
                    'id' => 'cumulate_http_https',
                    'name' => __( 'Cumulate Http(s) Shares', 'chwebr' ),
                    'desc' => __( 'Activate this if you want facebook shares to be cumulated for https and http scheme. If you switched your site to from http to https this is needed to not loose any previous shares which are cumulated earlier for the non ssl version of your site. If you are not missing any shares do not activate this option.', 'chwebr' ),
                    'type' => 'checkbox'
                ),
                'fake_count' => array(
                    'id' => 'fake_count',
                    'name' => __( 'Fake Share Count', 'chwebr' ),
                    'desc' => __( 'This number will be aggregated to all your share counts and is multiplied with a post specific factor. (Number of words of post title divided with 10).', 'chwebr' ),
                    'type' => 'text',
                    'size' => 'medium'
                ),
                'disable_sharecount' => array(
                    'id' => 'disable_sharecount',
                    'name' => __( 'Disable Sharecount', 'chwebr' ),
                    'desc' => __( 'Use this when curl() is not supported on your server or share counts should not counted. This mode does not call the database and no SQL queries are generated. (Only less performance benefit. All db requests are cached) Default: false', 'chwebr' ),
                    'type' => 'checkbox'
                ),
                'hide_sharecount' => array(
                    'id' => 'hide_sharecount',
                    'name' => __( 'Hide Sharecount', 'chwebr' ),
                    'desc' => __( '<strong>Optional:</strong> If you fill in any number here, the shares for a specific post are not shown until the share count of this number is reached.', 'chwebr' ),
                    'type' => 'text',
                    'size' => 'small'
                ),
                'execution_order' => array(
                    'id' => 'execution_order',
                    'name' => __( 'Execution Order', 'chwebr' ),
                    'desc' => __( 'If you use other content plugins you can define here the execution order. Lower numbers mean earlier execution. E.g. Say "0" and Chwebsocialshare is executed before any other plugin (When the other plugin is not overwriting our execution order). Default is "1000"', 'chwebr' ),
                    'type' => 'text',
                    'size' => 'small',
                    'std' => 1000
                ),
                'load_scripts_footer' => array(
                    'id' => 'load_scripts_footer',
                    'name' => __( 'JavaScript in Footer', 'chwebr' ),
                    'desc' => __( 'Enable this to load all *.js files into footer. Make sure your theme uses the wp_footer() template tag in the appropriate place. Default: Disabled', 'chwebr' ),
                    'type' => 'checkbox'
                ),
                'loadall' => array(
                    'id' => 'loadall',
                    'name' => __( 'JS & CSS Everywhere', 'chwebr' ),
                    'desc' => __( 'Enable this option if you are using </br> <strong>&lt;?php echo do_shortcode("[chwebsocialshare]"); ?&gt;</strong> to make sure that all css and js files are loaded. If Top or Bottom automatic position is used you can deactivate this option to allow conditional loading so ChwebSocialShare\'s JS and CSS files are loaded only on pages where ChwebSocialShare is used.', 'chwebr' ),
                    'type' => 'checkbox',
                    'std' => 'false'
                ),
                'twitter_popup' => array(
                    'id' => 'twitter_popup',
                    'name' => __( 'Twitter Popup disabled', 'chwebr' ),
                    'desc' => __( 'Check this box if your twitter popup is openening twice. This happens sometimes when you are using any third party twitter plugin or the twitter SDK on your website.', 'chwebr' ),
                    'type' => 'checkbox',
                    'std' => '0'
                ),
                'uninstall_on_delete' => array(
                    'id' => 'uninstall_on_delete',
                    'name' => __( 'Remove Data on Uninstall?', 'chwebr' ),
                    'desc' => __( 'Check this box if you would like Chwebsocialshare to completely remove all of its data when the plugin is deleted.', 'chwebr' ),
                    'type' => 'checkbox'
                ),
               /* 'allow_tracking' => array(
                    'id' => 'allow_tracking',
                    'name' => __( 'Allow Usage Tracking', 'chwebr' ),
                    'desc' => sprintf( __( 'Allow Chwebsocialshare to track plugin usage? Opt-in to tracking and our newsletter and immediately be emailed a <strong>20%% discount to the Chwebsocialshare shop</strong>, valid towards the <a href="%s" target="_blank">purchase of Add-Ons</a>. No sensitive data is tracked.', 'chwebr' ), 'https://www.chaudharyweb.com/add-ons/?utm_source=' . substr( md5( get_bloginfo( 'name' ) ), 0, 10 ) . '&utm_medium=admin&utm_term=setting&utm_campaign=CHWEBRUsageTracking' ),
                    'type' => 'checkbox'
                ),*/
                'is_main_query' => array(
                    'id' => 'is_main_query',
                    'name' => __( 'Hide Buttons in Widgets (is_main_query)', 'chwebr' ),
                    'desc' => __( 'If Share Buttons are shown in widgets enable this option. For devs: This uses the is_main_query condition. ' ) ,
                    'type' => 'checkbox'
                ),
                "user_roles_for_sharing_options" => array(
                    "id"            => "user_roles_for_sharing_options",
                    "name"          => __("Show Share Options Meta Box", "chwebr"),
                    "desc"          => __("Select user roles which can only see ChwebSocialShare Social Sharing Meta Box Options on posts and pages edit screen and User Meta Box on user profiles. If nothing is set meta boxes are shown for all user roles", "chwebr"),
                    "type"          => "multiselect",
                    "options"       => chwebr_get_user_roles(),
                    "placeholder"   => __("Select User Roles", "chwebr"),
                    "std"           => __("All Roles", "chwebr"),
                ),
                'services_header' => array(
                    'id' => 'services_header',
                    'name' => '<strong>' . __( 'Networks', 'chwebr' ) . '</strong>',
                    'desc' => '',
                    'type' => 'header'
                ),
              /*  array(
                'id' => 'fb_access_token_new',
                'name' => __( 'Facebook User Access Token', 'chwebr' ),
                'desc' => sprintf( __( 'Required if your website hits the facebook rate limit of 200 calls per hour. <a href="%s" target="_blank">Read here</a> how to get the access token.', 'chwebr' ), '' ),
                'type' => 'fboauth',
                'size' => 'large'
                ),*/
                array(
                    'id' => 'fb_publisher_url',
                    'name' => __( 'Facebook page url', 'chwebr' ),
                    'desc' => __( 'Optional: The url of the main facebook account connected with this site', 'chwebr' ),
                    'type' => 'text',
                    'size' => 'large'
                ),
//                array(
//                    'id' => 'fb_app_id',
//                    'name' => __( 'Facebook App ID', 'chwebr' ),
//                    'desc' => sprintf( __( 'Optional and not needed for basic share buttons. But required by some ChwebSocialShare Add-Ons. <a href="%1s" target="_blank">Create a App ID now</a>.', 'chwebr' ), 'https://developers.facebook.com/docs/apps/register' ),
//                    'type' => 'text',
//                    'size' => 'medium'
//                ),
//                array(
//                    'id' => 'fb_app_secret',
//                    'name' => __( 'Facebook App Secret', 'chwebr' ),
//                    'desc' => sprintf( __( 'Required for getting accurate facebook share numbers. Where do i find the facebook APP Secret?', 'chwebr' ), 'https://developers.facebook.com/docs/apps/register' ),
//                    'type' => 'text',
//                    'size' => 'medium'
//                ),
                'chwebsocialsharer_hashtag' => array(
                    'id' => 'chwebsocialsharer_hashtag',
                    'name' => __( 'Twitter Username', 'chwebr' ),
                    'desc' => __( '<strong>Optional:</strong> Using your twitter username results in via @username', 'chwebr' ),
                    'type' => 'text',
                    'size' => 'medium'
                ),
                'twitter_card' => array(
                    'id' => 'twitter_card',
                    'name' => __( 'Twitter Card', 'chwebr' ),
                    'desc' => __( 'If this is activated ChwebSocialShare is creating new tags in head of your site for twitter card data and populates them with data coming from the ChwebSocialShare Twitter Meta Box from the post editing screen or in case you are using Yoast these fields will be populated with the Yoast Twitter Card Data.<br><br>
So the ChwebSocialShare twitter card tags will be containing the same social meta data that YOAST would be supplying on your site. So you can use that feature parallel to the Yoast twitter card integration and you do not need to deactivate it even when you prefer to use the Yoast Twitter Card editor.', 'chwebr' ),
                    'type' => 'checkbox'
                ),
                'open_graph' => array(
                    'id' => 'open_graph',
                    'name' => __( 'Open Graph Meta Tags', 'chwebr' ),
                    'desc' => __( 'If this is activated ChwebSocialShare is creating new tags in head of your site for open graph data and populates them with data coming from the ChwebSocialShare Open Graph Meta Box from the post editing screen or in case you are using Yoast these fields will be populated with the Yoast Open Graph Data.<br><br>
So the ChwebSocialShare open graph data will be containing the same social meta data that YOAST would be supplying on your site. So you can use that feature parallel to the Yoast open graph integration and you do not need to deactivate it even when you prefer to use the Yoast Open Graph editor.', 'chwebr' ),
                    'type' => 'checkbox'
                ),
                'visible_services' => array(
                    'id' => 'visible_services',
                    'name' => __( 'Large Buttons', 'chwebr' ),
                    'desc' => __( 'Specify how many services and social networks are visible before the "Plus" Button is shown. This buttons turn into large prominent buttons.', 'chwebr' ),
                    'type' => 'select',
                    'options' => numberServices()
                ),
                'networks' => array(
                    'id' => 'networks',
                    'name' => __( 'Social Networks', 'chwebr' ),
                    'desc' => __( 'Use Drag and drop for sorting. Enable the ones that should be visible. Activate<br>more networks than number of "Large Buttons" and [+] PLUS button will be<br> added automatically.', 'chwebr' ),
                    'type' => 'networks',
                    'options' => chwebr_get_networks_list()
                ),
                /*'networks' => array(
                    'id' => 'networks',
                    'name' => '<strong>' . __( 'Services', 'chwebr' ) . '</strong>',
                    'desc' => __( 'Drag and drop the Share Buttons to sort them and specify which ones should be enabled. <br>If you enable more networks than "Large Buttons", the plus sign is automatically added <br>to the last visible large share buttons', 'chwebr' ),
                    'type' => 'networks',
                    'options' => chwebr_get_networks_list()
                ),*/
                /*'services_header' => array(
                    'id' => 'services_header',
                    'name' => __( 'Social Networks', 'chwebr' ),
                    'desc' => '',
                    'type' => 'header'
                ),*/
                /*'visible_services' => array(
                    'id' => 'visible_services',
                    'name' => __( 'Large Share Buttons', 'chwebr' ),
                    'desc' => __( 'Specify how many services and social networks are visible before the "Plus" Button is shown. These buttons turn into large prominent buttons.', 'chwebr' ),
                    'type' => 'select',
                    'options' => numberServices()
                ),*/
                
//                array(
//                    'id' => 'shorturl_type',
//                    'name' => __( 'Enable on', 'chwebr' ),
//                    'desc' => __( 'You can choose multiple networks where short url\'s should be used.', 'chwebr' ),
//                    'type' => 'multiselect',
//                    'placeholder' => 'Select the networks',
//                    'options' => array(
//                        'twitter' => 'Twitter',
//                        'facebook' => 'Facebook',
//                        'default' => 'All Networks'
//                    ),
//                    'std' => 'All networks'
//                ),
                'style_header' => array(
                    'id' => 'style_header',
                    'name' => '<strong>' . __( 'Visual', 'chwebr' ) . '</strong>',
                    'desc' => __( '', 'chwebr' ),
                    'type' => 'header'
                ),
                'share_headline' => array(
                    'id' => 'share_headline',
                    'name' => __( 'Shares', 'chwebr' ),
                    'type' => 'headline'
                ),
                'chwebsocialsharer_round' => array(
                    'id' => 'chwebsocialsharer_round',
                    'name' => __( 'Round up Shares', 'chwebr' ),
                    'desc' => __( 'Share counts greater than 1.000 will be shown as 1k. Greater than 1 Million as 1M', 'chwebr' ),
                    'type' => 'checkbox'
                ),
                'animate_shares' => array(
                    'id' => 'animate_shares',
                    'name' => __( 'Animate Shares', 'chwebr' ),
                    'desc' => __( 'Count up the shares on page loading with a nice looking animation effect. This only works on singular pages and not with shortcodes generated buttons.', 'chwebr' ),
                    'type' => 'checkbox'
                ),
                'sharecount_title' => array(
                    'id' => 'sharecount_title',
                    'name' => __( 'Share Count Label', 'chwebr' ),
                    'desc' => __( 'Change the text of the Share count title. <strong>Default:</strong> SHARES', 'chwebr' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => 'SHARES'
                ),
                'share_color' => array(
                    'id' => 'share_color',
                    'name' => __( 'Share Count Color', 'chwebr' ),
                    'desc' => __( 'Choose color of the share number in hex format, e.g. #7FC04C: ', 'chwebr' ),
                    'type' => 'color_select',
                    'size' => 'medium',
                    'std' => '#cccccc'
                ),
                'button_headline' => array(
                    'id' => 'button_headline',
                    'name' => __( 'Buttons', 'chwebr' ),
                    'type' => 'headline'
                ),
                #######################

                'buttons_size' => array(
                    'id' => 'buttons_size',
                    'name' => __( 'Buttons Size', 'chwebr' ),
                    'desc' => __('', 'chwebr'),
                    'type' => 'select',
                    'options' => array(
                        'chweb-large' => 'Large',
                        'chweb-medium' => 'Medium',
                        'chweb-small' => 'Small'
                    ),
                    'std' => 'Large'
                ),
                'responsive_buttons' => array(
                    'id' => 'responsive_buttons',
                    'name' => __( 'Full Responsive Buttons', 'chwebr' ),
                    'desc' => __( 'Get full width buttons on large devices and small buttons on mobile devices. Deactivate to specify manually a fixed button width.', 'chwebr' ),
                    'type' => 'checkbox'
                ),
                array(
                    'id' => 'button_width',
                    'name' => __( 'Button Width', 'chwebpv' ),
                    'desc' => __( 'Minimum with of the large share buttons in pixels', 'chwebpv' ),
                    'type' => 'number',
                    'size' => 'normal',
                    'std' => '177'
                ),
                'button_margin' => array(
                    'id' => 'button_margin',
                    'name' => __( 'Button Margin', 'chwebr' ),
                    'desc' => __('Decide if there is a small gap between the buttons or not', 'chwebr'),
                    'type' => 'checkbox',
                ),
                'border_radius' => array(
                    'id' => 'border_radius',
                    'name' => __( 'Border Radius', 'chwebr' ),
                    'desc' => __( 'Specify the border radius of all buttons in pixel. A border radius of 20px results in circle buttons. Default value is zero.', 'chwebr' ),
                    'type' => 'select',
                    'options' => array(
                        0 => 0,
                        1 => 1,
                        2 => 2,
                        3 => 3,
                        4 => 4,
                        5 => 5,
                        6 => 6,
                        7 => 7,
                        8 => 8,
                        9 => 9,
                        10 => 10,
                        11 => 11,
                        12 => 12,
                        13 => 13,
                        14 => 14,
                        15 => 15,
                        16 => 16,
                        17 => 17,
                        18 => 18,
                        19 => 19,
                        20 => 20,
                        'default' => 'default'
                    ),
                    'std' => 'default'
                ),
                'chweb_style' => array(
                    'id' => 'chweb_style',
                    'name' => __( 'Share Button Style', 'chwebr' ),
                    'desc' => __( 'Change visual appearance of the share buttons.', 'chwebr' ),
                    'type' => 'select',
                    'options' => array(
                        'shadow' => 'Shadowed',
                        'gradiant' => 'Gradient',
                        'default' => 'Flat'
                    ),
                    'std' => 'default'
                ),
                'small_buttons' => array(
                    'id' => 'small_buttons',
                    'name' => __( 'Small Share Buttons', 'chwebr' ),
                    'desc' => __( 'All buttons will be shown as pure small icons without any text on desktop and mobile devices all the time.<br><strong>Note:</strong>Disable this if you want the buttons full width on desktop devices and small on mobile devices.', 'chwebr' ),
                    'type' => 'checkbox'
                ),
                'text_align_center' => array(
                    'id' => 'text_align_center',
                    'name' => __( 'Text Align Center', 'chwebr' ),
                    'desc' => __( 'Buttons Text labels and social icons will be aligned in center of the buttons', 'chwebr' ),
                    'type' => 'checkbox'
                ),
                /*'image_share' => array(
                    'id' => 'image_share',
                    'name' => __( 'Share buttons on image hover', 'chwebr' ),
                    'desc' => __( '', 'chwebr' ),
                    'type' => 'checkbox'
                ),*/
                'subscribe_behavior' => array(
                    'id' => 'subscribe_behavior',
                    'name' => __( 'Subscribe Button', 'chwebr' ),
                    'desc' => __( 'Specify if the subscribe button is opening a content box below the button or if the button is linked to the "subscribe url" below.', 'chwebr' ),
                    'type' => 'select',
                    'options' => array(
                        'content' => 'Open content box',
                        'link' => 'Open Subscribe Link'
                    ),
                    'std' => 'content'
                ),
                'subscribe_link' => array(
                    'id' => 'subscribe_link',
                    'name' => __( 'Subscribe URL', 'chwebr' ),
                    'desc' => __( 'Link the Subscribe button to this URL. This can be the url to your subscribe page, facebook fanpage, RSS feed etc. e.g. http://yoursite.com/subscribe', 'chwebr' ),
                    'type' => 'text',
                    'size' => 'regular',
                    'std' => ''
                ),
                'additional_content' => array(
                    'id' => 'additional_content',
                    'name' => __( 'Additional Content', 'chwebr' ),
                    'desc' => __( '', 'chwebr' ),
                    'type' => 'add_content',
                    'options' => array(
                        'box1' => array(
                            'id' => 'content_above',
                            'name' => __( 'Content Above', 'chwebr' ),
                            'desc' => __( 'Content appearing above share buttons. Use HTML, formulars, like button, links or any other text. Shortcodes are supported, e.g.: [contact-form-7]', 'chwebr' ),
                            'type' => 'textarea',
                            'textarea_rows' => '3',
                            'size' => 15
                        ),
                        'box2' => array(
                            'id' => 'content_below',
                            'name' => __( 'Content Below', 'chwebr' ),
                            'desc' => __( 'Content appearing below share buttons.  Use HTML, formulars, like button, links or any other text. Shortcodes are supported, e.g.: [contact-form-7]', 'chwebr' ),
                            'type' => 'textarea',
                            'textarea_rows' => '3',
                            'size' => 15
                        ),
                        'box3' => array(
                            'id' => 'subscribe_content',
                            'name' => __( 'Subscribe content', 'chwebr' ),
                            'desc' => __( 'Define the content of the opening toggle subscribe window here. Use formulars, like button, links or any other text. Shortcodes are supported, e.g.: [contact-form-7]', 'chwebr' ),
                            'type' => 'textarea',
                            'textarea_rows' => '3',
                            'size' => 15
                        )
                    )
                ),
                'additional_css' => array(
                    'id' => 'additional_css',
                    'name' => __( 'Custom Styles', 'chwebr' ),
                    'desc' => __( '', 'chwebr' ),
                    'type' => 'add_content',
                    'options' => array(
                        'box1' => array(
                            'id' => 'textarea',
                            'name' => __( 'General CSS', 'chwebr' ),
                            'desc' => __( 'This css is loaded on all pages where the Chwebsocialshare buttons are enabled and it\'s loaded as an additonal inline css on your site', 'chwebr' ),
                            'type' => 'customcss',
                            'textarea_rows' => '3',
                            'size' => 15
                        ),
                        'box2' => array(
                            'id' => 'amp_css',
                            'name' => __( 'AMP CSS', 'chwebr' ),
                            'desc' => sprintf( __( 'This CSS is loaded only on AMP Project pages like yourwebsite.com/amp. <strong>Note: </strong> You need the WordPress <a href="%s" target="_blank">AMP Plugin</a> installed.', 'chwebr' ), 'https://wordpress.org/plugins/amp/' ),
                            'type' => 'textarea',
                            'textarea_rows' => '3',
                            'size' => 15
                        ),
                    )
                ),

                'location_header' => array(
                    'id' => 'location_header',
                    'name' => '<strong>' . __( 'Position', 'chwebr' ) . '</strong>',
                    'desc' => __( '', 'chwebr' ),
                    'type' => 'header'
                ),
                'chwebsocialsharer_position' => array(
                    'id' => 'chwebsocialsharer_position',
                    'name' => __( 'Position', 'chwebr' ),
                    'desc' => __( 'Position of Share Buttons. If this is set to <i>manual</i> use the shortcode function [chwebsocialshare] or use php code <br>&lt;?php echo do_shortcode("[chwebsocialshare]"); ?&gt; in template files. </p>You must activate the option "<strong>Load JS and CSS all over</strong>" if you experience issues with do_shortcode() and the buttons are not shown as expected. See all <a href="https://www.chaudharyweb.com/faq/#Shortcodes" target="_blank">available shortcodes</a>.', 'chwebr' ),
                    'type' => 'select',
                    'options' => array(
                        'before' => __( 'Top', 'chwebr' ),
                        'after' => __( 'Bottom', 'chwebr' ),
                        'both' => __( 'Top and Bottom', 'chwebr' ),
                        'manual' => __( 'Manual', 'chwebr' )
                    )
                ),
                'post_types' => array(
                    'id' => 'post_types',
                    'name' => __( 'Post Types', 'chwebr' ),
                    'desc' => __( 'Select on which post_types the share buttons appear. These values will be ignored when "manual" position is selected.', 'chwebr' ),
                    'type' => 'posttypes'
                ),
                'excluded_from' => array(
                    'id' => 'excluded_from',
                    'name' => __( 'Exclude from post id', 'chwebr' ),
                    'desc' => __( 'Exclude share buttons from a list of post ids. Put in the post id separated by a comma, e.g. 23, 63, 114 ', 'chwebr' ),
                    'type' => 'text',
                    'size' => 'medium'
                ),
                'singular' => array(
                    'id' => 'singular',
                    'name' => __( 'Categories', 'chwebr' ),
                    'desc' => __( 'Enable this checkbox to enable Chwebsocialshare on categories with multiple blogposts. <br><strong>Note: </strong> Post_types: "Post" must be enabled.', 'chwebr' ),
                    'type' => 'checkbox',
                    'std' => '0'
                ),
                'frontpage' => array(
                    'id' => 'frontpage',
                    'name' => __( 'Frontpage', 'chwebr' ),
                    'desc' => __( 'Enable share buttons on frontpage', 'chwebr' ),
                    'type' => 'checkbox'
                ),
            array(
                    'id' => 'shorturl_header',
                    'name' => '<strong>' . __( 'Short URLs', 'chwebr' ) . '</strong>',
                    'desc' => '',
                    'type' => 'header',
                    'size' => 'regular'
                ),
                array(
                    'id' => 'bitly_access_token',
                    'name' => __( 'Bitly access token', 'chwebr' ),
                    'desc' => sprintf(__( 'If you like to use bitly.com shortener get a free bitly access token <a href="%s" target="_blank">here</a>. This turn urls into a format: http://bit.ly/cXnjsh. ', 'chwebr' ), 'https://bitly.com/a/oauth_apps'),
                    'type' => 'text',
                    'size' => 'large'
                ),
                array(
                    'id' => 'google_app_id',
                    'name' => __( 'Google API Key (goo.gl)', 'chwebr' ),
                    'desc' => sprintf(__( 'If you like to use goo.gl shortener get a free Google API key <a href="%s" target="_blank">here</a>. This turn urls into a format: http://goo.gl/cXnjsh. ' . chwebr_check_google_apikey(), 'chwebr' ),'https://console.developers.google.com/'),
                    'type' => 'text',
                    'size' => 'large'
                ),
                array(
                    'id' => 'chwebsu_methods',
                    'name' => __( 'Shorturl method', 'chwebr' ),
                    'desc' => sprintf(__('Bitly generated shortlinks will be converted to the url format: <i>http://bit.ly/1PPg9D9</i><br><br>Goo.gl generated urls look like: <br><i>http://goo.gl/vSJwUV</i><br><br>Using WP Shortlinks converts twitter links into:<br> <i>%s ?p=101</i>', 'chwebr'), get_site_url() ),
                    'type' => 'select',
                    'options' => array(
                        'wpshortlinks' => 'WP Short links',
                        'bitly' => 'Bitly',
                        'google' => 'Goo.gl',
                        'disabled' => 'Short URLs Disabled',
                    )
                ),
                array(
                    'id' => 'shorturl_explanation',
                    'name' => __( 'Important: Read this!', 'chwebr' ),
                    'desc' => __('<strong>The post short url is NOT generated immediatly after first page load!</strong>  Background processing can take up to 1 hour for new posts and 4 - 12 hours for old posts.','chwebr'),
                    'type' => 'renderhr',
                    'size' => 'large'
                ),
                'debug_header' => array(
                    'id' => 'debug_header',
                    'name' => '<strong>' . __( 'Debug', 'chwebr' ) . '</strong>',
                    'desc' => __( '', 'chwebr' ),
                    'type' => 'header'
                ),
                array(
                    'id' => 'disable_cache',
                    'name' => __( 'Disable Cache', 'chwebr' ),
                    'desc' => __( '<strong>Note: </strong>Use this only for testing to see if shares are counted! Your page loading performance will drop. Works only when sharecount is enabled.<br>' . chwebr_cache_status(), 'chwebr' ),
                    'type' => 'checkbox'
                ),
                'delete_cache_objects' => array(
                    'id' => 'delete_cache_objects',
                    'name' => __( 'Attention: Purge DB Cache', 'chwebr' ),
                    'desc' => __( '<strong>Note: </strong>Use this with caution. <strong>This will delete all your twitter counts. They can not be restored!</strong> Activating this option will delete all stored chwebsocialshare post_meta objects.<br>' . chwebr_delete_cache_objects(), 'chwebr' ),
                    'type' => 'checkbox'
                ),
                'debug_mode' => array(
                    'id' => 'debug_mode',
                    'name' => __( 'Debug mode', 'chwebr' ),
                    'desc' => __( '<strong>Note: </strong> Check this box before you get in contact with our support team. This allows us to check publically hidden debug messages on your website. Do not forget to disable it thereafter! Enable this also to write daily sorted log files of requested share counts to folder <strong>/wp-content/plugins/chwebsocialsharer/logs</strong>. Please send us this files when you notice a wrong share count.' . chwebr_log_permissions(), 'chwebr' ),
                    'type' => 'checkbox'
                ),
                'fb_debug' => array(
                    'id' => 'fb_debug',
                    'name' => __( '', 'chwebr' ),
                    'desc' => '',
                    'type' => 'ratelimit'
                ),
            )
        ),
        'licenses' => apply_filters( 'chwebr_settings_licenses', array(
                'licenses_header' => array(
                    'id' => 'licenses_header',
                    'name' => __( 'Activate your Add-Ons', 'chwebr' ),
                    'desc' => chwebr_check_active_addons() ? __('Activate your license key to get important security and feature updates for your Add-On!','chwebr') : sprintf(__('No Add-Ons are active or installed! <a href="%s" target="blank">See all Add-Ons</a>','chwebr'), 'https://www.chaudharyweb.com/add-ons/?utm_source=insideplugin&utm_medium=userwebsite&utm_content=see_all_add_ons&utm_campaign=freeplugin'),
                    'type' => 'header'
                ),)
        ),
        'extensions' => apply_filters( 'chwebr_settings_extension', array()),
        'addons' => apply_filters( 'chwebr_settings_addons', array(
                'addons' => array(
                    'id' => 'addons',
                    'name' => __( '', 'chwebr' ),
                    'desc' => __( '', 'chwebr' ),
                    'type' => 'addons'
                ),
            )
        )
    );

    return $chwebr_settings;
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input
 *
 * @since 1.0.0
 *
 * @param array $input The value input in the field
 *
 * @return string $input Sanitized value
 */
function chwebr_settings_sanitize( $input = array() ) {

    global $chwebr_options;

    if( empty( $_POST['_wp_http_referer'] ) ) {
        return $input;
    }

    parse_str( $_POST['_wp_http_referer'], $referrer );

    $settings = chwebr_get_registered_settings();
    $tab = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';

    $input = $input ? $input : array();
    $input = apply_filters( 'chwebr_settings_' . $tab . '_sanitize', $input );

    // Loop through each setting being saved and pass it through a sanitization filter
    foreach ( $input as $key => $value ) {

        // Get the setting type (checkbox, select, etc)
        $type = isset( $settings[$tab][$key]['type'] ) ? $settings[$tab][$key]['type'] : false;

        if( $type ) {
            // Field type specific filter
            $input[$key] = apply_filters( 'chwebr_settings_sanitize_' . $type, $value, $key );
        }

        // General filter
        $input[$key] = apply_filters( 'chwebr_settings_sanitize', $value, $key );
    }

    // Loop through the whitelist and unset any that are empty for the tab being saved
    if( !empty( $settings[$tab] ) ) {
        foreach ( $settings[$tab] as $key => $value ) {

            // settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
            if( is_numeric( $key ) ) {
                $key = $value['id'];
            }

            if( empty( $input[$key] ) ) {
                unset( $chwebr_options[$key] );
            }
        }
    }

    // Merge our new settings with the existing
    $output = array_merge( $chwebr_options, $input );

    add_settings_error( 'chwebr-notices', '', __( 'Settings updated.', 'chwebr' ), 'updated' );

    return $output;
}

/**
 * Sanitize text fields
 *
 * @since 1.8
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function chwebr_sanitize_text_field( $input ) {
    return trim( $input );
}
add_filter( 'chwebr_settings_sanitize_text', 'chwebr_sanitize_text_field' );

/**
 * Retrieve settings tabs
 *
 * @since 1.8
 * @return string $input Sanitizied value
 */
function chwebr_get_settings_tabs() {

    $settings = chwebr_get_registered_settings();

    $tabs = array();
    $tabs['general'] = __( 'Settings', 'chwebr' );

    if( !empty( $settings['visual'] ) ) {
        $tabs['visual'] = __( 'Visual', 'chwebr' );
    }

    if( !empty( $settings['networks'] ) ) {
        $tabs['networks'] = __( 'Social Networks', 'chwebr' );
    }

    if( !empty( $settings['extensions'] ) ) {
        $tabs['extensions'] = __( 'Add-On Settings', 'chwebr' );
    }

    if( !empty( $settings['licenses'] ) ) {
       // $tabs['licenses'] = __( 'Licenses', 'chwebr' );
    }
    if (false === chwebr_hide_addons()){
    $tabs['addons'] = __( 'Donations for development', 'chwebr' );
    }
    
    //$tabs['misc']      = __( 'Misc', 'chwebr' );

    return apply_filters( 'chwebr_settings_tabs', $tabs );
}

/*
 * Retrieve a list of possible expire cache times
 *
 * @since  2.0.0
 *
 */

function chwebr_get_expiretimes() {
    /* Defaults */
    $times = array(
        '300' => 'in 5 minutes',
        '600' => 'in 10 minutes',
        '1800' => 'in 30 minutes',
        '3600' => 'in 1 hour',
        '21600' => 'in 6 hours',
        '43200' => 'in 12 hours',
        '86400' => 'in 24 hours'
    );
    return $times;
}

/**
 * Retrieve array of  social networks Facebook / Twitter / Subscribe
 *
 * @since 2.0.0
 *
 * @return array Defined social networks
 */
function chwebr_get_networks_list() {

    $networks = get_option( 'chwebr_networks' );
    return apply_filters( 'chwebr_get_networks_list', $networks );
}

/**
 * Page Header Callback
 *
 * Renders the header.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function chwebr_headline_callback( $args ) {
    echo '&nbsp';
}
/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function chwebr_header_callback( $args ) {
    echo '&nbsp';
}

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */

function chwebr_checkbox_callback( $args ) {
    global $chwebr_options;

    $checked = isset( $chwebr_options[$args['id']] ) ? checked( 1, $chwebr_options[$args['id']], false ) : '';
    $html = '<div class="chwebr-admin-onoffswitch">';
    $html .= '<input type="checkbox" class="chwebr-admin-onoffswitch-checkbox" id="chwebr_settings[' . $args['id'] . ']" name="chwebr_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>';
    $html .= '<label class="chwebr-admin-onoffswitch-label" for="chwebr_settings[' . $args['id'] . ']">'
        . '<span class="chwebr-admin-onoffswitch-inner"></span>'
        . '<span class="chwebr-admin-onoffswitch-switch"></span>'
        . '</label>';
    $html .= '</div>';

    echo $html;
}

/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */
function chwebr_multicheck_callback( $args ) {
    global $chwebr_options;

    if( !empty( $args['options'] ) ) {
        foreach ( $args['options'] as $key => $option ):
            if( isset( $chwebr_options[$args['id']][$key] ) ) {
                $enabled = $option;
            } else {
                $enabled = NULL;
            }
            echo '<input name="chwebr_settings[' . $args['id'] . '][' . $key . ']" id="chwebr_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked( $option, $enabled, false ) . '/>&nbsp;';
            echo '<label for="chwebr_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
        endforeach;
        echo '<p class="description chwebr_hidden">' . $args['desc'] . '</p>';
    }
}

/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since 1.3.3
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */
function chwebr_radio_callback( $args ) {
    global $chwebr_options;

    foreach ( $args['options'] as $key => $option ) :
        $checked = false;

        if( isset( $chwebr_options[$args['id']] ) && $chwebr_options[$args['id']] == $key )
            $checked = true;
        elseif( isset( $args['std'] ) && $args['std'] == $key && !isset( $chwebr_options[$args['id']] ) )
            $checked = true;

        echo '<input name="chwebr_settings[' . $args['id'] . ']"" id="chwebr_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( true, $checked, false ) . '/>&nbsp;';
        echo '<label for="chwebr_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
    endforeach;

    echo '<p class="description chwebr_hidden">' . $args['desc'] . '</p>';
}

/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */
function chwebr_text_callback( $args ) {
    global $chwebr_options;

    if( isset( $chwebr_options[$args['id']] ) )
        $value = $chwebr_options[$args['id']];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text" id="chwebr_settings[' . $args['id'] . ']" name="chwebr_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label class="chwebr_hidden" class="chwebr_hidden" for="chwebr_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Number Callback
 *
 * Renders number fields.
 *
 * @since 1.9
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */
function chwebr_number_callback( $args ) {
    global $chwebr_options;

    if( isset( $chwebr_options[$args['id']] ) )
        $value = $chwebr_options[$args['id']];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $max = isset( $args['max'] ) ? $args['max'] : 999999;
    $min = isset( $args['min'] ) ? $args['min'] : 0;
    $step = isset( $args['step'] ) ? $args['step'] : 1;

    $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="chwebr_settings[' . $args['id'] . ']" name="chwebr_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label class="chwebr_hidden" for="chwebr_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */
function chwebr_textarea_callback( $args ) {
    global $chwebr_options;

    if( isset( $chwebr_options[$args['id']] ) )
        $value = $chwebr_options[$args['id']];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : '40';
    $html = '<textarea class="large-text chwebr-textarea" cols="50" rows="' . $size . '" id="chwebr_settings[' . $args['id'] . ']" name="chwebr_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    $html .= '<label class="chwebr_hidden" for="chwebr_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}
/**
 * Custom CSS Callback
 *
 * Renders textarea fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */
function chwebr_customcss1_callback( $args ) {
    global $chwebr_options;

    if( isset( $chwebr_options[$args['id']] ) )
        $value = $chwebr_options[$args['id']];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : '40';
    $html = '<textarea class="large-text chwebr-textarea" cols="50" rows="' . $size . '" id="chwebr_settings[' . $args['id'] . ']" name="chwebr_settings[' . $args['id'] . ']">' . esc_textarea( $value ) . '</textarea>';
    $html .= '<label class="chwebr_hidden" for="chwebr_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Password Callback
 *
 * Renders password fields.
 *
 * @since 1.3
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */
function chwebr_password_callback( $args ) {
    global $chwebr_options;

    if( isset( $chwebr_options[$args['id']] ) )
        $value = $chwebr_options[$args['id']];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="password" class="' . $size . '-text" id="chwebr_settings[' . $args['id'] . ']" name="chwebr_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
    $html .= '<label for="chwebr_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since 1.3.1
 * @param array $args Arguments passed by the setting
 * @return void
 */
function chwebr_missing_callback( $args ) {
    printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'chwebr' ), $args['id'] );
}

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return string
 */
function chwebr_select_callback( $args ) {
    global $chwebr_options;

    if( isset( $chwebr_options[$args['id']] ) )
        $value = $chwebr_options[$args['id']];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<select id="chwebr_settings[' . $args['id'] . ']" name="chwebr_settings[' . $args['id'] . ']"/>';

    foreach ( $args['options'] as $option => $name ) :
        $selected = selected( $option, $value, false );
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label class="chwebr_hidden" for="chwebr_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Multi Select Callback
 *
 * @since 3.0.0
 * @param array $args Arguments passed by the settings
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return string $output dropdown
 */
function chwebr_multiselect_callback( $args = array() ) {
    global $chwebr_options;

    $selected = isset($chwebr_options[$args['id']]) ? $chwebr_options[$args['id']] : '';
    $checked = '';
    
    $html = '<select name="chwebr_settings[' . $args['id'] . '][]" data-placeholder="" style="width:350px;" multiple tabindex="4" class="chwebr-select chwebr-chosen-select">';
    $i = 0;
    foreach ( $args['options'] as $key => $value ) :
        if( is_array($selected)){
            $checked = selected( true, in_array( $key, $selected ), false );
        }
        $html .= '<option value="' . $key . '" ' . $checked . '>' . $value . '</option>';
    endforeach;
    $html .= '</select>';
    echo $html;
}




/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since 2.1.2
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */

function chwebr_color_select_callback( $args ) {
    global $chwebr_options;

    if( isset( $chwebr_options[$args['id']] ) )
        $value = $chwebr_options[$args['id']];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<strong>#:</strong><input type="text" style="max-width:80px;border:1px solid #' . esc_attr( stripslashes( $value ) ) . ';border-right:20px solid #' . esc_attr( stripslashes( $value ) ) . ';" id="chwebr_settings[' . $args['id'] . ']" class="medium-text ' . $args['id'] . ' chwebr-color-box" name="chwebr_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';

    $html .= '</select>';
    $html .= '<label class="chwebr_hidden" for="chwebr_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @global $wp_version WordPress Version
 */
function chwebr_rich_editor_callback( $args ) {
    global $chwebr_options, $wp_version;
    if( isset( $chwebr_options[$args['id']] ) )
        $value = $chwebr_options[$args['id']];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    if( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
        ob_start();
        wp_editor( stripslashes( $value ), 'chwebr_settings_' . $args['id'], array('textarea_name' => 'chwebr_settings[' . $args['id'] . ']', 'textarea_rows' => $args['textarea_rows']) );
        $html = ob_get_clean();
    } else {
        $html = '<textarea class="large-text chwebr-richeditor" rows="10" id="chwebr_settings[' . $args['id'] . ']" name="chwebr_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    }

    $html .= '<br/><label class="chwebr_hidden" for="chwebr_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Upload Callback
 *
 * Renders upload fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */
function chwebr_upload_callback( $args ) {
    global $chwebr_options;

    if( isset( $chwebr_options[$args['id']] ) )
        $value = $chwebr_options[$args['id']];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text chwebr_upload_field" id="chwebr_settings[' . $args['id'] . ']" name="chwebr_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<span>&nbsp;<input type="button" class="chwebr_settings_upload_button button-secondary" value="' . __( 'Upload File', 'chwebr' ) . '"/></span>';
    $html .= '<label class="chwebr_hidden" for="chwebr_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since 1.6
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */
function chwebr_color_callback( $args ) {
    global $chwebr_options;

    if( isset( $chwebr_options[$args['id']] ) )
        $value = $chwebr_options[$args['id']];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $default = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="chwebr-color-picker" id="chwebr_settings[' . $args['id'] . ']" name="chwebr_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
    $html .= '<label class="chwebr_hidden" for="chwebr_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}

function chwebr_networks_callback( $args ) {
    global $chwebr_options;
   
    ob_start();
    ?>
    <p class="chwebr_description"><?php echo $args['desc']; ?></p>
    <table id="chwebr_network_list" class="wp-list-table fixed posts">
    <thead>
    <tr>
        <th scope="col" class='chwebr-network-col' style="padding: 2px 0px 10px 0px"><?php _e( 'Social Network', 'chwebr' ); ?></th>
        <th scope="col" class='chwebr-status-col' style="padding: 2px 0px 10px 10px"><?php _e( 'Status', 'chwebr' ); ?></th>
        <th scope="col" class='chwebr-label-col' style="padding: 2px 0px 10px 10px"><?php _e( 'Custom Label', 'chwebr' ); ?></th>
    </tr>
    </thead>
    <?php
    if( !empty( $args['options'] ) ) {
        foreach ( $args['options'] as $key => $option ):
            echo '<tr id="chwebr_list_' . $key . '" class="chwebr_list_item">';
            if( isset( $chwebr_options[$args['id']][$key]['status'] ) ) {
                $enabled = 1;
            } else {
                $enabled = NULL;
            }
            if( isset( $chwebr_options[$args['id']][$key]['name'] ) ) {
                $name = $chwebr_options[$args['id']][$key]['name'];
            } else {
                $name = NULL;
            }
            
            if ($option === 'Flipboard'){ // Darn you multi color flipboard svg icon.
            echo '<td class="chwebicon-' . strtolower( $option ) . '"><div class="icon"><span class="chweb-path1"></span><span class="chweb-path2"></span><span class="chweb-path3"></span><span class="chweb-path4"></span></div><span class="text">' . $option . '</span></td>';
            } else {
            echo '<td class="chwebicon-' . strtolower( $option ) . '"><span class="icon"></span><span class="text">' . $option . '</span></td>';    
            }
            echo '<td><input type="hidden" name="chwebr_settings[' . $args['id'] . '][' . $key . '][id]" id="chwebr_settings[' . $args['id'] . '][' . $key . '][id]" value="' . strtolower( $option ) . '">';
            echo '<div class="chwebr-admin-onoffswitch">';
            echo '<input name="chwebr_settings[' . $args['id'] . '][' . $key . '][status]" class="chwebr-admin-onoffswitch-checkbox" id="chwebr_settings[' . $args['id'] . '][' . $key . '][status]" type="checkbox" value="1" ' . checked( 1, $enabled, false ) . '/>';
            echo '<label class="chwebr-admin-onoffswitch-label" for="chwebr_settings[' . $args['id'] . '][' . $key . '][status]">'
                . '<span class="chwebr-admin-onoffswitch-inner"></span>'
                . '<span class="chwebr-admin-onoffswitch-switch"></span>'
                . '</label>';
            echo '</div>';
            echo '<td><input type="text" class="medium-text" id="chwebr_settings[' . $args['id'] . '][' . $key . '][name]" name="chwebr_settings[' . $args['id'] . '][' . $key . '][name]" value="' . $name . '"/>';
            echo '</tr>';
        endforeach;
    }
    echo '</table>';
    echo ob_get_clean();
}



/**
 * Registers the Add-Ons field callback for Chwebsocialshare Add-Ons
 *
 * @since 2.0.5
 * @param array $args Arguments passed by the setting
 * @return html
 */
function chwebr_addons_callback( $args ) {
    $html = chwebr_add_ons_page();
    echo $html;
}

/**
 * Registers the image upload field
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the CHWEBR Options
 * @return void
 */
function chwebr_upload_image_callback( $args ) {
    global $chwebr_options;

    if( isset( $chwebr_options[$args['id']] ) )
        $value = $chwebr_options[$args['id']];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text ' . $args['id'] . '" id="chwebr_settings[' . $args['id'] . ']" name="chwebr_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';

    $html .= '<input type="submit" class="button-secondary chwebr_upload_image" name="' . $args['id'] . '_upload" value="' . __( 'Select Image', 'chwebr' ) . '"/>';

    $html .= '<label class="chwebr_hidden" for="chwebr_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label>';

    echo $html;
}

/*
 * Post Types Callback
 *
 * Adds a multiple choice drop box
 * for selecting where Chwebsocialshare should be enabled
 *
 * @since 2.0.9
 * @param array $args Arguments passed by the setting
 * @return void
 *
 */

function chwebr_posttypes_callback( $args ) {
    global $chwebr_options;
    $posttypes = get_post_types();

    //if ( ! empty( $args['options'] ) ) {
    if( !empty( $posttypes ) ) {
        //foreach( $args['options'] as $key => $option ):
        foreach ( $posttypes as $key => $option ):
            if( isset( $chwebr_options[$args['id']][$key] ) ) {
                $enabled = $option;
            } else {
                $enabled = NULL;
            }
            echo '<input name="chwebr_settings[' . $args['id'] . '][' . $key . ']" id="chwebr_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked( $option, $enabled, false ) . '/>&nbsp;';
            echo '<label for="chwebr_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
        endforeach;
        echo '<p class="description chwebr_hidden">' . $args['desc'] . '</p>';
    }
}

/*
 * Note Callback
 *
 * Show a note
 *
 * @since 2.2.8
 * @param array $args Arguments passed by the setting
 * @return void
 *
 */

function chwebr_note_callback( $args ) {
    global $chwebr_options;
    //$html = !empty($args['desc']) ? $args['desc'] : '';
    $html = '';
    echo $html;
}

/**
 * Additional content Callback
 * Adds several content text boxes selectable via jQuery easytabs()
 *
 * @param array $args
 * @return string $html
 * @scince 2.3.2
 */
function chwebr_add_content_callback( $args ) {
    global $chwebr_options;

    $html = '<div id="chwebtabcontainer" class="tabcontent_container"><ul class="chwebtabs" style="width:99%;max-width:500px;">';
    foreach ( $args['options'] as $option => $name ) :
        $html .= '<li class="chwebtab" style="float:left;margin-right:4px;"><a href="#' . $name['id'] . '">' . $name['name'] . '</a></li>';
    endforeach;
    $html .= '</ul>';
    $html .= '<div class="chwebtab-container">';
    foreach ( $args['options'] as $option => $name ) :
        $value = isset( $chwebr_options[$name['id']] ) ? $chwebr_options[$name['id']] : '';
        $textarea = '<textarea class="large-text chwebr-textarea" cols="50" rows="15" id="chwebr_settings[' . $name['id'] . ']" name="chwebr_settings[' . $name['id'] . ']">' . esc_textarea( $value ) . '</textarea>';
        $html .= '<div id="' . $name['id'] . '" style="max-width:500px;"><span style="padding-top:60px;display:block;">' . $name['desc'] . '</span><br>' . $textarea . '</div>';
    endforeach;
    $html .= '</div>';
    $html .= '</div>';
    echo $html;
}

/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since 1.0.8.2
 * @param array $args Arguments passed by the setting
 * @return void
 */
function chwebr_hook_callback( $args ) {
    do_action( 'chwebr_' . $args['id'] );
}

/**
 * Custom Callback for rendering a <hr> line in the settings
 *
 * @since 2.4.7
 * @param array $args Arguments passed by the setting
 * @global $chwebr_options Array of all the Chwebsocialshare Options
 * @return void

 */
if( !function_exists( 'chwebr_renderhr_callback' ) ) {

    function chwebr_renderhr_callback( $args ) {
        $html = '';
        echo $html;
    }

}

/**
 * Set manage_options as the cap required to save CHWEBR settings pages
 *
 * @since 1.9
 * @return string capability required
 */
function chwebr_set_settings_cap() {
    return 'manage_options';
}

add_filter( 'option_page_capability_chwebr_settings', 'chwebr_set_settings_cap' );


/* returns array with amount of available services
 * @since 2.0
 * @return array
 */

function numberServices() {
    $number = 1;
    $array = array();
    while ( $number <= count( chwebr_get_networks_list() ) ) {
        $array[] = $number++;
    }
    $array['all'] = __( 'All Services' );
    return apply_filters( 'chwebr_return_services', $array );
}

/* Purge the Chwebsocialshare
 * database CHWEBR_TABLE
 *
 * @since 2.0.4
 * @return string
 */

function chwebr_delete_cache_objects() {
    global $chwebr_options, $wpdb;
    if( isset( $chwebr_options['delete_cache_objects'] ) ) {
        delete_post_meta_by_key( 'chwebr_timestamp' );
        delete_post_meta_by_key( 'chwebr_shares' );
        delete_post_meta_by_key( 'chwebr_jsonshares' );
        return ' <strong style="color:red;">' . __( 'DB cache deleted! Do not forget to uncheck this box for performance increase after doing the job.', 'chwebr' ) . '</strong> ';
    }
}

/* 
 * Check Cache Status if enabled or disabled
 *
 * @since 2.0.4
 * @return string
 */

function chwebr_cache_status() {
    global $chwebr_options;
    if( isset( $chwebr_options['disable_cache'] ) ) {
        return ' <strong style="color:red;">' . __( 'Transient Cache disabled! Enable it for performance increase.', 'chwebr' ) . '</strong> ';
    }
}

/**
 * Check if cache is deactivated
 * 
 * @global $chwebr_options $chwebr_options
 * @return boolean
 */
function chwebr_is_deactivated_cache() {
    global $chwebr_options;
    if( isset( $chwebr_options['disable_cache'] ) ) {
        return true;
    }
    return false;
}

/**
 * Check if cache gets deleted
 * 
 * @global $chwebr_options $chwebr_options
 * @return boolean
 */
function chwebr_is_deleted_cache() {
    global $chwebr_options;
    if( isset( $chwebr_options['delete_cache_objects'] ) ) {
        return true;
    }
    return false;
}

/* Permission check if logfile is writable
 *
 * @since 2.0.6
 * @return string
 */

function chwebr_log_permissions() {
    global $chwebr_options;
    if( !CHWEBR()->logger->checkDir() ) {
        return '<br><strong style="color:red;">' . __( 'Log file directory not writable! Set FTP permission to 755 or 777 for /wp-content/plugins/chwebsocialsharer/logs/', 'chwebr' ) . '</strong> <br> Read here more about <a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank">file permissions</a> ';
    }
}

/**
 * Sanitizes a string key for CHWEBR Settings
 *
 * Keys are used as internal identifiers. Alphanumeric characters, dashes, underscores, stops, colons and slashes are allowed
 *
 * @since  2.5.5
 * @param  string $key String key
 * @return string Sanitized key
 */
function chwebr_sanitize_key( $key ) {
    $raw_key = $key;
    $key = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );
    /**
     * Filter a sanitized key string.
     *
     * @since 2.5.8
     * @param string $key     Sanitized key.
     * @param string $raw_key The key prior to sanitization.
     */
    return apply_filters( 'chwebr_sanitize_key', $key, $raw_key );
}


function chwebr_return_self($content = array()){
    return $content;
}

/**
 * Check if ChwebSocialShare Add-Ons are installed and active
 *
 * @return boolean true when active
 */
function chwebr_check_active_addons(){

    $content = apply_filters('chwebr_settings_licenses', array());
    if (count($content) > 0){
        return true;
    }
}

/**
 * 
 * Get user roles with capability 'edit_posts'
 * 
 * @global array $wp_roles
 * @return array
 */
function chwebr_get_user_roles() {
    global $wp_roles;
    $roles = array();

    foreach ( $wp_roles->roles as $role ) {
        if( isset( $role["capabilities"]["edit_posts"] ) && $role["capabilities"]["edit_posts"] === true ) {
            $value = str_replace( ' ', null, strtolower( $role["name"] ) );
            $roles[$value] = $role["name"];
        }
    }
    return $roles;
}

/**
 * Render Button for oauth authentication and access token generation
 * @global $chwebr_options $chwebr_options
 * @param type $args
 */
function chwebr_fboauth_callback( $args ) {
    global $chwebr_options;
    
    if( isset( $chwebr_options[$args['id']] ) ){
        $value = $chwebr_options[$args['id']];
    }else{        
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }
    // Change expiration date
    if( isset( $chwebr_options['expire_'.$args['id']] ) ){
        $expire = $chwebr_options['expire_'.$args['id']];
    }else{        
        $expire = '';
    }
    
    $button_label = __('Verify Access Token', 'chwebr');

    $html = '<a href="#" id="chwebr_verify_fbtoken" class="button button-primary">'.$button_label.'</a>';
    $html .= '&nbsp; <input type="text" class="medium-text" style="width:333px;" id="chwebr_settings[' . $args['id'] . ']" name="chwebr_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '&nbsp; <input type="hidden" class="medium-text" id="chwebr_settings[expire_' . $args['id'] . ']" name="chwebr_settings[expire_' . $args['id'] . ']" value="' . esc_attr( stripslashes( $expire ) ) . '"/>';
    $html .= '<div class="token_status">'
            . '<span id="chwebr_expire_token_status"></span>'
            . '<span id="chwebr_token_notice"></span>'
            . '</div>';
    
echo $html;
    
}
//function chwebr_fboauth_callback( $args ) {
//    global $chwebr_options;
//    
//    if( isset( $chwebr_options[$args['id']] ) ){
//        $value = $chwebr_options[$args['id']];
//    }else{        
//        $value = isset( $args['std'] ) ? $args['std'] : '';
//    }
//    // Change expiration date
//    if( isset( $chwebr_options['expire_'.$args['id']] ) ){
//        $expire = $chwebr_options['expire_'.$args['id']];
//    }else{        
//        $expire = '';
//    }
//    
//    $button_label = empty($chwebr_options[$args['id']]) ? __('Get Access Token | Facebook Login', 'chwebr') : __('Renew Access Token', 'chwebr');
//
//    $auth_url = 'https://www.chaudharyweb.com/oauth/login.html'; // production
//
//    $html = '<a href="'.$auth_url.'" id="chwebr_fb_auth" class="button button-primary">'.$button_label.'</a>';
//    //$html .= empty($chwebr_options[$args['id']]) ? $verify_button : '';
//    $html .= '&nbsp; <input type="text" class="medium-text" id="chwebr_settings[' . $args['id'] . ']" name="chwebr_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
//    $html .= '&nbsp; <input type="hidden" class="medium-text" id="chwebr_settings[expire_' . $args['id'] . ']" name="chwebr_settings[expire_' . $args['id'] . ']" value="' . esc_attr( stripslashes( $expire ) ) . '"/>';
//    $html .= '<div class="token_status">'
//            . '<span id="chwebr_expire_token_status"></span>'
//            . '<span id="chwebr_token_notice"></span>'
//            . '</div>';
//    
//echo $html;
//    
//}

/**
 * Test facebook api and check if site is rate limited
 * 
 * @global array $chwebr_options
 * @return string
 */
function chwebr_ratelimit_callback() {
        global $chwebr_options;


        if( !chwebr_is_admin_page() || !isset( $chwebr_options['debug_mode'] ) || !function_exists( 'curl_init' ) ) {
            return '';
        }
        // Test open facebook api endpoint
        $url = 'http://graph.facebook.com/?id=http://www.google.com';
        $curl_handle = curl_init();
        curl_setopt( $curl_handle, CURLOPT_URL, $url );
        curl_setopt( $curl_handle, CURLOPT_CONNECTTIMEOUT, 2 );
        curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, 1 );
        $buffer = curl_exec( $curl_handle );
        curl_close( $curl_handle );
        echo '<div style="min-width:500px;"><strong>Testing facebook public API <br><br>Result for google.com: </strong></div>';
        if( empty( $buffer ) ) {
            print "Nothing returned from url.<p>";
        } else {
            print '<div style="max-width:200px;">' . $buffer . '</div>';
        }
        
        // Test facebook api with access token
        $url = 'https://graph.facebook.com/v2.7/?id=http://www.google.com&access_token=' . $chwebr_options['fb_access_token_new'];
        $curl_handle = curl_init();
        curl_setopt( $curl_handle, CURLOPT_URL, $url );
        curl_setopt( $curl_handle, CURLOPT_CONNECTTIMEOUT, 2 );
        curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, 1 );
        $buffer = curl_exec( $curl_handle );
        curl_close( $curl_handle );
        echo '<br><strong>Testing facebook API <br>with access token<br><br>Result for google.com: </strong>';
        if( empty( $buffer ) ) {
            print "Nothing returned from url.<p>";
        } else {
            print '<div style="max-width:200px;">' . $buffer . '</div>';
        }
        
        
    }

    /**
 * Helper function to determine if adverts and add-on ressources are hidden
 * 
 * @return bool
 */
function chwebr_hide_addons(){
    return apply_filters('chwebr_hide_addons', false);
}
