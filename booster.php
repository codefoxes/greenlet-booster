<?php
/**
 * Plugin Name: Greenlet Booster
 * Description: An amazing companion plugin for Greenlet theme to add multiple extra functionalities.
 * Plugin URI: https://greenletwp.com/booster
 * Author: Greenlet Team
 * Version: 1.0.1
 * Author URI: https://greenletwp.com/about/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: glbooster
 * Domain Path: /library/languages
 *
 * @package Greenlet Booster
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GREENLET_BOOSTER_VERSION', '1.0.1' );
define( 'GREENLET_BOOSTER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GREENLET_BOOSTER_LIBRARY_DIR', GREENLET_BOOSTER_PLUGIN_DIR . '/library' );

define( 'GREENLET_BOOSTER_ASSETS_URL', plugins_url( '/assets', __FILE__ ) );

if ( ! function_exists( 'greenlet_load_booster' ) ) {
	/**
	 * Loads all the booster files.
	 *
	 * @since  1.0.0
	 */
	function greenlet_load_booster() {
		if ( 'greenlet' !== wp_get_theme()->get_template() ) {
			return;
		}

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'greenlet_booster_action_links' );

		require_once GREENLET_BOOSTER_LIBRARY_DIR . '/class-performance.php';
		require_once GREENLET_BOOSTER_LIBRARY_DIR . '/class-seo.php';
		require_once GREENLET_BOOSTER_LIBRARY_DIR . '/class-importer.php';
	}
	greenlet_load_booster();
}

if ( ! function_exists( 'greenlet_booster_action_links' ) ) {
	/**
	 * Add plugin action links.
	 *
	 * @since  1.0.0
	 * @param  array $links Links array.
	 * @return array        Links array.
	 */
	function greenlet_booster_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'themes.php?page=greenlet' ) . '" aria-label="' . esc_attr__( 'View Greenlet settings', 'glbooster' ) . '">' . esc_html__( 'Settings', 'glbooster' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}
}
