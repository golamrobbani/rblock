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

if ( ! function_exists( 'rblock_get_template' ) ) :
	function rblock_get_template( $slug, array $params = array(), $output = true, $ddir = true ) {
	    if( !$output ) ob_start();
	    if ( $ddir ) {
	    	$template_file = RB_PATH . "/templates/{$slug}.php";
	    } else {
	    	$template_file = RB_PATH . "/{$slug}.php";
	    }
	    if ( !file_exists( $template_file ) ) {
	      	trigger_error( sprintf( esc_html__( 'Error locating %s for inclusion', 'rblock' ), $template_file ), E_USER_ERROR );
	    }
	    extract( $params, EXTR_SKIP );
	    require( $template_file );

	    if( !$output ) return ob_get_clean();

	}
endif;
