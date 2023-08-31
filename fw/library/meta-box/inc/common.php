<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RWMB_Common' ) )
{
	/**
	 * Common functions for the plugin
	 * Independent from meta box/field classes
	 */
	class RWMB_Common
	{
		/**
		 * Do actions when class is loaded
		 *
		 * @return void
		 */
		public static function on_load()
		{
			$plugin = 'meta-box/meta-box.php';
			add_filter( "plugin_action_links_$plugin", array( __CLASS__, 'plugin_links' ) );
		}

		/**
		 * Add links to Documentation and Extensions in plugin's list of action links
		 *
		 * @since 4.3.11
		 *
		 * @param array $links Array of action links
		 *
		 * @return array
		 */
		public static function plugin_links( $links )
		{
			$links[] = '<a href="#">' . __( 'Documentation', 'azzu'.LANG_DN ) . '</a>';
			$links[] = '<a href="#">' . __( 'Extensions', 'azzu'.LANG_DN ) . '</a>';
			return $links;
		}

	}

	RWMB_Common::on_load();
}
