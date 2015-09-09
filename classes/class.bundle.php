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
class bundle {

	/**
	 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $ID , $action  identifier for this plugin.
	 */		
	public $ID;
	public $action;
	
	/**
	 * A constructor, to create objects from a class.
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */	
	public function __construct( $bundleid ) {
		$this->ID 		= $bundleid;
		$this->action = $_SERVER["REQUEST_URI"];
	}
	
	/**
	 * to get the field usng the ID
	 *		 		
	 * @since    1.0.0
	 * @access   private		
	 * @return 	 string  
	 */	
	private function get_fields() {
		return get_post_meta( $this->ID, 'bundle', true);
	}
	
	/**
	 * A method to load the css and javascript files 
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function enqueue_scripts() {
		
		$template = $this->template();
		
		wp_enqueue_style( "woo_bundles_style-css" );
		wp_enqueue_style( "woo_bundles_resp-css" );
		
		wp_enqueue_script( "woo_bundles_elem-js" );
		wp_enqueue_script( "woo_bundles_bind-js" );
		
		echo '<script>var variable_products = [];</script>';
		
	}
	
	/**
	 * the function to return the template which is selected here default template is selected
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 * @return 	 string var
	 */
	public function template() {
		$fields 	= $this->get_fields();
			$template = "one";

		return $template;
	}
	
	/**
	 * to get the products list
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 * @return 	 string var
	 */
	public function get_products() {
		
		$fields 	= $this->get_fields();		
		$products = ( isset($fields["products"]) ? $fields["products"] : array() );
		$products = explode( ',', $products );
		
		return $products;
	}
	
	/**
	 * count the number of products and return it
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 * @return 	 int
	 */
	public function product_count() {
		$products = $this->get_products();
		return count($products);
	}
	
	/**
	 * A method to do action 
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */	
	public function do_actions() {
		$fields = $this->get_fields();
		
		do_action("custom_css", $fields);
		do_action("script_errors", $fields);
	}
	
	/**
	 * To get the field with the key 
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 * @return 	 string var
	 */
	public function get_field( $key ) {
		$fields = $this->get_fields();
		return ( isset($fields[$key]) && !empty($fields[$key]) ) ? $fields[$key] : "";
	}
	
	/**
	 * Get the title and retun it after the filtreation 
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 * @return 	 string var
	 */
	public function get_the_title() {
		$bundle = get_post($this->ID);
		return apply_filters('the_title', $bundle->post_title);
	}
	
	/**
	 * xxxxxxxxxxxxxxxxxxxxxxxxxxxx
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function script_offers() {
		
		$global_offers 	= get_offers($this->ID);
		$prd_offers 		= get_offers($this->ID, 'product-quantity');
		$tc_offers 			= get_offers($this->ID, 'total-cart');
		
		echo '<script>
			
			var offers_json = \''.json_encode($global_offers).'\';
			var offers = JSON.parse(offers_json);
			
			var product_offers_json = \''.json_encode($prd_offers).'\';
			var product_offers = JSON.parse(product_offers_json);
			
			var cart_offers_json = \''.json_encode($tc_offers).'\';
			var cart_offers = JSON.parse(cart_offers_json);

		</script>';
	}
	
	/**
	 * hidden fields to take desions 
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function hidden_inputs() {
		global $woocommerce;
		echo '
		<input type="hidden" name="bundler[id]" value="' . $this->ID . '" />
		<input type="hidden" name="bundler[total]" id="woos-total-input" />
		<input type="hidden" name="bundler[min-items]" id="woos-min-items-input" value="' . ( (!empty($this->get_field("min-items") && $this->get_field("min-items") > 0) ? $this->get_field("min-items") : -1) ) . '" />
		<input type="hidden" name="bundler[max-items]" id="woos-max-items-input" value="' . ( (!empty($this->get_field("max-items") && $this->get_field("max-items") > 0) ? $this->get_field("max-items") : -1) ) . '" />
		<input type="hidden" name="bundler[discount]" id="woos-discount-input" />
		<input type="hidden" name="bundler[redirect]" value="' . (( $this->get_field("redirect") ) ? urlencode($this->get_checkout_url()) : urlencode($this->get_cart_url()) ) . '" />
		';
		wp_nonce_field('woobundler-add-to-cart', 'nonce');
	}
	
	/**
	 * submit button to add to cart product 
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function submit_html() {
		echo '<a href="javascript:void(0);" class="woos-addcart"><span class="fa fa-shopping-cart"></span> ' . (( !empty($this->get_field("addcart")) ) ? $this->get_field("addcart") : "Add To Cart") . '</a>';
	}
	
	/**
	 * Get the url of the check out page 
	 *		 		
	 * @since    1.0.0
	 * @access   private		 
	 * @return 	 string url
	 */
	private function get_checkout_url() {
		global $woocommerce;
		return $woocommerce->cart->get_checkout_url();
	}

	/**
	 * Get the url of the cart out page 
	 *		 		
	 * @since    1.0.0
	 * @access   private		 
	 * @return 	 string url
	 */
	private function get_cart_url() {
		global $woocommerce;
		return $woocommerce->cart->get_cart_url();
	}
	
	/**
	 * number of produt to add in bundle 
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function quantity_input() {
		
		$bundleqty = $this->get_field("allow-bundle-quantity");
		
		echo '<div class="woos-quantity" style="display: ' . ( ($bundleqty) ? "block" : "none" ) . '">';
		echo '<label>Qty.</label>';
		echo '<input type="number" min="0" name="bundler[bundle-quantity]" value="1"' ." readonly" . ' />';
		echo '</div>';
	}
	
	/**
	 * To show the discount of the selected product base on bundle 
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function discount_html() {
		echo '<span data-percent="true" id="woos-discount">0</span>% OFF';
	}
	
	/**
	 * show the discount price
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function bundle_price() {
		echo '<div id="sip_currency_symbol" style="display:none;">' . get_woocommerce_currency_symbol() . '</div>';
		echo '<div class="woos-price">';
		echo '<del id="woos-total-price"></del> <ins id="woos-discounted-price">' . get_woocommerce_currency_symbol() . '0.00</ins>';
		echo '</div>';
	}
	/**
	 * show the discount price
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function bundle_description() {
		echo '<div class="woos-description">';
		echo '<h3>'. $this->get_field("description") .'</h3>';
		echo '</div>';
	}
	
	/**
	 * show in detail saving, price, text message and also submit button 
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function print_estimation() {
		echo '<div class="errors"></div>';
		echo '<div class="savings">';
		echo '<h3>Total Savings</h3>';
		echo '<h1>';
		$this->discount_html();
		echo '</h1>';
		echo '</div>';
		
		echo ($this->get_field("description")) ? '<hr /><p>' . $this->get_field("description") . '</p>' : "<p></p>";
		
		$this->quantity_input();
		$this->bundle_price();
		$this->hidden_inputs();

		echo '<a href="javascript:void(0);" class="woos-addcart" onclick="javascript:document.getElementById(\'woobundler-form\').submit(); return false;"><span class="fa fa-shopping-cart"></span> ' . (( !empty($this->get_field("addcart")) ) ? $this->get_field("addcart") : "Add To Cart") . '</a>';
	}
}