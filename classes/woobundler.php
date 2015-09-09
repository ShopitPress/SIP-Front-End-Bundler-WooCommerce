<?php

/**
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 *
 *
 * @link       https://shopitpress.com
 * @since      1.0.0
 * @package    Sip_Front_End_Bundler_Woocommerce
 * @subpackage Sip_Front_End_Bundler_Woocommerce/class
 * @author     shopitpress <hello@shopitpress.com>
 */
class WooBundler {
	
	/**
	 * To check the woocommerece plugin is active or not, if not then deactivate this plugin
	 *		 		
	 * @since    1.0.0
	 * @access   public
	 */
	public static function plugin_activation() {
		
		if( !is_plugin_active('woocommerce/woocommerce.php') ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( "This is WooCommerce add-on and requires WooCommerce plugin to be installed/active.<br/>Sorry for inconvenience!" );
		}
		
		self::create_tables();
	}
	
	/**
	 * for plugin deactivation
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public static function plugin_deactivation() {
		
	}
	
	/**
	 * to create database table
	 *		 		
	 * @since    1.0.0
	 * @access   private		 
	 */
	private static function create_tables() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		global $wpdb, $db;
		$charset_collate = '';
		
		if (! empty( $wpdb->charset ))
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";

		if (! empty( $wpdb->collate ))
			$charset_collate .= " COLLATE {$wpdb->collate}";

		$query = "CREATE TABLE {$db->settings} (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			meta_key varchar(50) NOT NULL,
			meta_value longtext,
			UNIQUE KEY id (id)
			) $charset_collate;";
			
		//dbDelta( $query );
	}
	
	/**
	 * set variable in database
	 *		 		
	 * @since    1.0.0
	 * @access   private		 
	 */
	private static function set_vars( $bool ) {
		global $wpdb, $db;
		
		$active = $wpdb->get_var("SELECT meta_value FROM {$db->settings} WHERE meta_key = 'active';");
		
		if(!option_exists( 'active' )) {
			$wpdb->insert($db->settings, array( "meta_key" => "active", "meta_value" => $bool ));
		} else {
			$wpdb->update($db->settings, array( "meta_value" => $bool ), array( "meta_key" => "active" ));
		}
		
	}
	
}