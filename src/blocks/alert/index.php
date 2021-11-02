<?php
/**
 * Server-side rendering for the alert_box block
 * @since   1.0.0
 */

/**
 * Register the block on the server
 */
if(!function_exists('rblock_register_alert_box')):
    function rblock_register_alert_box() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }
        register_block_type(
            'rblock/alert-box', 
            array(
                'style'           => 'rblock-blocks-style-css',
                'attributes'      => array(
                    'alert_type'  => array(
                        'type'    =>'string',
                        'default' => 'primary'
                    ),
                    'content' =>array(
                        'type'   => 'string',
                        'default'=>__('Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea sequat, vel illum dolore eu feugiat nulla facili','rblock')
                    ),
                    'dismiss' =>array(
                        'type'    => 'Boolean',
                        'default' => true
                    ),
                    'backgroundColor' => array(
                        'type' => 'string',
                        'default'=>'#cce5ff'
                    ),
                    'textColor' => array(
                        'type'  => 'string',
                        'default'=> '#004085'
                    )
                ),
                'render_callback' => 'rblock_render_alert_box',
            )
        );
    }
endif;
add_action( 'init', 'rblock_register_alert_box' );


//render fornt end alert box 
if(!function_exists('rblock_render_alert_box')):
    function rblock_render_alert_box( $attributes ) {
        $allowed_html_field = [
            'a'=>[
                'class'  => [],
                'id' => [],
                'href'=>[],
                'target'=>[]
            ],
            'span'=>[
                'class'  => [],
                'id' => [],
                'span'=>[]
            ],
            'br'=>[],
            'strong'=>[],
            'em'=>[],
        ];
        $text_color = !empty($attributes['textColor'])?$attributes['textColor']:'#004085';
        ob_start(); ?>

        <div style="background-color:<?php echo esc_attr($attributes['backgroundColor']); ?>;color:<?php echo esc_attr($text_color); ?>;border-color:<?php echo esc_attr($attributes['backgroundColor']); ?>" class="wp-block-rblock-alert-box rblock-alert rblock-alert-<?php echo isset($attributes['alert_type'])? esc_attr($attributes['alert_type']):'primary'; ?>"  role="alert">
            <p><?php echo wp_kses($attributes['content'],$allowed_html_field); ?></p>
            <?php if( isset($attributes['dismiss']) && $attributes['dismiss']=== true ){ ?>
                <button style="background:none!important;color:<?php echo esc_attr($text_color); ?>" type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php } ?>
        </div>

        <?php return ob_get_clean(); 
    }
endif;