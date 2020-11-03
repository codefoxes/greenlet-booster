<?php
/**
 * Greenlet Booster Performance Enhancer.
 *
 * @package greenlet-booster\library
 */

namespace Greenlet\Booster;

/**
 * Set up Import Export.
 *
 * @since  1.0.0
 */
class Performance {
	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Initialize the importer.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'disable_emojis' ) );
		add_filter( 'greenlet_options', array( $this, 'add_options' ) );
		add_action( 'wp_footer', array( $this, 'deregister_scripts' ) );
	}

	/**
	 * Add performance options.
	 *
	 * @since 1.0.0
	 * @param array $options Options array.
	 *
	 * @return array Array with added options.
	 */
	public function add_options( $options ) {
		$options[] = array(
			'type'  => 'setting_control',
			'id'    => 'disable_emojis',
			'sargs' => array(
				'default' => false,
			),
			'cargs' => array(
				'type'        => 'checkbox',
				'section'     => 'performance',
				'label'       => __( 'Disable WP Emojis', 'glbooster' ),
				'description' => __( 'Posts with emojis may break, disable with caution.', 'glbooster' ),
			),
		);

		$options[] = array(
			'type'  => 'setting_control',
			'id'    => 'disable_embed',
			'sargs' => array(
				'default' => false,
			),
			'cargs' => array(
				'type'        => 'checkbox',
				'section'     => 'performance',
				'label'       => __( 'Disable WP Embed Scripts', 'glbooster' ),
				'description' => __( 'Embedding third party WordPress site\'s posts may break, disable with caution.', 'glbooster' ),
			),
		);

		return $options;
	}

	/**
	 * Disable Emojis if opted.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function disable_emojis() {
		$disable = gl_get_option( 'disable_emojis', false );
		if ( false === $disable ) {
			return;
		}

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'tiny_mce_plugins', array( $this, 'disable_emojis_tinymce' ) );
		add_filter( 'wp_resource_hints', array( $this, 'disable_emojis_remove_dns_prefetch' ), 10, 2 );
	}

	/**
	 * Filter function used to remove the tinymce emoji plugin.
	 *
	 * @since  1.0.0
	 * @param  array $plugins Plugins list.
	 * @return array          Difference between the two arrays
	 */
	public function disable_emojis_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}

	/**
	 * Remove emoji CDN hostname from DNS pre-fetching hints.
	 *
	 * @since  1.0.0
	 * @param  array  $urls          URLs to print for resource hints.
	 * @param  string $relation_type The relation type the URLs are printed for.
	 * @return array                 Difference between the two arrays.
	 */
	public function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
		if ( 'dns-prefetch' === $relation_type ) {
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

			$urls = array_diff( $urls, array( $emoji_svg_url ) );
		}
		return $urls;
	}

	/**
	 * Deregister WP Embed script.
	 */
	public function deregister_scripts() {
		$disable = gl_get_option( 'disable_embed', false );
		if ( false === $disable ) {
			return;
		}

		wp_deregister_script( 'wp-embed' );
	}

	/**
	 * Returns the instance.
	 *
	 * @return object
	 * @since  1.0.0
	 * @access public
	 */
	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Performance::get_instance();
