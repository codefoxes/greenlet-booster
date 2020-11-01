<?php
/**
 * Greenlet Booster SEO Enhancer.
 *
 * @package greenlet-booster\library
 */

namespace Greenlet\Booster;

/**
 * SEO Enhancer.
 *
 * @since  1.0.0
 */
class SEO {
	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Initialize the SEO Enhancer.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'greenlet_l10n_object', array( $this, 'add_l10_object' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
	}

	/**
	 * Add Localization object.
	 *
	 * @param array $l10n Localization object array.
	 * @return array      Updated object.
	 */
	public function add_l10_object( $l10n ) {
		$l10n['meta_description'] = $this->meta_description();
		return $l10n;
	}

	/**
	 * Add inline scripts.
	 */
	public function enqueue_scripts() {
		$data = 'var desc = document.querySelector(\'meta[name="description"]\'); if (desc === null) { var meta = document.createElement(\'meta\'); meta.name = "description"; meta.content = greenletData.meta_description; document.getElementsByTagName(\'head\')[0].appendChild(meta); }';
		if ( function_exists( 'greenlet_enqueue_inline_script' ) ) {
			greenlet_enqueue_inline_script( 'greenlet-scripts', $data );
		}
	}

	/**
	 * Get meta description.
	 *
	 * @since  1.0.0
	 * @return string Meta description.
	 */
	public function meta_description() {
		if ( is_front_page() ) {
			$description = get_bloginfo( 'name' ) . ' - ' . get_bloginfo( 'description' );
		} else {
			$description = get_bloginfo( 'name' ) . wp_title( '&raquo;', false );
		}

		return $description;
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

SEO::get_instance();
