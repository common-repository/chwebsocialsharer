<?php
    /**
     * Output debug notices in footer
     * @global type $chwebr_options
     */
    function chwebrOutputDebug() {
        global $chwebr_options, $chwebr_error;
        
            if (empty($chwebr_error)){
                return '';
            }

        if (current_user_can('install_plugins') && isset($chwebr_options['debug_mode'])) {
            echo '<div class="chweb-debug" style="display:block;z-index:250000;font-size:11px;text-align:center;">';
            foreach ($chwebr_error as $key => $value){
                echo $key . ' ' . date( 'H:m:s.u', time()). ' ' . $value . '<br />';
            }
            echo '</div>';
        }
    }
    add_action('wp_footer', 'chwebrOutputDebug', 100);
    
    
    
