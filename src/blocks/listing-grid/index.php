<?php
/**
 * Server-side rendering for the alert_box block
 * @since   1.0.0
 */

/**
 * Register the block on the server
 */
class RTCL_Listing_Grid{

    public function __construct() {
		$this->init_hooks();
    }

    private function init_hooks() {
        add_action( 'init', array($this,'rblock_register_listing_grid') );
        add_action('wp_ajax_rtcl_categories', array($this,'rtcl_categories')); 
        add_action('wp_ajax_nopriv_rtcl_categories', array($this,'rtcl_categories')); 
    }

    private function rtcl_build_query( $data ) {

		if ( $data['type'] != 'custom' ) {

			// Get plugin settings
			$settings = get_option( 'rtcl_moderation_settings' );
			$min_view = !empty( $settings['popular_listing_threshold'] ) ? (int) $settings['popular_listing_threshold'] : 500;
			$new_threshold = !empty( $settings['new_listing_threshold'] ) ? (int) $settings['new_listing_threshold'] : 3;

			// Post type
			$args = array(
				'post_type'      => 'rtcl_listing',
				'post_status'    => 'publish',
				'ignore_sticky_posts' => true,
				'posts_per_page' => $data['number'],
			);

			// Ordering
			if ( $data['random'] ) {
				$args['orderby'] = 'rand';
			}
			else {
				$args['orderby'] = $data['orderby'];
				if ( $data['orderby'] == 'title' ) {
					$args['order'] = 'ASC';
				}
			}

			// Taxonomy
			if ( !empty( $data['cat'] ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'rtcl_category',
						'field' => 'term_id',
						'terms' => $data['cat'],
					)
				);
			}

			// Date and Meta Query
			switch ( $data['type'] ) {
				case 'new':
					$args['date_query'] = array(
						array(
							'after' => $new_threshold . ' day ago',
						),
					);
					break;

				case 'featured':
                    $args['meta_key'] = 'featured';
                    $args['meta_value'] = '1';
					break;

				case 'top':
                    $args['meta_key'] = '_top';
                    $args['meta_value'] = '1';
					break;

				case 'popular':
                    $args['meta_key'] = '_views';
                    $args['meta_value'] = $min_view;
                    $args['meta_compare'] = '>=';
					break;
			}
		}

		else {

			$posts = array_map( 'trim' , explode( ',', $data['ids'] ) );

			$args = array(
				'post_type'      => 'rtcl_listing',
				'post_status'    => 'publish',
				'ignore_sticky_posts' => true,
				'nopaging'       => true,
				'post__in'       => $posts,
				'orderby'        => 'post__in',
			);
		}
		return new WP_Query( $args );
	}


    public function rblock_register_listing_grid() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }
        register_block_type(
            'rblock/listing-grid', 
            array(
                'render_callback' => array($this,'rblock_render_listing_grid'),
                'attributes'      => array(

                    'style'=> array(
                        'type'=>'string',
                        'default'=>'1'
                    ),
                    'type'=> array(
                        'type'=>'string',
                        'default'=>'all'
                    ),
                    'cat' => array(
                        'type' => 'string',
                        'default'=>'0'
                    ),
                    'cat_display'   => array(
                        'type'    => 'boolean',
                        'default' => false,
                    ),
                    'field_display'   => array(
                        'type'    => 'boolean',
                        'default' => true,
                    ),
                    'views_display'   => array(
                        'type'    => 'boolean',
                        'default' => true,
                    ),
                    'number'  => array(
                        'type'    => 'number',
                        'default' => 8,
                    ),
                    'random'   => array(
                        'type'    => 'boolean',
                        'default' => true,
                    ),


                    'displaySectionTitle' => array(
                        'type'    => 'boolean',
                        'default' => false,
                    ),
                  
                    'order'               => array(
                        'type'    => 'string',
                        'default' => 'desc',
                    ),
                    'orderBy'             => array(
                        'type'    => 'string',
                        'default' => 'date',
                    ),
                  
                    'sectionTitle'        => array(
                        'type' => 'string',
                    ),
                  
                ),
               
            )
        );
    }


    //render fornt end alert box 
    public function rblock_render_listing_grid( $attributes ) {
        $data=$attributes;
        $data['query'] = $this->rtcl_build_query( $data );
        $query=$data['query'];

        if ( $query->have_posts() ) :?>
            <?php while ( $query->have_posts() ) : $query->the_post();?>

            <?php get_template_part('',$data); ?>
            
            <?php endwhile;?>
        <?php endif;?>
        <?php wp_reset_postdata();
    }

    public function rtcl_categories(){
        $rtcl_nonce = $_POST['rtcl_nonce'];	       
        if ( !wp_verify_nonce($rtcl_nonce, 'rtcl-nonce' ) ) {
            wp_die('nonce not varified'); 	       
        }
    
        $terms  = get_terms( array( 'taxonomy' => 'rtcl_category', 'fields' => 'id=>name' ) );
        $category_dropdown = array( '0' => __( 'All Categories', 'rblock' ) );;
        foreach ( $terms as $id => $name ) {
            $category_dropdown[$id] = $name;
        }
    
        if(!empty($category_dropdown) ) :
            wp_send_json($category_dropdown);
        else: ?>
            <p><div class="ajax-data-notfound"><?php  echo __('Content empty'); ?></div></p>
        <?php endif;
    
        wp_reset_postdata();	 			
        wp_die(); 
    }





}
new RTCL_Listing_Grid();

    