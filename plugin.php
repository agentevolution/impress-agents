<?php
/*
	Plugin Name: IMPress Agents
	Plugin URI: http://wordpress.org/plugins/impress-agents/
	Description: Employee Directory for WordPress tailored for Real Estate Offices.
	Author: Agent Evolution
	Author URI: http://agentevolution.com

	Version: 1.1.2

	License: GNU General Public License v2.0 (or later)
	License URI: http://www.opensource.org/licenses/gpl-license.php
*/

if ( ! defined( 'ABSPATH' ) ) exit;

register_activation_hook( __FILE__, 'impress_agents_activation' );
/**
 * This function runs on plugin activation. It flushes the rewrite rules to prevent 404's
 *
 * @since 0.9.0
 */
function impress_agents_activation() {

		/** Flush rewrite rules */
		if ( ! post_type_exists( 'employee' ) ) {
			impress_agents_init();
			global $_impress_agents, $_impress_agents_taxonomies;
			$_impress_agents->create_post_type();
			$_impress_agents_taxonomies->register_taxonomies();
		}

		flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'impress_agents_deactivation' );

/**
 * This function runs on plugin deactivation. It flushes the rewrite rules to get rid of remnants
 *
 * @since 0.9.0
 */
function impress_agents_deactivation() {
		flush_rewrite_rules();
}

add_action( 'after_setup_theme', 'impress_agents_init' );
/**
 * Initialize IMPress Agents.
 *
 * Include the libraries, define global variables, instantiate the classes.
 *
 * @since 0.9.0
 */
function impress_agents_init() {

	global $_impress_agents, $_impress_agents_taxonomies;

	define( 'IMPRESS_AGENTS_URL', plugin_dir_url( __FILE__ ) );
	define( 'IMPRESS_AGENTS_VERSION', '1.1.1' );

	/** Load textdomain for translation */
	load_plugin_textdomain( 'impress_agents', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	/** Includes */
	require_once( dirname( __FILE__ ) . '/includes/helpers.php' );
	require_once( dirname( __FILE__ ) . '/includes/functions.php' );
	require_once( dirname( __FILE__ ) . '/includes/shortcodes.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-agents.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-taxonomies.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-employee-widget.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-agent-import.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-migrate-old-posts.php' );

	/** Add theme support for post thumbnails if it does not exist */
	if(!current_theme_supports('post-thumbnails')) {
		add_theme_support( 'post-thumbnails' );
	}

	/** Enqueues impress-agents.css style file if it exists and is not deregistered in settings */
	add_action('wp_enqueue_scripts', 'add_impress_agents_main_styles');
	function add_impress_agents_main_styles() {

		$options = get_option('plugin_impress_agents_settings');

		/** Register Font Awesome icons but don't enqueue them */
		wp_register_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', '', null, 'all');
		wp_enqueue_style('font-awesome');

		if ( !isset($options['impress_agents_stylesheet_load']) ) {
			$options['impress_agents_stylesheet_load'] = 0;
		}

		if ('1' == $options['impress_agents_stylesheet_load'] ) {
			return;
		}

        if ( file_exists(dirname( __FILE__ ) . '/includes/css/impress-agents.css') ) {
        	wp_register_style('impress_agents', IMPRESS_AGENTS_URL . 'includes/css/impress-agents.css', '', null, 'all');
            wp_enqueue_style('impress_agents');
        }
    }

    /** Add admin scripts and styles */
    function impress_agents_admin_scripts_styles() {
        wp_enqueue_style( 'impress_agents_admin_css', IMPRESS_AGENTS_URL . 'includes/css/impress-agents-admin.css' );

		wp_enqueue_script( 'impress-agents-admin', IMPRESS_AGENTS_URL . 'includes/js/admin.js', 'media-views' );

		$localize_script = array(
			'title'        => __( 'Set Term Image', 'impress_agents' ),
			'button'       => __( 'Set term image', 'impress_agents' )
		);

		/* Pass custom variables to the script. */
		wp_localize_script( 'impress-agents-admin', 'impa_term_image', $localize_script );

		wp_enqueue_media();

	}
	add_action( 'admin_enqueue_scripts', 'impress_agents_admin_scripts_styles' );

	/** Instantiate */
	$_impress_agents = new IMPress_Agents;
	$_impress_agents_taxonomies = new IMPress_Agents_Taxonomies;

	add_action( 'widgets_init', 'impress_agents_register_widgets' );

	/** Make sure is_plugin_active() can be called */
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	if(is_plugin_active('genesis-agent-profiles/plugin.php')) {
		add_action( 'wp_loaded', 'impress_agents_migrate' );
	}
}

function impress_agents_migrate() {
	new IMPress_Agents_Migrate();
}

/**
 * Register Widgets that will be used in the IMPress Agents plugin
 *
 * @since 0.9.0
 */
function impress_agents_register_widgets() {

	$widgets = array( 'IMPress_Agents_Widget' );

	foreach ( (array) $widgets as $widget ) {
		register_widget( $widget );
	}

}
