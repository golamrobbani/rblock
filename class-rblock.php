<?php
/**
 * Plugin Name: RBlock
 * Description: RBlock blocks for bloging website. Create Better Content With Gutenberg.
 * Version:     1.0.0
 * Author:      ThemeIM
 * Author URI:  https://themeim.com/
 * License:     GPLv2+
 * Text Domain: rblock
 * Domain Path: /languages/
*/


/**
 * Copyright (c) 2021 themeim (email : support@themeim.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


// don't call the file directly
defined( 'ABSPATH' ) || exit();

/**
 * Main RBlock Class.
 * @class RBlock
 */
final class RBlock {

    /**
	 * RBlock version.
	 * @var string
	 */
	protected $version = '1.0.0';

    /**
     * Minimum PHP version required
     * @var string
     */
    private $min_php = '5.6.0';

	/**
	 * The single instance of the class.
	 * @var RBlock
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main RBlock Instance.
	 * Ensures only one instance of RBlock is loaded or can be loaded.
	 * @return RBlock - Main instance.
	 * @since 1.0.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'rblock' ), '1.0.0' );
	}

	/**
	 * Universalizing instances of this class is forbidden.
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Universalizing instances of this class is forbidden.', 'rblock' ), '1.0.0' );
	}

    /**
	 * RBlock constructor.
     * @since 1.0.0
	 */
    public function __construct() {
        $this->define_constants();
		$this->includes();
		$this->init_hooks();
    }

    /**
	 * Define RBlock Constants.
	 * @return void
	 * @since 1.0.0
	 */
	private function define_constants() {
        define( 'RB_VERSION', $this->version );
		define( 'RB_FILE', __FILE__ );
		define( 'RB_PATH', dirname( RB_FILE ) );
		define( 'RB_INCLUDES', RB_PATH . '/includes' );
		define( 'RB_URL', plugins_url( '', RB_FILE ) );
		define( 'RB_ASSETS_URL', RB_URL . '/assets' );
        define( 'RB_BUILD_URL', RB_URL . '/build' );
        define( 'RB_SRC_BLOCKS', RB_PATH . '/src/blocks' );
		define( 'RB_BASENAME', plugin_basename( __FILE__ ) );
    }

    /**
	 * Include all required files
	 * @since 1.0.0
	 * @return void
	*/
	public function includes() {
        require_once( RB_INCLUDES . '/functions.php' );
		require_once RB_INCLUDES . '/class-rblock-font-loader.php';
		require_once RB_INCLUDES . '/class-rblock-post-meta.php';
        require_once( RB_SRC_BLOCKS . '/alert/index.php' );
    }

