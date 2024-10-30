<?php

/**
 * Admin Pages
 *
 * @package     CHWEBR
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Creates the admin submenu pages under the Chwebsocialshare menu and assigns their
 * links to global variables
 *
 * @since 1.0
 * @global $chwebr_settings_page
 * @global $chwebr_add_ons_page
 * @global $chwebr_tools_page
 * @return void
 */
function chwebr_add_options_link() {
    global $chwebr_parent_page, $chwebr_add_ons_page, $chwebr_settings_page, $chwebr_tools_page, $chwebr_quickstart;

   
    $chwebsocialshare_logo = 'iVBORw0KGgoAAAANSUhEUgAAABYAAAAUCAYAAACJfM0wAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsIAAA7CARUoSoAAAAPYSURBVDhPhVV7aFtVHP7uTW7SNF3SpFuYdko2y2ZHLQrS2omsUlRUlG6D4R9uWlBE3BgymOseiHUiiCA+QHCIrihzugdTVsaG/UPXjbGuw7Wr07TdyrpuXdaaLu/7yPU7N7c2MUo+SHLu+d3znd/zi4Tvb5sohMlH1YRfkbAy4IBeYHVIQFwzEYkZUK0dbrj4+Q8UE4tV2sCuB71YF3bh/C0NORJByh82eWmVx4EHgk6MxQ1sPZvA6KwOLHBa9kIUE2dyaL9XwSdNPjQfmkZjrYKgU4Zu3UjnZAnjcR2RCR1P1rvR3eLD24MpdPUnSO74xwGBYuKsgYOP+zE0rSFc5cRLyypsQynevZjEV8Mp9D4TwIGJLLafI7mHntvccv6HsOmXemQMJXJQ7OiuZQx88FsC7w8lsfN8AoNMgcDuRi/2tvrRfCKGt1Z60RpyAVrOsgnMEwvwNlEgRg9GbSFCot1nk/hhIIneyym0HpxG0/EZpAwTbYtd2NHgxZsDCWxurAS4N4diYkKY5s1sEEPC6noPBjaG8NoqP6Y6Qtb57rG0Zd9Y50LPdRVra13wCjb7cAnxvyHxRZXR/zqlouNIFO19d/Aeu2Ykng874HTAYXsqK6TL2WvruyxM3Meqd9RXYvv9FTjFNqytZBcQgl6z82YK0pLi/Q8Mkl6e0RFlQbc8vAD7I1kcHs/i5aVuy/7dlQwaggpGEwbSouftS8oSN1Y78OxiBZ2DSbzeH0fLQieGn69BwC0jxvbcdC6OQ49WoetiCobIm42yxEs4aV+u8qGnrRqnnw7ixTqPtT/JCW05OoM9D1VhjN52j2Q4Qfn0CJQlnkzn8CmH4fPhJDJ2kX5P6FjxTRQfr/ZjQ9iN5T/NsEeZgrkeJcoSjyZz6LyUwuEbGjoZrkA9p/KjtgC+4PMftJuiQwRxAcoSh9wSVixy4iRTcSSSQkwUiHhlmRtOTmf/TQ37n6qm7FGMClBCbG2IrrHr4GD/JG3tPNNeg5oDUWst8BlFqIvRvBCuwBNL2CWU2znME4tIGNEUtcHDsHS7IS/Rk3vYsz3Xszh1U8U7HI61vTHLtrBCRjOj+XNWQ1NI4fm8jggUe8wLj91Q8QY92ENtuBDVsKlvFh82VOLrqyrWUyd2cX0Xh6X951n0Tao4c1vDcr+CK38xFVKhn4WyKapOhYqsqcHEHR17R7J4tc5NcZfxyPEYzRLuZs6vrQvix7Es9l3NYN9jPvxyS8dzIgq30OQ8Velfk5GD6NQ2huaj1LHoOEGdSIu3hOxlTSziC9solWG/jGPjKr7l9FljLaTRAvA3sH+DhCTpHQkAAAAASUVORK5CYII=';
    // Getting Started Page
    $chwebr_parent_page = add_menu_page( 'Chwebsocialshare Settings', __( 'Chwebsocialshare', 'chwebr' ), 'manage_options', 'chwebr-settings', 'chwebr_options_page', 'data:image/png;base64,' . $chwebsocialshare_logo);
    $chwebr_settings_page = add_submenu_page( 'chwebr-settings', __( 'Chwebsocialshare Settings', 'chwebr' ), __( 'Settings', 'chwebr' ), 'manage_options', 'chwebr-settings', 'chwebr_options_page' );
     $chwebr_tools_page = add_submenu_page( 'chwebr-settings', __( 'Chwebsocialshare Tools', 'chwebr' ), __( 'Im/Export & System', 'chwebr' ), 'manage_options', 'chwebr-tools', 'chwebr_tools_page' );

}
add_action( 'admin_menu', 'chwebr_add_options_link', 10 );

/**
 *  Determines whether the current admin page is an CHWEBR admin page.
 *  
 *  Only works after the `wp_loaded` hook, & most effective 
 *  starting on `admin_menu` hook.
 *  
 *  @since 1.9.6
 *  @return bool True if CHWEBR admin page.
 */
function chwebr_is_admin_page() {
    $currentpage = isset( $_GET['page'] ) ? $_GET['page'] : '';
    if( !is_admin() || !did_action( 'wp_loaded' ) ) {
        return false;
    }

    global $chwebr_parent_page, $pagenow, $typenow, $chwebr_settings_page, $chwebr_add_ons_page, $chwebr_tools_page, $chwebr_quickstart;

    if( 'chwebr-settings' == $currentpage || 'chwebr-addons' == $currentpage || 'chwebr-tools' == $currentpage || 'chwebr-getting-started' == $currentpage || 'chwebr-credits' == $currentpage || 'chwebr-about' == $currentpage  ) {
        return true;
    }
}
