<?php
/**
 * Weclome Page Class
 *
 * @package     CHWEBR
 * @subpackage  Admin/Welcome
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * CHWEBR_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since 1.4
 */
class CHWEBR_Welcome {

    /**
     * @var string The capability users should have to view the page
     */
    public $minimum_capability = 'manage_options';

    /**
     * Get things started
     *
     * @since 1.0.1
     */
    public function __construct() {
        add_action( 'admin_menu', array($this, 'admin_menus') );
        add_action( 'admin_head', array($this, 'admin_head') );
        add_action( 'admin_init', array($this, 'welcome') );
    }

    /**
     * Register the Dashboard Pages which are later hidden but these pages
     * are used to render the Welcome and Credits pages.
     *
     * @access public
     * @since 1.4
     * @return void
     */
    public function admin_menus() {
        // About Page
        add_dashboard_page(
                __( 'Welcome to ChwebSocialShare', 'chwebr' ), __( 'Welcome to ChwebSocialShare', 'chwebr' ), $this->minimum_capability, 'chwebr-about', array($this, 'about_screen')
        );

        // Changelog Page
        $chwebr_about = add_dashboard_page(
                __( 'ChwebSocialShare Changelog', 'chwebr' ), __( 'ChwebSocialShare Changelog', 'chwebr' ), $this->minimum_capability, 'chwebr-changelog', array($this, 'changelog_screen')
        );

        // Getting Started Page
        $chwebr_quickstart = add_submenu_page(
                'chwebr-settings', __( 'Quickstart', 'chwebr' ), __( 'Quickstart', 'chwebr' ), $this->minimum_capability, 'chwebr-getting-started', array($this, 'getting_started_screen')
        );

        // Credits Page
        $chwebr_credits = add_dashboard_page(
                __( 'The people that build ChwebSocialShare', 'chwebr' ), __( 'The people that build ChwebSocialShare', 'chwebr' ), $this->minimum_capability, 'chwebr-credits', array($this, 'credits_screen')
        );
    }