    /**
	 * Hook into actions and filters.
	 * @since 1.0.0
	*/
	private function init_hooks() {
        //register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );
        //register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin' ) );
        add_action( 'admin_init', array( $this, 'check_environment' ) );
        add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), - 1 );
		add_action( 'init', array( $this, 'localization_setup' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		//add_filter( 'plugin_action_links_'.RB_BASENAME, array( &$this, 'plugin_action_links' ) );

        add_action('init',array($this,'block_assets'));
        add_action( 'enqueue_block_editor_assets', array($this,'editor_assets' ));
        add_action( 'wp_enqueue_scripts', array($this,'enqueue_styles_scripts' ));
		add_filter( 'block_categories_all', array($this,'add_custom_block_category') );


    }

	/**
	 * Adds the rblock category.
	 * @param array $categories Existing block categories.
	 * @return array Updated block categories.
	 * @since 1.0.0
	 */
	public function add_custom_block_category( $categories ) {
		$category_title = __( 'RBlock', 'rblock' );

		if ( rblock_is_pro() ) {
			$category_title = __( 'RBlock Pro', 'rblock' );
		}

		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'rblock',
					'title' => $category_title,
				),
			)
		);
	}

    /**
	 * Register style and script fire on action init.
	 * @since 1.0.0
	*/
    public function block_assets() {
        // Load the compiled styles.
	    wp_register_style( 'rblock-blocks-style-css', plugin_dir_url(__FILE__) . 'build/style-index.css', array(), RB_VERSION );
        wp_register_script( 'rblock-hide-alert-js', plugin_dir_url(__FILE__) . 'assets/js/hide-alert.js', array( 'jquery' ) );

    }

    /**
	 * Register style and script fire on action block editor asets.
	 * @since 1.0.0
	*/
    public function editor_assets() {
        // Load the compiled blocks into the editor.
        wp_enqueue_script('rblock-blocks-js', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-block-editor', 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n' ),RB_VERSION,true);
        // Load the compiled styles into the editor.
        wp_enqueue_style('rblock-blocks-editor-css', plugin_dir_url(__FILE__) . 'build/index.css', array( 'wp-block-editor' ), RB_VERSION );

        $user_data = wp_get_current_user();
        unset( $user_data->user_pass, $user_data->user_email );
        // Pass in REST URL.
        wp_localize_script(
            'rblock-blocks-js',
            'rblock_globals',
            array(
                'rest_url'      => esc_url( rest_url() ),
                'user_data'     => $user_data,
                'pro_activated' => rblock_is_pro(),
                'is_wpe'        => function_exists( 'is_wpe' ),
            )
        );
		wp_localize_script(
			'rblock-blocks-js',
			'rblockBlockData',
			array(
				'rblockNonce'               => wp_create_nonce( 'rblockNonce' ),
				'typographyControlsEnabled'   => true,
			)
		);

    }

    /**
	 * Enqueue Script and Style if the post has a block only.
	 * @since 1.0.0
	*/
    public function enqueue_styles_scripts() {
        if ( has_block( 'rblock/alert-box' ) ) {
            wp_enqueue_script( 'rblock-hide-alert-js' );
        }
    }

    // /**
    //  * What type of request is this?
    //  * @param  string $type admin, ajax, cron or frontend.
    //  * @since 1.0.0
    //  * @return bool
    //  */
    // private function is_request($type) {
    //     switch ($type) {
    //         case 'admin':
    //             return is_admin();
    //         case 'ajax':
    //             return defined('DOING_AJAX');
    //         case 'cron':
    //             return defined('DOING_CRON');
    //         case 'frontend':
    //             return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON') && !defined('REST_REQUEST');
    //     }
    // }

    /**
	 * Ensure theme and server variable compatibility
     * @since 1.0.0
	 */
    public function check_environment()
    {
        if (version_compare(PHP_VERSION, $this->min_php, '<=') &&  version_compare( WP_VERSION, '3.6', '<=' ) ) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die("Unsupported PHP version Min required PHP Version:{$this->min_php}");
        }
    }

    // /**
	//  * Activate plugin.
	//  * @return void
	//  * @since 1.0.0
	//  */
	// public function activate_plugin() {
	// }

	// /**
	//  * Deactivate plugin.
	//  * @return void
	//  * @since 1.0.0
	//  */
	// public function deactivate_plugin() {
	// 	if ( ! current_user_can( 'activate_plugins' ) ) {
	// 		return;
	// 	}
	// }

    /**
	 * loaded all plugins, trigger the rblock_loaded hook.
	 * @since 1.0.0
	 */
	public function on_plugins_loaded() {
		do_action( 'rblock_loaded' );
	}

	/**
	 * Initialize plugin for localization
	 * @return void
	 * @since 1.0.0
	 */
	public function localization_setup() {
		load_plugin_textdomain( 'rblock', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	// /**
	//  * Plugin action links
	//  * @param array $links
    //  * @since 1.0.0
	//  */
	// public function plugin_action_links( $links ) {
	// 	$admin_url = admin_url();
	// 	$link = array( '<a href="'.$admin_url.'options-general.php?page=rblock_settings">Settings</a>' );
	// 	return array_merge( $links, $link );
	// }

	/**
	 * Add plugin docs links in plugin row links
	 * @param mixed $links Links
	 * @param mixed $file File
	 * @return array $links
	 * @since 1.0.0
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( plugin_basename( __FILE__ ) === $file ) {
			$row_meta = array(
				'docs' => '<a href="' . esc_url( apply_filters( 'rblock_docs_url', 'https://themeim.com/demo/rblock/docs' ) ) . '" aria-label="' . esc_attr__( 'View documentation', 'rblock' ) . '">' . esc_html__( 'Docs', 'rblock' ) . '</a>',
			);
			return array_merge( $links, $row_meta );
		}
		return $links;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_version() {
		return $this->version;
	}


}

/**
 * @return RBlock
 */
function rblock_init() {
	return RBlock::instance();
}
//fire off the plugin
rblock_init();
