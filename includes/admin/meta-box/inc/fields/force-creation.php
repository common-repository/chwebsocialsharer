<?php

/**
 * Custom HTML field class.
 */
class CHWEBR_RWMB_Force_Creation_Field extends CHWEBR_RWMB_Field {

    /**
     * Get field HTML
     *
     * @param mixed $meta
     * @param array $field
     *
     * @return string
     */
    static function html( $meta, $field ) {
        global $post;
        $html = !empty( $field['std'] ) ? $field['std'] : '';
        if( !empty( $field['callback'] ) && is_callable( $field['callback'] ) ) {
            $html = call_user_func_array( $field['callback'], array($meta, $field) );
        }
        return self::get_force_refresh_url($post, $field);
    }

    /**
     * Link to the open graph debugger to check if open graph tags are valid
     * 
     * @global array $post
     * @return string
     */
    static function get_force_refresh_url($post, $field) {
        //var_dump( $post );
        if( isset( $post ) && $post->post_status == "publish" ) {
            $url = get_permalink( $post->ID );
            return '<a href="' . $url . '?chwebr-refresh" target="_blank" class="button-small"> Get Shares & Shortlinks </a>' . $field['helper'];
        } else {
            return '';
        }
    }

}