    /**
     * Hide Individual Dashboard Pages
     *
     * @access public
     * @since 1.4
     * @return void
     */
    public function admin_head() {
        remove_submenu_page( 'index.php', 'chwebr-about' );
        remove_submenu_page( 'index.php', 'chwebr-changelog' );
        remove_submenu_page( 'index.php', 'chwebr-getting-started' );
        remove_submenu_page( 'index.php', 'chwebr-credits' );
        ?>
        
        <style type="text/css" media="screen">
        /*<![CDATA[*/

        .chwebr-about-wrap .chwebr-badge { float: right; border-radius: 4px; margin: 0 0 15px 15px; max-width: 100px; }
        .chwebr-about-wrap #chwebr-header { margin-bottom: 15px; }
        .chwebr-about-wrap #chwebr-header h1 { margin-bottom: 15px !important; }
        .chwebr-about-wrap .about-text { margin: 0 0 15px; max-width: 670px; }
        .chwebr-about-wrap .feature-section { margin-top: 20px; }
        .chwebr-about-wrap .feature-section-content,
        .chwebr-about-wrap .feature-section-media { width: 50%; box-sizing: border-box; }
        .chwebr-about-wrap .feature-section-content { float: left; padding-right: 50px; }
        .chwebr-about-wrap .feature-section-content h4 { margin: 0 0 1em; }
        .chwebr-about-wrap .feature-section-media { float: right; text-align: right; margin-bottom: 20px; }
        .chwebr-about-wrap .feature-section-media img { border: 1px solid #ddd; }
        .chwebr-about-wrap .feature-section:not(.under-the-hood) .col { margin-top: 0; }
        /* responsive */
        @media all and ( max-width: 782px ) {
            .chwebr-about-wrap .feature-section-content,
            .chwebr-about-wrap .feature-section-media { float: none; padding-right: 0; width: 100%; text-align: left; }
            .chwebr-about-wrap .feature-section-media img { float: none; margin: 0 0 20px; }
        }
        /*]]>*/
        </style>
        
        <?php
    }

    /**
     * Render Getting Started Screen
     *
     * @access public
     * @since 1.9
     * @return void
     */
    public function getting_started_screen() {
        global $chwebr_redirect;
        ?>
        <div class="wrap about-wrap chwebr-about-wrap">
            <?php
            // load welcome message and content tabs
            $this->welcome_message();
            $this->tabs();
            ?>
            <?php if (isset($_GET['redirect'])) {?>
            <p class="about-description chwebr-notice notice-success"><?php _e( 'Facebook and Twitter Share Buttons are successfully enabled on all your posts! <br> Now you can use the steps  below to customize ChwebSocialShare to your needs.', 'chwebr' ); ?></p>
            <?php } ?>
            <div class="changelog">
                <h2><?php _e( 'Create Your First Social Sharing Button', 'chwebr' ); ?></h2>
                <div class="feature-section">
                    <div class="feature-section-media">
                        <img style="display:none;" src="<?php echo CHWEBR_PLUGIN_URL . 'assets/images/screenshots/social-networks-settings.png'; ?>" class="chwebr-welcome-screenshots"/>
                    </div>
                    <div class="feature-section-content">
                        <h4>Step 1: Go to <a href="<?php echo admin_url( 'admin.php?page=chwebr-settings#chwebr_settingsservices_header' ) ?>" target="blank"><?php _e( 'Settings &rarr; Networks', 'chwebr' ); ?></a></h4>
                        <p><?php _e( 'The Social Network menu is your general access point for activating the desired share buttons and for customizing the share button label', 'chwebr' ); ?></p>
                        <h4>Step 2: Go to <a href="<?php echo admin_url( 'admin.php?page=chwebr-settings#chwebr_settingslocation_header' ) ?>" target="blank"><?php _e( 'Settings &rarr; Position', 'chwebr' ); ?></a></h4>
                        <p><?php _e( 'Select the location and exact position of the share buttons within your content', 'chwebr' ); ?></p>
                        <h3><?php _e('You are done! Easy, isn\'t it?', 'chwebr'); ?></h3>
                        <p></p>
                            
                    </div>
                </div>
            </div>

            <div class="changelog">
                <h2><?php _e( 'Display a Most Shared Post Widget', 'chwebr' ); ?></h2>
                <div class="feature-section">
                    <div class="feature-section-media">
                        &nbsp;
                    </div>
                    <div class="feature-section-content">
                        <h4><a href="<?php echo admin_url( 'widgets.php' ) ?>" target="blank"><?php _e( 'Appearance &rarr; Widgets', 'chwebr' ); ?></a></h4>

                        <p><?php _e( 'Drag and drop the widget </br> "<i>ChwebSocialShare - Most Shared Posts</i>" </br>into the desired widget location and save it', 'chwebr' ); ?></p>
                        <img style="display:none;" src="<?php echo CHWEBR_PLUGIN_URL . 'assets/images/screenshots/most-shared-posts.png'; ?>"/>

                    </div>
                </div>
            </div>

            <div class="changelog">
                <h2><?php _e( 'Content Shortcodes', 'chwebr' ); ?></h2>
                <div class="feature-section">
                    <div class="feature-section-media">
                        <img style="display:none;" src="<?php echo CHWEBR_PLUGIN_URL . 'assets/images/screenshots/shortcodes.png'; ?>"/>
                    </div>
                    <div class="feature-section-content">
                        <p>
                            <?php _e( 'Add Share buttons manually with using the shortcode <i style="font-weight:bold;">[chwebsocialshare]</i>.', 'chwebr' ); ?>
                        </p>
                        <?php _e( 'Paste the shortcode in content of your posts or pages with the post editor at the place you want the share buttons appear', 'chwebr' ); ?>
                        <p>
                            <?php echo sprintf(__( 'There are various parameters you can use for the chwebsocialshare shortcode. Find a list of all available shortcode parameters <a href="%s" target="blank">here</a>', 'chwebr'), 'http://docs.chaudharyweb.com/article/67-shortcodes'); ?><br>
                        </p>
                    </div>
                </div>
            </div>
            <div class="changelog">
                <h2><?php _e( 'PHP Template Shortcode', 'chwebr' ); ?></h2>
                <div class="feature-section">
                    <div class="feature-section-media">
s                    </div>
                    <div class="feature-section-content">
                        <p>
                            <?php _e( 'Add ChwebSocialShare directly into your theme template files with using the PHP code <i style="font-weight:bold;">&lt;?php do_shortcode(\'[chwebsocialshare]\'); ?&gt;</i>', 'chwebr' ); ?>
                        </p>
                            
                        <p>
                            <?php echo sprintf(__( 'There are various parameters you can use for the chwebsocialshare shortcode. Find a list of all available shortcode parameters <a href="%s" target="blank">here</a>', 'chwebr'), 'https://www.chaudharyweb.com/documentation/shortcodes/'); ?><br>
                        </p>
                    </div>
                </div>
            </div>

            <div class="changelog">
                <h2><?php _e( 'Need Help?', 'chwebr' ); ?></h2>
                <div class="feature-section two-col">
                    <div>
                        <h3><?php _e( 'Great Support', 'chwebr' ); ?></h3>
                        <p><?php _e( 'We do our best to provide the best support we can. If you encounter a problem or have a question, simply open a ticket using our <a href="https://www.chaudharyweb.com/contact-developer/" target="blank">support form</a>.', 'chwebr' ); ?></p>
                        <ul id="chweb-social-admin-head">
                            <?php echo chwebr_share_buttons(); ?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
        <?php
    }

    /**
     * Welcome message
     *
     * @access public
     * @since 2.5
     * @return void
     */
    public function welcome_message() {
        list( $display_version ) = explode( '-', CHWEBR_VERSION );
        ?>
        <div id="chwebr-header">
            <!--<img class="chwebr-badge" src="<?php //echo  . 'assets/images/chwebr-logo.svg';  ?>" alt="<?php //_e( 'ChwebSocialShare', 'chwebr' );  ?>" / >//-->
            <h1><?php printf( __( 'Welcome to ChwebSocialShare %s', 'chwebr' ), $display_version ); ?></h1>
            <p class="about-text">
                <?php _e( 'Thank you for updating to the latest version! ChwebSocialShare is installed and ready to grow your traffic from social networks!', 'chwebr' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Render About Screen
     *
     * @access public
     * @since 1.4
     * @return void
     */
    public function about_screen() {
        ?>
        <div class="wrap about-wrap chwebr-about-wrap">
            <?php
            // load welcome message and content tabs
            $this->welcome_message();
            $this->tabs();
            ?>
            <div class="changelog">
                <div class="feature-section">
                    <div class="feature-section-content">
                        <!--
                        <h2><?php //_e( 'Use Facebook Connect to Skyrocket Share Count', 'chwebr' ); ?></h2>
                        <p><?php //_e( 'ChwebSocialShare is the first Social Media plugin that uses the brandnew Facebook Connect Integration to bypass the regular facebook API limit which has been introduced recently. <p>It allows you up to 200 API calls per hour to the facebook server. This is more than enough for even huge traffic sites as ChwebSocialShare is caching all share counts internally. <p>We are convinced that other social media plugins are going to copy our solution soon... and we will be proud of it;) <p> Your site becomes immediately better than the rest because you are the one whose website is running with full social sharing power. Other sites share count still stucks and are delayed and they do not know it;)', 'chwebr' ); ?></p>
                        <img src="<?php //echo CHWEBR_PLUGIN_URL . 'assets/images/screenshots/oauth.png'; ?>"/>
                        //-->
                        <p></p>
                        <h2><?php _e( 'A New Beautiful Sharing Widget', 'chwebr' ); ?></h2>
                        <p><?php _e( 'We have heard your wishes so the new widget contains the long requested post thumbnail and a beautiful css which gives your side bar sharing super power.', 'chwebr' ); ?></p>
                        <img src="<?php echo CHWEBR_PLUGIN_URL . 'assets/images/screenshots/widget.png'; ?>"/>
                        <p></p>
                        <h2><?php _e( 'Better Customization Options', 'chwebr' ); ?></h2>
                        <p><?php _e( 'Select from 3 ready to use sizes to make sure that ChwebSocialShare is looking great on your site. No matter if you prefer small, medium or large buttons.', 'chwebr' ); ?></p>
                        <img src="<?php echo CHWEBR_PLUGIN_URL . 'assets/images/screenshots/different_sizes.gif'; ?>"/>
                        <p></p>
                        <h2><?php _e( 'Asyncronous Share Count Aggregation', 'chwebr' ); ?></h2>
                        <p><?php _e( 'With ChwebSocialShare you get our biggest performance update. Use the new <i>Async Cache Refresh</i> method and your share counts will be aggregated only after page loading and never while page loads. This is a huge performance update.', 'chwebr' ); ?></p>
                        <img src="<?php echo CHWEBR_PLUGIN_URL . 'assets/images/screenshots/async_cache_refresh.png'; ?>"/>
                        <p></p>
                        <h2><?php _e( 'Open Graph and Twitter Card Integration', 'chwebr' ); ?></h2>
                        <p><?php _e( 'Use open graph and twitter card to specify the content you like to share. If you are using Yoast, ChwebSocialShare will use the Yoast open graph data instead and extend it with custom data to get the maximum out of your valuable content.', 'chwebr' ); ?></p>
                        <p></p>
                        
                        <img src="<?php echo CHWEBR_PLUGIN_URL . 'assets/images/screenshots/social_sharing_settings.png'; ?>"/>
                        <p></p>
                        <h2><?php _e( 'Great Responsive Buttons', 'chwebr' ); ?></h2>
                        <p><?php _e( 'ChwebSocialShare arrives you with excellent responsive support. So the buttons look great on mobile and desktop devices. If you want more customization options for mobile devices you can purchase the responsive Add-On', 'chwebr' ); ?></p>
                        <p></p>
                        <h2><?php _e( 'Share Count Dashboard', 'chwebr' ); ?></h2>
                        <p><?php _e( 'See the shares of your posts at a glance on the admin posts listing:', 'chwebr' ); ?></p>
                        <p></p>
                        <img alt="Share count dashboard" title="Share count dashboard" src="<?php echo CHWEBR_PLUGIN_URL . 'assets/images/screenshots/dashboard.png'; ?>"/>
                        <p></p>
                        <h2><?php _e( 'A much cleaner user interface', 'chwebr' ); ?></h2>
                        <p><?php _e( 'We spent a lot of time to make useful first time settings and improved the user interface for an easier experience.', 'chwebr' ); ?></p>
                        <p></p>
                    </div>
                </div>
            </div>


            <div class="changelog">
                <h2><?php _e( 'Additional Updates', 'chwebr' ); ?></h2>
                <div class="feature-section three-col">
                    <div class="col">
                        <h4><?php _e( 'Developer Friendly', 'chwebr' ); ?></h4>
                        <p><?php echo sprintf(__( 'Are you a theme developer and want to use ChwebSocialShare as your build in share count aggregator? Read the <a href="%s" target="blank">developer instructions.</a>', 'chwebr' ), 'https://www.chaudharyweb.com/documentation/developer-instruction-for-commercial-theme-integration/'); ?></p>
                    </div>
                    <div class="col">
                        <h4><?php _e( 'Check Open Graph Settings', 'chwebr' ); ?></h4>
                        <p><?php _e( 'Use the <i>Validate Open Graph Data</i> button and check if the open graph data on your site is working as expected or conflicts with other open graph data.', 'chwebr' ); ?></p>
                    </div>
                    <div class="col">
                        <h4><?php _e( 'Use Yoast SEO Title', 'chwebr' ); ?></h4>
                        <p><?php _e( 'ChwebSocialShare will use the YOAST SEO title if it is defined.', 'chwebr' ); ?></p>
                    </div>
                </div>
            </div>

            <div class="return-to-dashboard">
                <a href="<?php echo esc_url( admin_url( add_query_arg( array('page' => 'chwebr-settings&tab=visual#chwebr_settingslocation_header'), 'edit.php' ) ) ); ?>"><?php _e( 'Go to ChwebSocialShare Settings', 'chwebr' ); ?></a> &middot;
                <a href="<?php echo esc_url( admin_url( add_query_arg( array('page' => 'chwebr-changelog'), 'admin.php' ) ) ); ?>"><?php _e( 'View the Full Changelog', 'chwebr' ); ?></a>
                <ul id="chweb-social-admin-head">
                    <?php echo chwebr_share_buttons(); ?>
                </ul>
            </div>
            

        </div>
        <?php
    }

    /**
     * Navigation tabs
     *
     * @access public
     * @since 1.9
     * @return void
     */
    public function tabs() {
        $selected = isset( $_GET['page'] ) ? $_GET['page'] : 'chwebr-about';
        ?>
        <h1 class="nav-tab-wrapper">
            <a class="nav-tab <?php echo $selected == 'chwebr-about' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array('page' => 'chwebr-about'), 'admin.php' ) ) ); ?>">
                <?php _e( "What's New", 'chwebr' ); ?>
            </a>
            <a class="nav-tab <?php echo $selected == 'chwebr-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array('page' => 'chwebr-getting-started'), 'admin.php' ) ) ); ?>">
                <?php _e( 'Getting Started', 'chwebr' ); ?>
            </a>
            <a class="nav-tab <?php echo $selected == 'chwebr-credits' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array('page' => 'chwebr-credits'), 'admin.php' ) ) ); ?>">
                <?php _e( 'Credits', 'chwebr' ); ?>
            </a>
        </h1>
        <?php
    }

    /**
     * Render Credits Screen
     *
     * @access public
     * @since 3.0.0
     * @return void
     */
    public function credits_screen() {
        ?>
        <div class="wrap about-wrap chwebr-about-wrap">
            <?php
            // load welcome message and content tabs
            $this->welcome_message();
            $this->tabs();
            ?>
            <p class="about-description"><?php _e( 'Chwebsocialshare is created by a Rajesh Chaudhary and developers all over the world who aim to provide the #1 ecosystem for growing social media traffic through WordPress.', 'chwebr' ); ?></p>

            <?php echo $this->contributors(); ?>
            <p class="small"><?php echo sprintf(__(' If you want to be credited here participate on the development and  make your pull request on <a href="%s" target="_blank">github</a>',' chwebr'), 'https://github.com/rajeshdnws')?></p>
            <ul id="chweb-social-admin-head">
                <?php echo chwebr_share_buttons(); ?>
            </ul>
        </div>
        <?php
    }

    /**
     * Render Contributors List
     *
     * @since 3.0.0
     * @uses CHWEBR_Welcome::get_contributors()
     * @return string $contributor_list HTML formatted list of all the contributors for CHWEBR
     */
    public function contributors() {
        $contributors = $this->get_contributors();

        if( empty( $contributors ) )
            return '';

        $contributor_list = '<ul class="wp-people-group">';

       /* foreach ( $contributors as $contributor ) {
            $contributor_list .= '<li class="wp-person">';
            $contributor_list .= sprintf( '<a href="%s" title="%s">', esc_url( 'https://github.com/' . $contributor->login ), esc_html( sprintf( __( 'View %s', 'chwebr' ), $contributor->login ) )
            );
            $contributor_list .= sprintf( '<img src="%s" width="64" height="64" class="gravatar" alt="%s" />', esc_url( $contributor->avatar_url ), esc_html( $contributor->login ) );
            $contributor_list .= '</a>';
            $contributor_list .= sprintf( '<a class="web" href="%s">%s</a>', esc_url( 'https://github.com/' . $contributor->login ), esc_html( $contributor->login ) );
            $contributor_list .= '</a>';
            $contributor_list .= '</li>';
        }*/

        $contributor_list .= '</ul>';

        return $contributor_list;
    }

    /**
     * Retreive list of contributors from GitHub.
     *
     * @access public
     * @since 3.0.0
     * @return array $contributors List of contributors
     */
    public function get_contributors() {
        $contributors = get_transient( 'chwebr_contributors' );

        if( false !== $contributors ){
            return $contributors;
        }

        $response = wp_remote_get( '', array('sslverify' => false) );

        if( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ){
            return array();
        }

        $contributors = json_decode( wp_remote_retrieve_body( $response ) );

        if( !is_array( $contributors ) ){
            return array();
        }

        set_transient( 'chwebr_contributors', $contributors, 3600 );

        return $contributors;
    }

    /**
     * Parse the CHWEBR readme.txt file
     *
     * @since 3.0.0
     * @return string $readme HTML formatted readme file
     */
    public function parse_readme() {
        $file = file_exists( CHWEBR_PLUGIN_DIR . 'readme.txt' ) ? CHWEBR_PLUGIN_DIR . 'readme.txt' : null;

        if( !$file ) {
            $readme = '<p>' . __( 'No valid changelog was found.', 'chwebr' ) . '</p>';
        } else {
            $readme = file_get_contents( $file );
            $readme = nl2br( esc_html( $readme ) );
            $readme = explode( '== Changelog ==', $readme );
            $readme = end( $readme );

            $readme = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $readme );
            $readme = preg_replace( '/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme );
            $readme = preg_replace( '/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme );
            $readme = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $readme );
            $readme = preg_replace( '/\[(.*?)\]\((.*?)\)/', '<a href="\\2">\\1</a>', $readme );
        }

        return $readme;
    }

    /**
     * Render Changelog Screen
     *
     * @access public
     * @since 2.0.3
     * @return void
     */
    public function changelog_screen() {
        ?>
        <div class="wrap about-wrap chwebr-about-wrap">
            <?php
            // load welcome message and content tabs
            $this->welcome_message();
            $this->tabs();
            ?>
            <div class="changelog">
                <h3><?php _e( 'Full Changelog', 'chwebr' ); ?></h3>

                <div class="feature-section">
                    <?php echo $this->parse_readme(); ?>
                </div>
            </div>

            <div class="return-to-dashboard">
                <a href="<?php echo esc_url( admin_url( add_query_arg( array('page' => 'chwebr-settings&tab=visual#chwebr_settingslocation_header'), 'edit.php' ) ) ); ?>"><?php _e( 'Go to ChwebSocialShare Settings', 'chwebr' ); ?></a>
            </div>
        </div>
        <?php
    }

    /**
     * Sends user to the Settings page on first activation of CHWEBR as well as each
     * time CHWEBR is upgraded to a new version
     *
     * @access public
     * @since 1.0.1
     * @global $chwebr_options Array of all the CHWEBR Options
     * @return void
     */
    public function welcome() {
        global $chwebr_options;

        // Bail if no activation redirect
        if( !get_transient( '_chwebr_activation_redirect' ) ){
            return;
        }
        
        // Delete the redirect transient
        delete_transient( '_chwebr_activation_redirect' );

        // Bail if activating from network, or bulk
        if( is_network_admin() || isset( $_GET['activate-multi'] ) )
            return;

        $upgrade = get_option( 'chwebr_version_upgraded_from' );

        if( !$upgrade ) { // First time install
            wp_safe_redirect( admin_url( 'admin.php?page=chwebr-getting-started&redirect=1' ) );
            exit;
        } else { // Update
            wp_safe_redirect( admin_url( 'admin.php?page=chwebr-about&redirect=1' ) );
            exit;
        }
    }

}

new CHWEBR_Welcome();
