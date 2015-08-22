<?php
/**
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
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
	 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
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
		
		require_once( SIP_FEBWC_DIR . 'config/require.php' );
		
		$this->posttype = "sip-bundles";
	}

}