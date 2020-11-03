<?php
/**
 * Greenlet Settings Importer and Exporter.
 *
 * @package greenlet-booster\library
 */

namespace Greenlet\Booster;

/**
 * Set up Import Export.
 *
 * @since  1.0.0
 */
class Importer {
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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'wp_ajax_booster_import_options', array( $this, 'import_options' ) );
		add_action( 'wp_ajax_booster_save_backend', array( $this, 'save_backend' ) );
		add_action( 'greenlet_after_backend_customizer_links', array( $this, 'add_importer' ) );
	}

	/**
	 * Loads the required javascript.
	 *
	 * @since 1.0.0
	 * @param string $hook The current admin page.
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( 'appearance_page_greenlet' !== $hook ) {
			return;
		}

		wp_enqueue_script( 'booster-options', GREENLET_BOOSTER_ASSETS_URL . '/js/options.js', array( 'greenlet-options' ), GREENLET_BOOSTER_VERSION, true );
	}

	/**
	 * Add Import Export section.
	 */
	public function add_importer() {
		$options = get_theme_mods();
		if ( $options && is_array( $options ) ) {
			// Generate the export data.
			$val = wp_json_encode( $options );

			$editor_styles = ( isset( $options['editor_styles'] ) && false === $options['editor_styles'] ) ? false : true;
		} else {
			$val = __( 'You don\'t have any options to export. Try saving your options first.', 'glbooster' );

			$editor_styles = true;
		}

		?>
		<div class="impex">
			<div class="heading"><?php esc_html_e( 'Import / Export Theme Settings', 'glbooster' ); ?></div>
			<div class="content-wrap impex-section">
				<div class="row">
					<div class="col-6 col-impex export">
						<div class="sub-heading"><?php esc_html_e( 'Export Settings', 'glbooster' ); ?></div>
						<div class="export-option">
							<textarea rows="8" readonly><?php echo esc_html( $val ); ?></textarea>
							<div class="explain"><?php esc_html_e( 'Copy the contents to export.', 'glbooster' ); ?></div>
						</div>
					</div>
					<div class="col-6 col-impex import">
						<div class="sub-heading"><?php esc_html_e( 'Import Settings', 'glbooster' ); ?></div>
						<div class="import-option">
							<textarea id="import-content" rows="8"></textarea>
							<div class="explain"><?php esc_html_e( 'Paste the contents to import.', 'glbooster' ); ?></div>
							<div class="btn-wrap">
								<a href="#" id="import-btn" class="button-primary action-btn"><?php esc_html_e( 'Import Settings', 'glbooster' ); ?></a>
								<span class="spinner"></span>
								<?php wp_nonce_field( 'greenlet_options', 'options_nonce' ); ?>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="message success import-success"><?php esc_html_e( 'Import Settings successful. Reload page to get imported settings.', 'glbooster' ); ?></div>
					<div class="message warning import-warning"><?php esc_html_e( 'Import successful. Your settings and Import settings are same!!', 'glbooster' ); ?></div>
					<div class="message error import-error"><?php esc_html_e( 'Sorry. Import Failed. Please check the code.', 'glbooster' ); ?></div>
					<div class="message default import-default"><?php esc_html_e( 'There is nothing to import!', 'glbooster' ); ?></div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Import options.
	 *
	 * @since 1.0.0
	 */
	public function import_options() {

		if ( ! isset( $_POST['nonce'] ) || ! isset( $_POST['value'] ) ) {
			die( '0' );
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'greenlet_options' ) ) {
			die( '0' );
		}

		$new_mods = json_decode( sanitize_textarea_field( wp_unslash( $_POST['value'] ) ), true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			die( '0' );
		}

		if ( ! isset( $new_mods['custom_css_post_id'] ) ) {
			die( '0' );
		}

		$mods = get_theme_mods();

		if ( $mods === $new_mods ) {
			die( '2' );
		}

		$theme = get_option( 'stylesheet' );
		if ( update_option( "theme_mods_$theme", $new_mods ) ) {
			die( '1' );
		}

		die( '0' );
	}

	/**
	 * Save Backend Settings.
	 *
	 * @since 1.1.0
	 */
	public function save_backend() {
		if ( ! isset( $_POST['nonce'] ) || ! isset( $_POST['settings'] ) ) {
			die( '0' );
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'greenlet_backend' ) ) {
			die( '0' );
		}

		$settings = json_decode( sanitize_textarea_field( wp_unslash( $_POST['settings'] ) ), true );
		foreach ( $settings as $setting => $setting_val ) {
			set_theme_mod( $setting, $setting_val );
		}

		die( wp_json_encode( get_theme_mods() ) );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Importer::get_instance();
