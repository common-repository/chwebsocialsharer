<?php

/**
 * Echo string when debug mode is enabled
 * 
 * @param type $string
 */
function chwebecho($string){
    if(CHWEBR_DEBUG){
        echo $string;
    }
}

/**
 * Check if curl is installed
 * 
 * @return boolean true when it is installed
 */
function chwebr_curl_installed(){
    if(function_exists('curl_init')){
        return true;
    }
    
    return false;
}

/*function chwebr_is_amp_endpoint(){
    if (  function_exists( 'is_amp_endpoint' )){
        return is_amp_endpoint();
    }
}*/


/**
 * Remove http(s) on WP site info
 * 
 * @param type $string
 * @return type
 */
function chwebr_replace_http($string){
    if (empty($string)){
        return $string;
    }
    
    $a = str_replace('https://', '', $string);
    return str_replace('http://', '', $string);
}

function chwebr_share_buttons(){
    $content = '<li><a class="chwebicon-facebook" target="_blank" href="https://www.facebook.com/sharer.php?u=https%3A%2F%2Fwww.chaudharyweb.com%2F&display=popup&ref=plugin&src=like&app_id=449277011881884"><span class="icon"></span><span class="text">Share it</span></a></li>'.
               '<li><a class="chwebicon-twitter" target="_blank" href="https://twitter.com/intent/tweet?hashtags=chwebsocialshare%2C&original_referer=http%3A%2F%2Fsrc.wordpress-develop.dev%2Fwp-admin%2Fadmin.php%3Fpage%3Dchwebr-settings%26tab%3Dgeneral&ref_src=twsrc%5Etfw&related=chwebsocialshare&text=I%20use%20ChwebSocialShare%20- incredible%20great%20socialm%20media%20tool%20on%20my%20site%20'. chwebr_replace_http(get_bloginfo('wpurl')).'&tw_p=tweetbutton&url=https%3A%2F%2Fwww.chaudharyweb.com%2F"><span class="icon"></span><span class="text">Tweet #chwebsocialshare</span></a></li>' .
               '<li><a class="chwebicon-twitter" target="_blank" href="https://twitter.com/intent/follow?original_referer=http%3A%2F%2Fsrc.wordpress-develop.dev%2Fwp-admin%2Fadmin.php%3Fpage%3Dchwebr-settings%26tab%3Dgeneral&ref_src=twsrc%5Etfw&region=follow_link&screen_name=chwebsocialshare&tw_p=followbutton"><span class="icon"></span><span class="text">Follow @chwebsocialshare</span></a></li>'.
               '<li><a class="chwebicon-twitter" target="_blank" href="https://twitter.com/intent/follow?original_referer=http%3A%2F%2Fsrc.wordpress-develop.dev%2Fwp-admin%2Fadmin.php%3Fpage%3Dchwebr-settings%26tab%3Dgeneral&ref_src=twsrc%5Etfw&region=follow_link&screen_name=renehermenau&tw_p=followbutton"><span class="icon"></span><span class="text">Follow @rajeshdnw</span></a></li>';
    return $content;
}

