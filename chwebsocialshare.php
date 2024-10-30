<?php

/**
 * Plugin Name: Chwebsocialshare Share Buttons
 * Plugin URI: https://www.chaudharyweb.com
 * Description: Chwebsocialshare is a Share functionality inspired by the the great website Chwebable for Facebook and Twitter. More networks available.
 * Author: Rajesh Chaudhary
 * Author URI: https://www.chaudharyweb.com
 * Version: 1.0.0
 * Text Domain: chwebr
 * Domain Path: /languages
 * Chwebsocialshare Share Buttons is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 1 of the License, or
 * any later version.
 *
 * Chwebsocialshare Share Buttons is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Chwebsocialshare Share Buttons. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package CHWEBR
 * @category Core
 * @author Rajesh Chaudhary
 * @version 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

// Plugin version
if( !defined( 'CHWEBR_VERSION' ) ) {
    define( 'CHWEBR_VERSION', '1.0.0' );
}

// Debug mode
if( !defined( 'CHWEBR_DEBUG' ) ) {
    define( 'CHWEBR_DEBUG', false );
}


if( !class_exists( 'chwebsocialshare' ) ) :

    /**
     * Main chwebr Class
     *
     * @since 1.0.0
     */
    final class Chwebsocialshare {
        /** Singleton ************************************************************ */

        /**
         * @var Chwebsocialshare The one and only Chwebsocialshare
         * @since 1.0
         */
        private static $instance;

        /**
         * CHWEBR HTML Element Helper Object
         *
         * @var object
         * @since 2.0.0
         */
        public $html;

        /* CHWEBR LOGGER Class
         * 
         */
        public $logger;
        
        /**
         * CHWEBR TEMPLATE Object
         * @var object 
         */
        public $template;

        /**
         * Main Chwebsocialshare Instance
         *
         * Insures that only one instance of chwebsocialshare exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 1.0
         * @static
         * @staticvar array $instance
         * @uses chwebsocialshare::setup_constants() Setup the constants needed
         * @uses chwebsocialshare::includes() Include the required files
         * @uses chwebsocialshare::load_languages() load the language files
         * @see CHWEBR()
         * @return The one true chwebsocialshare
         */
        public static function instance() {
            if( !isset( self::$instance ) && !( self::$instance instanceof Chwebsocialshare ) ) {
                self::$instance = new Chwebsocialshare;
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_languages();
                self::$instance->html = new CHWEBR_HTML_Elements();
                self::$instance->logger = new chwebrLogger( "chweblog_" . date( "Y-m-d" ) . ".log", chwebrLogger::INFO );
                self::$instance->template = new chwebrBuildTemplates();
            }
            return self::$instance;
        }

        /**
         * Throw error on object clone
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         *
         * @since 1.0
         * @access protected
         * @return void
         */
        public function __clone() {
            // Cloning instances of the class is forbidden
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'CHWEBR' ), '1.0' );
        }

        /**
         * Disable unserializing of the class
         *
         * @since 1.0
         * @access protected
         * @return void
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'CHWEBR' ), '1.0' );
        }

        /**
         * Setup plugin constants
         *
         * @access private
         * @since 1.0
         * @return void
         */
        private function setup_constants() {
            global $wpdb;

            // Plugin Folder Path
            if( !defined( 'CHWEBR_PLUGIN_DIR' ) ) {
                define( 'CHWEBR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }

            // Plugin Folder URL
            if( !defined( 'CHWEBR_PLUGIN_URL' ) ) {
                define( 'CHWEBR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }

            // Plugin Root File
            if( !defined( 'CHWEBR_PLUGIN_FILE' ) ) {
                define( 'CHWEBR_PLUGIN_FILE', __FILE__ );
            }

            // Plugin database
            // Plugin Root File
            if( !defined( 'CHWEBR_TABLE' ) ) {
                define( 'CHWEBR_TABLE', $wpdb->prefix . 'chwebsocialsharer' );
            }
        }

        /**
         * Include required files
         *
         * @access private
         * @since 1.0
         * @return void
         */
        private function includes() {
            global $chwebr_options;

            require_once CHWEBR_PLUGIN_DIR . 'includes/admin/settings/register-settings.php';
            $chwebr_options = chwebr_get_settings();
            require_once CHWEBR_PLUGIN_DIR . 'includes/scripts.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/template-functions.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/class-chwebr-license-handler.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/class-chwebr-html-elements.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/debug/classes/ChwebDebug.interface.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/debug/classes/ChwebDebug.class.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/logger.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/actions.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/helper.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/class-chwebr-shared-posts-widget.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/admin/settings/metabox-settings.php'; /* move into is_admin */
            require_once CHWEBR_PLUGIN_DIR . 'includes/admin/meta-box/meta-box.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/header-meta-tags.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/class-build-templates.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/sharecount-functions.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/shorturls.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/libraries/class-google-shorturl.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/libraries/class-bitly-shorturl.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/admin/tracking.php'; // Ensure cron is loading even on frontpage
            require_once CHWEBR_PLUGIN_DIR . 'includes/debug/debug.php';
            require_once CHWEBR_PLUGIN_DIR . 'includes/amp.php';

            if( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
                require_once CHWEBR_PLUGIN_DIR . 'includes/install.php';
                require_once CHWEBR_PLUGIN_DIR . 'includes/admin/add-ons.php';
                require_once CHWEBR_PLUGIN_DIR . 'includes/admin/admin-actions.php';
                require_once CHWEBR_PLUGIN_DIR . 'includes/admin/admin-notices.php';
                require_once CHWEBR_PLUGIN_DIR . 'includes/admin/admin-footer.php';
                require_once CHWEBR_PLUGIN_DIR . 'includes/admin/admin-pages.php';
                require_once CHWEBR_PLUGIN_DIR . 'includes/admin/plugins.php';
                require_once CHWEBR_PLUGIN_DIR . 'includes/admin/welcome.php';
                require_once CHWEBR_PLUGIN_DIR . 'includes/admin/settings/display-settings.php';
                require_once CHWEBR_PLUGIN_DIR . 'includes/admin/settings/contextual-help.php';
                require_once CHWEBR_PLUGIN_DIR . 'includes/admin/settings/user-profiles.php';
                require_once CHWEBR_PLUGIN_DIR . 'includes/admin/tools.php';
                require_once CHWEBR_PLUGIN_DIR . 'includes/admin/dashboard.php';
                require_once CHWEBR_PLUGIN_DIR . 'includes/admin/upgrades/upgrade-functions.php';
            }
        }

        /**
         * Loads the plugin language files
         *
         * @access public
         * @since 1.4
         * @return void
         */
        public function load_languages() {
            // Set filter for plugin's languages directory
            $chwebr_lang_dir = dirname( plugin_basename( CHWEBR_PLUGIN_FILE ) ) . '/languages/';
            $chwebr_lang_dir = apply_filters( 'chwebr_languages_directory', $chwebr_lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'chwebr' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'chwebr', $locale );

            // Setup paths to current locale file
            $mofile_local = $chwebr_lang_dir . $mofile;
            $mofile_global = WP_LANG_DIR . '/chwebr/' . $mofile;
            //echo $mofile_local;
            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/CHWEBR folder
                load_languages( 'chwebr', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/chwebsocialshare/languages/ folder
                load_languages( 'chwebr', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'chwebr', false, $chwebr_lang_dir );
            }
        }

    }

    endif; // End if class_exists check

/**
 * The main function responsible for returning the one true Chwebsocialshare
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: $CHWEBR = CHWEBR();
 *
 * @since 2.0.0
 * @return object The one true Chwebsocialshare Instance
 */
function CHWEBR() {
    return Chwebsocialshare::instance();
}
// Get CHWEBR Running
CHWEBR();
