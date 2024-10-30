<?php

// Queue up our profile field functions
add_action( 'show_user_profile', 'chwebr_render_user_profiles' );
add_action( 'edit_user_profile', 'chwebr_render_user_profiles' );
add_action( 'personal_options_update', 'chwebr_save_user_profiles' );
add_action( 'edit_user_profile_update', 'chwebr_save_user_profiles' );

/**
 * Render the user profile settings
 * 
 * @param array $user
 * @return string html
 */
function chwebr_render_user_profiles( $user ) {
    
    $html = '<h3>' . __( 'ChwebSocialShare Social Media Integration', 'chwebr' ) . '</h3>' .
            '<table class="form-table">' .
            '<tr>' .
            '<th><label for="twitter">' . __( 'Twitter Username', 'chwebr' ) . '</label></th>' .
            '<td>' .
            '<input type="text" name="chwebr_twitter_handle" id="chwebr_twitter_handle" value="' . esc_attr( get_the_author_meta( 'chwebr_twitter_handle', $user->ID ) ) . '" class="regular-text" />' .
            '<br /><span class="description">' . __( 'Your Twitter username (without the @ symbol)', 'chwebr' ) . '</span>' .
            '</tr>' .
            '<th><label for="chwebr_fb_author_url">' . __( 'Facebook Author URL', 'chwebr' ) . '</label></th>' .
            '<td>' .
            '<input type="text" name="chwebr_fb_author_url" id="chwebr_fb_author_url" value="' . esc_attr( get_the_author_meta( 'chwebr_fb_author_url', $user->ID ) ) . '" class="regular-text" />' .
            '<br /><span class="description">' . __( 'URL to your Facebok profile.', 'chwebr' ) . '</span>' .
            '</td>' .
            '</tr>' .
            '</table>';
    
    if( chwebr_show_meta_box() ){
        echo $html;
    }
}

/**
 * Save user profile
 * 
 * @param type $user_id
 * @return boolean
 */
function chwebr_save_user_profiles( $user_id ) {

    if( !current_user_can( 'edit_user', $user_id ) )
        return false;

    update_user_meta( $user_id, 'chwebr_twitter_handle', $_POST['chwebr_twitter_handle'] );
    update_user_meta( $user_id, 'chwebr_fb_author_url', $_POST['chwebr_fb_author_url'] );
}
