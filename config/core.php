<?php
/**
 * Core plugin class which initializes custom post type
 *
 *
 * @link       https://shopitpress.com
 * @since      1.0.0
 * @package    Sip_Front_End_Bundler_Woocommerce
 * @subpackage Sip_Front_End_Bundler_Woocommerce/config/
 * @author     shopitpress <hello@shopitpress.com>
 */

class Core {

	/**
	 * a variable to store post type name
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $posttype 
	 */	
	public $posttype;

	/**
	 * A constructor, to create objects from a class.
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function __construct() {
		
		//require_once( SIP_FEBWC_DIR . 'config/require.php' );
		require_once( SIP_FEBWC_DIR . 'classes/woobundler.php' );
		require_once( SIP_FEBWC_DIR . 'classes/class.post_tab.php' );
		require_once( SIP_FEBWC_DIR . 'functions.php' );
		
		$this->posttype = "sip-bundles";
	}

}