<?php 
/**
 * Check for Pro version.
 * @since 1.0.0
 */
if( !function_exists('rblock_is_pro') ):
    function rblock_is_pro() {
        return function_exists( 'rblock_pro_main_plugin_file' );
    }
endif;