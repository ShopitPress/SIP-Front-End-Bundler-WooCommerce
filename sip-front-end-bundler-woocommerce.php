<?php
/**
 *
 * @since      1.0.0
 * @package    Sip_Front_End_Bundler_Woocommerce
 * @author     shopitpress <hello@shopitpress.com>
 *
 * Plugin Name:				SIP Front End Bundler for WooCommerce
 * Plugin URI:				https://shopitpress.com/plugins/sip-front-end-bundler-woocommerce/
 * Description:				WooCommerce add-on: For having bundled products with custom bundle offers
 * Version:           1.0.0
 * Author:            ShopitPress <hello@shopitpress.com>
 * Author URI:        https://shopitpress.com
 * License:           GPL-2.0+
 * License URI:				http://www.gnu.org/licenses/gpl-2.0.txt
 * Copyright:					© 2015 ShopitPress(email: hello@shopitpress.com)
 * Text Domain:       WB
 * Domain Path:       /languages
 * Requires:					PHP5, WooCommerce Plugin
 * Last updated on:		20-08-2015
*/

if ( !defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

global $wpdb;

// Define plugin constants
define( 'SIP_FEBWC_NAME', 'SIP Front End Bundler for WooCommerce' );
define( 'SIP_FEBWC_VERSION', '1.0.0' );
define( 'SIP_FEBWC_PLUGIN_SLUG', 'sip-front-end-bundler-woocommerce' );
define( 'SIP_FEBWC_BASENAME', plugin_basename( __FILE__ ) );
define( 'SIP_FEBWC_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'SIP_FEBWC_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define(	'SIP_FEBWC_WEBURL', get_bloginfo( 'url' ));
define(	'SIP_FEBWC_CURRENT_URL', get_bloginfo('url') . $_SERVER['REQUEST_URI']);
define(	'SIP_FEBWC_PREFIX', $wpdb->prefix . "woo_front_end_bundler_");
define( 'SIP_FEBWC_PLUGIN_PURCHASE_URL', 'https://shopitpress.com/plugins/sip-front-end-bundler-woocommerce/' );

/**
 * Registers credit/affiliate link options
 *
 * @since 1.0.0
 *
 */
add_action( 'admin_init', 'sip_febwc_affiliate_register_admin_settings' );
function sip_febwc_affiliate_register_admin_settings() {
	register_setting( 'sip-febwc-affiliate-settings-group', 'sip-febwc-affiliate-check-box' );
	register_setting( 'sip-febwc-affiliate-settings-group', 'sip-febwc-affiliate-radio' );
	register_setting( 'sip-febwc-affiliate-settings-group', 'sip-febwc-affiliate-affiliate-username' );
}


register_deactivation_hook( __FILE__, array( 'Sip_Front_End_Bundler_WC_Admin' , 'sip_febwc_deactivate') );

require_once( SIP_FEBWC_DIR . 'config/core.php' );
require_once( SIP_FEBWC_DIR . 'admin/sip-front-end-bundler-admin.php' );

global $core;

// CORE INSTANCE
$core = new Core;