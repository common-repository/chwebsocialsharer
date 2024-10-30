<?php
/**
 * Admin Dashboard
 *
 * @package     CHWEBR
 * @subpackage  Admin/Dashboard
 * @copyright   Copyright (c) 2016, Rajesh Chaudhary
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add new columns in posts dashboard
 * 
 * @return string
 */
function chwebr_create_share_columns($col) {
	$col['chwebr_shares'] = 'Share Count';
	return $col;
}
add_filter('manage_posts_columns', 'chwebr_create_share_columns');

/**
 * Get share count in post columns
 * 
 * @param array $col
 * @param int $post_id
 * @retrun int
 */
function chwebr_get_shares($col, $post_id) {
	if ($col == 'chwebr_shares') {
		$shares = get_post_meta($post_id,'chwebr_shares',true);
		echo (int)$shares;
	}
}
add_action('manage_posts_custom_column', 'chwebr_get_shares', 10, 2);
/**
 * Make share count columns sortable
 * 
 * @param array $col
 * @return string
 */
// Make the column Sortable
function chwebr_share_column_sortable( $col ) {
	$col['chwebr_shares'] = 'Share Count';
	return $col;
}
add_filter('manage_edit-post_sortable_columns', 'chwebr_share_column_sortable');


/**
 * Change columns get_posts() query
 * 
 * @param type $query
 * @return void
 */
function chwebr_sort_shares_by( $query ) {
    if( ! is_admin() ){
        return false;
    }
 
    $orderby = $query->get( 'orderby');
 
    if( 'Share Count' == $orderby ) {
        $query->set('meta_key','chwebr_shares');
        $query->set('orderby','meta_value_num');
    }
}
add_action( 'pre_get_posts', 'chwebr_sort_shares_by' );


