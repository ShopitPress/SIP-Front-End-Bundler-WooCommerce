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
class wbproduct {
	
	/**
	 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $ID
	 */
	private $ID;
	
	/**
	 * A constructor, to create objects from a class.
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function __construct( $productid ) {
		$this->ID = $productid;
	}
	
	/**
	 * get the product meta data base on id from database table and return it to the template.
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 * @return 	 array string 
	 */
	public function get_product_metadata() {
		$product_metas = get_post_meta( $this->ID, 'bundle', true );
		
		$product_metas['qty'] 	= get_post_meta( $this->ID, '_stock', true );
		$product_metas['sku'] 	= get_post_meta( $this->ID, '_sku', true );
		$product_metas['type'] 	= get_post_meta( $this->ID, 'product-type', true );
		
		return $product_metas;
	}
	
	/**
	 * get the product meta data base on key
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 * @return 	 string 
	 */
	public function get_meta( $key, $default = "" ) {
		$product_metas = $this->get_product_metadata();
		return ( isset($product_metas[$key]) && !empty($product_metas[$key]) ) ? $product_metas[$key] : $default;
	}
	
	/**
	 * get the stock of the selected product.
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 * @return 	 string
	 */
	public function get_stock() {
		$product_metas = $this->get_product_metadata();
		return ceil((!empty($product_metas['qty'])) ? $product_metas['qty'] : "-1");
	}
	
	/**
	 * xxxxxxxx.
	 *		 		
	 * @since    1.0.0
	 * @access   private		 
	 * @return 	 string
	 */
	private function get_wc_product() {
		return wc_get_product($this->ID);
	}
	
	/**
	 * to check the wcproduct is variable
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 * @return 	 string
	 */
	public function is_variable() {
		$wcproduct = $this->get_wc_product();
		return $wcproduct->is_type('variable');
	}
	
	/**
	 * get the attributes of the selected product
	 *		 		
	 * @since    1.0.0
	 * @access   private
	 * @return 	 string array		 
	 */
	private function get_attributes() {
		$wcproduct = $this->get_wc_product();
		return $wcproduct->get_attributes();
	}
	
	/**
	 * to display the product variation base on attributes.
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function display_variations( $class = "" ) {
		$attributes = $this->get_attributes();
		
		echo '<div class="variation' . $class . '">';
			$i = $j = -1;
			foreach( $attributes as $attr ) {
				$i++;
				echo '<div class="woos-field">';
				echo '<label>' . $attr["name"].':</label>';
				
				$values = explode(' | ', $attr["value"]);
				foreach( $values as $val ) {
					$j++;
					echo '<span><input type="radio" name="bundler[product][' . $this->ID . '][options][' . $i . ']" id="radio-' . $j . '" value="' . sanitize_title( $val ) . '" data-option="' . $i . '" /> <label>' . $val . '</label></span>';
				}
				echo '</div>';
			}
		echo '</div>';
	}
	
	/**
	 * xxxxxxxxxxxxxxxxx
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function script_variations() {
		$variations = $this->get_available_variations();
		
		if( $variations ) {
			echo '<script>var vars = { productid: ' . $this->ID . ', vars: [';
			$i = 0;
			foreach( $variations as $var ) {
				if( !empty($var["attributes"]) ) {
					$i++;
					echo '[ ';
					$j = 0;
					foreach( $var["attributes"] as $key => $value ) {
						$j++;
						echo "'{$value}'" . (( sizeof($var["attributes"]) > $j ) ? ',' : '');
					}
					echo ',' . ( ( isset($var["variation_id"]) ) ? $var["variation_id"] : "" ) . ',' . (( isset($var["display_price"]) && $var["display_price"] > 0 ) ? $var["display_price"] : ( isset($var["display_regular_price"]) ? $var["display_regular_price"] : 0 )) . ',' . (( isset( $var["max_qty"] ) && $var["max_qty"] > 1 ) ? ( (isset( $var["is_in_stock"] ) && $var["is_in_stock"] == true) ? $var["max_qty"] : 0 ) : 1) . ' ]' . (( sizeof($variations) > $i ) ? ',' : '');
				}
			}
			echo ']}; variable_products['.$this->ID.'] = vars;
			</script>';
		}
	}
	
	/**
	 * xxxxxxxxxxxxxxxx
	 *		 		
	 * @since    1.0.0
	 * @access   public	
	 * @return 	 array string	 
	 */
	public function get_available_variations() {
		$wcproduct = $this->get_wc_product();
		return $wcproduct->get_available_variations();
	}
	
	/**
	 * to get the product thumbnail.
	 *		 		
	 * @since    1.0.0
	 * @access   public
	 * @return 	 array string		 
	 */
	public function get_product_thumbnail() {
		return ( (has_post_thumbnail($this->ID)) ? get_the_post_thumbnail( $this->ID, 'large', array( 'alt' => 'product-image', 'title' => get_the_title($this->ID) ) ) : '<img src="' . SIP_FEBWC_URL . 'assets/img/null.png" alt="no-image" title="no featured image" />' );
	}
	
	/**
	 * to get the product title and apply filter on it
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 * @return 	 string var
	 */
	public function get_product_title($length = 25) {
		return apply_filters( 'the_title', substr(get_the_title($this->ID), 0, (( $length && intval($length) ) ? $length : 25) ));
	}
	
	/**
	 * get the price in html formate
	 *		 		
	 * @since    1.0.0
	 * @access   public		
	 * @return 	 string html 
	 */
	public function get_price_html() {
		$wcproduct = $this->get_wc_product();
		return '<div class="price">' . $wcproduct->get_price_html() . '</div>';
	}
	
	/**
	 * to get the price base on id
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 * @return 	 int
	 */
	public function get_price() {
		return ( ($price = get_post_meta( $this->ID, '_sale_price', true )) ? $price : ( ($price = get_post_meta( $this->ID, '_regular_price', true )) ? $price : 0 ) );
	}
	
	/**
	 * to get the product description base on id
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 * @return 	 string 
	 */
	public function product_description() {
		$product = get_post($this->ID);
		return htmlspecialchars( trim( preg_replace('/\s+/',' ', $product->post_content ) ), ENT_QUOTES );
	}
	
	/**
	 * hidden input for take decision
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function hidden_inputs() {
		echo '
		<input type="hidden" name="bundler[product][' . $this->ID . '][id]" value="' . $this->ID . '" class="woos-productid-input" />
		<input type="hidden" name="bundler[product][' . $this->ID . '][price]" value="' . $this->get_price() . '" class="woos-price-input" />
		<input type="hidden" name="bundler[product][' . $this->ID . '][stock]" value="' . $this->get_meta("qty", -1) . '" class="woos-stock-input" />
		<input type="hidden" name="bundler[product][' . $this->ID . '][variationid]" value="" class="woos-variationid-input" />
		<input type="hidden" name="bundler[product][' . $this->ID . '][sku]" value="' . $this->get_meta("sku") . '" class="woos-sku-input" />
		<input type="hidden" name="bundler[product][' . $this->ID . '][desc]" value="' . $this->product_description() . '" class="woos-desc-input" />
		';
	}
}