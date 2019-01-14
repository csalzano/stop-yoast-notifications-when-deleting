<?php
/**
 * Plugin Name: Stop Yoast notifications when deleting posts & terms
 * Plugin URI: https://github.com/mistercorey/stop-yoast-notifications-when-deleting
 * Description: Suppress admin notifications created by the Yoast SEO plugin when deleting posts and terms.
 * Version: 1.0.0
 * Author: Corey Salzano
 * Author URI: https://coreysalzano.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) OR exit;


class Stop_Yoast_Notifications_When_Deleting{

	/**
	 * Remove a hook from WordPress by providing the name of the hook and the
	 * method.
	 *
	 * @param string $name The name of the hook from which to remove
	 * @param string $method The name of the function to remove from the hook
	 * @param string $class The name of the class where the function is defined, if applicable
	 *
	 * @return void
	 */
	function remove_filter( $name, $method, $class = null ) {

		global $wp_filter;
		if( empty( $name ) || ! isset( $wp_filter[$name] ) || ! isset( $wp_filter[$name]->callbacks ) ) {
			return;
		}

		foreach( $wp_filter[$name]->callbacks as $level => $filters ) {
			if( ! is_array( $filters ) ) {
				continue;
			}

			foreach( $filters as $key => $details ) {
				if( ! isset( $details['function'] ) ) {
					continue;
				}

				if( ( is_array( $details['function'] ) && 'object' == gettype( $details['function'][0] ) && $class == get_class( $details['function'][0] ) && $method == $details['function'][1] )
					|| $method == $details['function'] )
				{
					unset( $wp_filter[$name]->callbacks[$level][$key] );
					return;
				}
			}
		}
	}

	function stop_yoast_notifications() {
		$this->remove_filter( 'admin_enqueue_scripts', 'enqueue_assets', 'WPSEO_Slug_Change_Watcher' );
		$this->remove_filter( 'wp_trash_post', 'detect_post_trash', 'WPSEO_Slug_Change_Watcher' );
		$this->remove_filter( 'before_delete_post', 'detect_post_delete', 'WPSEO_Slug_Change_Watcher' );
		$this->remove_filter( 'delete_term_taxonomy', 'detect_term_delete', 'WPSEO_Slug_Change_Watcher' );
	}

	function hooks() {
		add_action( 'plugins_loaded', array( $this, 'stop_yoast_notifications' ), 28 );
	}
}
$salzano_stop_yoast_23984234 = new Stop_Yoast_Notifications_When_Deleting();
$salzano_stop_yoast_23984234->hooks();
