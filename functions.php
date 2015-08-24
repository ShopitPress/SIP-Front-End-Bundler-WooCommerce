<?php
/**
 * It will display the default template of SIP Front End Bundler for WooCommerce
 *
 *
 * @since      1.0.0
 *
 * @package    Sip_Front_End_Bundler_Woocommerce
 */

/**
 * Default time zone 
 *
 * @since      1.0.0
 */
date_default_timezone_set("America/New_York");

/**
 * load the text domain
 *
 * @since      1.0.0
 */
function sip_febwc_load_text_domain() {
	load_plugin_textdomain( 'WB' );
}

/**
 * init function to load the core class
 *
 * @since      1.0.0
 */
add_action('init', 'sip_febwc_init_function');
function sip_febwc_init_function() {
	global $core;
	
	// POST TYPES INSTANCE
	require_once( SIP_FEBWC_DIR . 'classes/class.type.php' );
	new Type( $core->posttype, "Bundles", array(), array( "public" => true, "show_in_menu" => true, "show_in_nav_menus" => true, "show_in_admin_bar" => true, "capability_type" => "post", "supports" => array("title") ), array( "name_admin_bar" => "Bundle", "all_items" => "All Bundles", "add_new_item" => "Add New Bundle", "edit_item" => "Edit Bundle", "new_item" => "New Bundle", "view_item" => "View Bundle", "search_items" => "Search Bundles", "not_found" => "No Bundle found", "not_found_in_trash" => "No Bundle found in trash", "parent_item_colon" => "Parent Bundle") );
}

/**
 * xxxxxxxxxxxxxxx
 *
 * @since      1.0.0
 */
add_action('wp_loaded', 'sip_febwc_loaded_function');
function sip_febwc_loaded_function() {
	
	global $core;
	
	if( isset($_POST) && isset($_POST['nonce']) ) {
		if( wp_verify_nonce($_POST['nonce'], 'woobundler-add-to-cart') ) {
			if( isset($_POST['bundler']) && isset($_POST['bundler']['id']) ) {
				
				global $woocommerce;
				$woocommerce->cart->empty_cart();
				
				$bundler = $_POST['bundler'];
				$bundle_quantity = 1;
				
				for($i = 0; $i < $bundle_quantity; $i++) {
					$bundle = get_post( $bundler['id'] );
					
					if( $bundle ) {
						
						$global_offers 	= get_offers($bundle->ID);
						$prd_offers 		= get_offers($bundle->ID, 'product-quantity');
						$tc_offers 			= get_offers($bundle->ID, 'total-cart');
						$offers 				= get_offers($bundle->ID, '');
						
						$total_products = 0;
						$total_amount 	= 0;
						$products_qty 	= array();
						
						$coupons 				= array("minimum" => "", "products" => "", "quantity" => "", "override" => "");
						$discounts 			= array("minimum" => 0, "products" => 0, "quantity" => 0, "override" => array( "percent" => 0, "coupon" => "" ));
						
						if( isset($bundler['product']) ) {
							foreach($bundler['product'] as $product) {
								
								$post_product = get_post($product["id"]);
								$productqty 	= get_post_meta( $product["id"], '_stock', true );
								$variationid 	= ( isset($product["variationid"]) && !empty($product["variationid"]) ) ? $product["variationid"] : "";
								
								if( isset($product['added']) && !empty($product['added']) && !empty($post_product) ) {
									$qty = ( isset($product['quantity']) && $product['quantity'] != "" && $product['quantity'] <= $productqty ) ? $product['quantity'] : 1;
									$total_products += intval($qty);
									
									if( isset($products_qty[$post_product->ID]) )
										$products_qty[$post_product->ID] += intval($qty);
									else
										$products_qty[$post_product->ID] = intval($qty);
									
									$total_amount += intval($product["price"]) * intval($qty);
									$added = $woocommerce->cart->add_to_cart($post_product->ID, $qty, $variationid);
								}
							}

							foreach( $offers as $offer ) {
								if( $offer["type"] == '0' ) {
									if( $total_amount > intval($offer["minimum"]) && $discounts["minimum"] < $offer["discount"] ) {
										$discounts["minimum"] = intval($offer["discount"]);
										
										if( isset($offer["override"]) && $offer["override"] != "" && $discounts["override"]["percent"] < $offer["discount"] ) {
											$discounts["override"]["percent"] = intval($offer["discount"]);
											$discounts["override"]["coupon"] 	= $offer["coupon"];
										}

										//array_push($coupons, $offer["coupon"]);
										$coupons["minimum"] = $offer["coupon"];
									}
								}
								if( $offer["type"] == '1' ) {
									if( isset($offer["cart"]) && $total_products >= $offer["cart"] && $discounts["products"] < intval($offer["discount"]) ) {
										$discounts["products"] = intval($offer["discount"]);
										
										if( isset($offer["override"]) && $offer["override"] != "" && $discounts["override"]["percent"] < $offer["discount"] ) {
											$discounts["override"]["percent"]	= intval($offer["discount"]);
											$discounts["override"]["coupon"]	= $offer["coupon"];
										}
										
										//array_push($coupons, $offer["coupon"]);
										$coupons["products"] = $offer["coupon"];
									}
								}
								if( $offer["type"] == '2' ) {
									foreach( $products_qty as $key => $qty ) {
										if( $key == $offer["id"] && $qty >= $offer["quantity"] && $discounts["quantity"] < intval($offer["discount"]) ) {
											$discounts["quantity"] = intval($offer["discount"]);
											
											if( isset($offer["override"]) && $offer["override"] != "" && $discounts["override"]["percent"] < $offer["discount"] ) {
												$discounts["override"]["percent"] = intval($offer["discount"]);
												$discounts["override"]["coupon"] = $offer["coupon"];
											}
											
											//array_push($coupons, $offer["coupon"]);
											$coupons["quantity"] = $offer["coupon"];
										}
									}
								}
							}
						}
					}
				}

				if( $discounts["override"]["percent"] > 0 ) {
					$coupons = array($discounts["override"]["coupon"]);
				}
				
				if( isset($coupons) && !empty($coupons) ) {
					foreach( $coupons as $coupon ) {
						$post_coupon = get_post( $coupon );
						$woocommerce->cart->add_discount($post_coupon->post_title);
					}
				}
				if( isset($bundler["redirect"]) && !empty($bundler["redirect"]) ) {
					$url = $bundler["redirect"]  ;		
					$url = utf8_decode(urldecode($url));
					wp_redirect( $url , 301 ); exit;
				}
			}
		}
	}
}


/**
 * Hide coupon from woocommerce check cart page 
 *
 * @since      1.0.0
 */
add_filter( 'woocommerce_cart_totals_coupon_label', 'sip_febwc_hide_coupon' );
function sip_febwc_hide_coupon() {
  echo "Discount Applied";
}

/**
 * to do action woocommerce check cart items
 *
 * @since      1.0.0
 */
function sip_febwc_action_woocommerce_check_cart_items() {
	global $woocommerce;
	$woocommerce->cart->empty_cart();
}
//add_action( 'woocommerce_check_cart_items', 'sip_febwc_action_woocommerce_check_cart_items', 10);

/**
 * To perferom an action on shortcode 
 *
 * @since      1.0.0
 * @return 		 string if template or ID does not exist
 */
add_shortcode('sip_front_end_bundler', 'sip_febwc_woobundler_shortcode');
function sip_febwc_woobundler_shortcode( $atts ) {
	
	$atts = shortcode_atts( array("id" => -1), $atts, "sip_front_end_bundler" );
	extract($atts);
	
	if( !empty($id) ) {
		$bundle = get_post($id);
		if( !empty($bundle) ) {
			
			require_once( SIP_FEBWC_DIR . "classes/class.bundle.php" );
			require_once( SIP_FEBWC_DIR . "classes/class.wbproduct.php" );
			
			$bundler = new bundle($bundle->ID);
			
			$bundler->enqueue_scripts();
			$template = $bundler->template();
			
			if( file_exists( SIP_FEBWC_DIR . "templates/template-{$template}.php" ) ) {
				include SIP_FEBWC_DIR . "templates/template-{$template}.php";
			} else {
				echo "<strong>WooBundler:</strong> Template doesn't exists.";
			}
			
			return;
		}
	}
	return "<strong>WooBundler:</strong> No bundle with defined ID.";
}

/**
 * Make child menue at admin page
 *
 * @since      1.0.0
 */
//add_action('admin_menu', 'sip_febwc_register_child_menus');
function sip_febwc_register_child_menus() {
	// MENUS
	//add_menu_page( 'WooBundler', 'WooBundler', 'edit_posts', 'woo_bundles', 'callback_woobundles_menu_page', '', 120 );
}

/**
 * load the css and javascript files
 *
 * @since      1.0.0
 */
add_action( 'wp_enqueue_scripts', 'sip_febwc_wp_register_scripts' );
function sip_febwc_wp_register_scripts() {
	wp_register_style( 'woo_bundles_style-css', SIP_FEBWC_URL . 'assets/css/style.css' );
	wp_register_style( 'woo_bundles_resp-css', SIP_FEBWC_URL . 'assets/css/responsive.css' );
	wp_register_style( 'woo_bundles_fontawesome-css', SIP_FEBWC_URL . 'assets/fontawesome/css/font-awesome.css' );
	wp_register_style( 'jquery-ui-core', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css' );
	
	wp_register_script( "woo_bundles_elem-js", SIP_FEBWC_URL . 'assets/js/elements.js', array('jquery'), '', true );
	wp_register_script( "woo_bundles_bind-js", SIP_FEBWC_URL . 'assets/js/binding.js', array('jquery'), '', true );
	wp_register_script( "jquery-ui-core-js", '//code.jquery.com/ui/1.11.4/jquery-ui.js', array('jquery'), '', true );
}

/**
 * load the css and javascript files to admin side
 *
 * @since      1.0.0
 */
add_action( 'admin_enqueue_scripts', 'sip_febwc_wp_admin_register_scripts' );
function sip_febwc_wp_admin_register_scripts() {
	// STYLES
	wp_register_style( 'woo_bundles_admin-css', SIP_FEBWC_URL . 'assets/css/admin.css' );
	// SCRIPTS
	wp_register_script( "woo_bundles_admin-vsort", SIP_FEBWC_URL . 'assets/js/jquery.vSort.min.js', array('jquery'), '', true );
	wp_register_script( "woo_bundles_admin-js", SIP_FEBWC_URL . 'assets/js/admin.js', array('jquery'), '', true );
}

add_action( 'admin_head', 'sip_febwc_admin_head_function' );
function sip_febwc_admin_head_function() {
	wp_enqueue_style('woo_bundles_admin-css');
	wp_enqueue_script('woo_bundles_admin-vsort');
}

/**
 * to get the field
 *
 * @since      1.0.0
 */
function option_exists( $name ) {
	global $wpdb, $db;
	$field = $wpdb->get_var("SELECT meta_value FROM {$db->settings} WHERE meta_key = '{$name}';");
	
	return (( !is_null( $field ) ) ? true : false);
}

/**
 * add the meta boxes in post page
 *
 * @since      1.0.0
 */
add_action( 'add_meta_boxes', 'sip_febwc_bundles_add_meta_box' );
function sip_febwc_bundles_add_meta_box() {
	global $core;
	
	add_meta_box( 'bundle-fields-design', __( 'Design', 'WB' ), 'bundles_callback_meta_box', $core->posttype, 'normal', 'high', array('type' => 'design') );
	add_meta_box( 'bundle-fields-text', __( 'Text', 'WB' ), 'bundles_callback_meta_box', $core->posttype, 'normal', 'high', array('type' => 'text') );
	add_meta_box( 'bundle-fields-products-add', __( 'Add Products', 'WB' ), 'bundles_callback_meta_box', $core->posttype, 'side', 'core', array('type' => 'products-add') );
	add_meta_box( 'bundle-fields-setting', __( 'Setting', 'WB' ), 'bundles_callback_meta_box', $core->posttype, 'normal', 'high', array('type' => 'setting') );	
	add_meta_box( 'woobundler-product-fields', __( 'WooBundler', 'WB' ), 'bundles_callback_meta_box', 'product', 'normal', 'high', array('type' => 'woocommerce') );
	add_meta_box( 'bundle-fields-offers', __( 'Offers', 'WB' ), 'bundles_callback_meta_box', $core->posttype, 'normal', 'high', array('type' => 'offers') );
}
/**
 * callback meta box
 *
 * @since      1.0.0
 */
function bundles_callback_meta_box( $post, $type ) {
	$type = $type["args"]["type"];
	$bundle = get_post_meta( $post->ID, 'bundle', true );
	
	switch($type) {
		
		case "design": ?>
			<div class="bundle-fields">
				<div class="field">
					<div class="boxes templates">
						<div class="box one<?php echo ( (isset($bundle["template"]) && $bundle["template"] == 'one') ? " active" : "" ); ?>" data-label="Template #1" data-value="one"></div>
						
						<div class="box two"  style="pointer-events: none;cursor: default;" data-label="Template #2" data-value="two"></div>
						<div class="box three"  style="pointer-events: none;cursor: default;" data-label="Template #3" data-value="three"></div>
						
						<input type="hidden" class="template-input" name="bundle[template]" value="<?php echo ( (isset($bundle["template"]) && !empty($bundle["template"])) ? $bundle["template"] : "" ); ?>" />
					</div>
				</div>
				<div class="field">
					<label>Display Variations</label>
					<select name="bundle[display_vars]">
						<option value="active"<?php echo ( (isset($bundle["display_vars"]) && $bundle["display_vars"] == 'active') ? " selected" : ( !isset($bundle["display_vars"]) || (isset($bundle["display_vars"]) && $bundle["display_vars"] != 'hover') ) ? " selected" : "" ); ?>>On Active</option>
						<option value="hover"<?php echo ( (isset($bundle["display_vars"]) && $bundle["display_vars"] == 'hover') ? " selected" : "" ); ?>>On hover</option>
					</select>
				</div>
			</div>
		<?php break; ?>
		
		<?php case "text": ?>
			<div class="bundle-fields">
				<div class="field">
					<label>Header</label>
					<input type="text" name="bundle[header]" value="<?php echo ( (isset($bundle["header"]) && !empty($bundle["header"])) ? $bundle["header"] : "" ); ?>" />
				</div>
				<div class="field">
					<label>Description</label>
					<input type="text" name="bundle[description]" value="<?php echo ( (isset($bundle["description"]) && !empty($bundle["description"])) ? $bundle["description"] : "" ); ?>" />
				</div>
				<div class="field">
					<label>Add to cart</label>
					<input type="text" name="bundle[addcart]" value="<?php echo ( (isset($bundle["addcart"]) && !empty($bundle["addcart"])) ? $bundle["addcart"] : "" ); ?>" />
				</div>
				<div class="field">
					<label>Out of Stock</label>
					<input type="text" name="bundle[outstock]" value="<?php echo ( (isset($bundle["outstock"]) && !empty($bundle["outstock"])) ? $bundle["outstock"] : "" ); ?>" />
				</div>

				<div class="field" onclick="alert('This feature is available only in PRO version');">
					<label>Combination Error Text</label>
					<small>You can bind: [product-name],[product-price],[product-sku],[product-description],[product-id]</small>
					<textarea name="bundle[combination-error]" disabled ><?php echo ( (isset($bundle["combination-error"]) && !empty($bundle["combination-error"])) ? $bundle["combination-error"] : "Sorry, the combination chosen for the product \"[product-name]\" is unavailable. Please choose a different combination." ); ?></textarea>
				</div>

			</div>
		<?php break; ?>
		
		<?php case "products-add":
			?>
			<script>
				var productSKUS 	= [
						<?php
						$allproducts 	= get_posts( array("post_type" => "product", "showposts" => -1, "posts_per_page" => -1) );
						foreach( $allproducts as $product ) {
							$product_ 	= wc_get_product($product);
							?>
						{
							value: "<?php echo $product->ID; ?>",
							label: "<?php echo get_post_meta($product->ID, '_sku', true); ?>",
							desc:  "<?php echo $product->post_title; ?> <small>(<?php echo ( !$product_->is_type('variable') ? '$' . ( (get_post_meta( $product->ID, '_sale_price', true )) ? get_post_meta( $product->ID, '_sale_price', true ) : ( (get_post_meta( $product->ID, '_regular_price', true )) ? get_post_meta( $product->ID, '_regular_price', true ) : 0 ) ) : "variable product"); ?>)</small>"
						},
						<?php } ?>
					];
			</script>
			<div class="bundle-fields">
				<div class="field ui-widget" id="add-product">
					<input type="text" id="tags" placeholder="Search product by SKU" />
					<input type="hidden" name="bundle[products]" class="products-field" />
				</div>
				<div id="products" data-callback="" style="">
					<?php
						if( isset($bundle["products"]) && !empty($bundle["products"]) ) {
							$products 	= explode(',', $bundle["products"]);
							foreach( $products as $product ) {
								$product 	= get_post($product);
								$product_ = wc_get_product($product);
								if( $product ) {
									?>
									<div class="sortitem" data-id="<?php echo $product->ID; ?>">
										<span class="sorthandle" unselectable="on"> </span><?php echo $product->post_title; ?> <small><?php echo ( !$product_->is_type('variable') ? '$' . ( (get_post_meta( $product->ID, '_sale_price', true )) ? get_post_meta( $product->ID, '_sale_price', true ) : ( (get_post_meta( $product->ID, '_regular_price', true )) ? get_post_meta( $product->ID, '_regular_price', true ) : 0 ) ) : "variable product"); ?></small><br/>SKU: <?php echo get_post_meta($product->ID, '_sku', true); ?><span class="close">x</span></div>
									<?php
								}
							}
						}
					?>
				</div>
			</div>
		<?php break; ?>
		
		<?php case "setting": ?>
			<div class="bundle-fields">
				<div class="field" onclick="alert('This feature is available only in PRO version');">
					<label>Define quantity of items per bundle</label>
					<label><input style="display: inline; width: 10%;" type="number" name="bundle[min-items]" value="1" disabled /> MIN</label>
					<label><input style="display: inline; width: 10%;" type="number" name="bundle[max-items]" value="1" disabled/> MAX</label>
				</div>
				<div class="field">
					<label><input type="checkbox" name="bundle[display-product-prices]" <?php echo ( (isset($bundle["display-product-prices"])) ? "checked" : "" ); ?> /> Display Product Prices</label>
				</div>
				<div class="field" onclick="alert('This feature is available only in PRO version');">
					<label><input disabled type="checkbox" name="bundle[allow-bundle-quantity]" /> Allow quantity input</label>
				</div>
				<div class="field" onclick="alert('This feature is available only in PRO version');">
					<label><input disabled type="checkbox" name="bundle[allow-product-quantity]" /> Allow per product quantity input</label>
				</div>
				<div class="field">
					<label><input type="checkbox" name="bundle[redirect]" <?php echo ( (isset($bundle["redirect"])) ? "checked" : "" ); ?> /> Redirect to checkout after selection</label>
				</div>
				<div class="field">
					<label>Limit Product Title Characters</label>
					<input type="number" name="bundle[title-character-length]" value="<?php echo ( (isset($bundle["title-character-length"]) && !empty($bundle["title-character-length"])) ? $bundle["title-character-length"] : "25" ); ?>" />
				</div>
				<div class="field">
					<label>Custom CSS</label>
					<textarea name="bundle[customcss]"><?php echo ( (isset($bundle["customcss"])) ? $bundle["customcss"] : "" ); ?></textarea>
				</div>
			</div>		
		<?php break; ?>


		<?php case "offers": ?>
			<div class="bundle-fields">
				<div class="field">
					<?php
						$offers = isset($bundle["offers"]) ? $bundle["offers"] : array();
						//echo '<pre>';var_dump($offers);echo '</pre>';
						$types = get_offer_types();
						$offers = array_values($offers);
						
						echo '<script>';
						echo 'var offerCount 	= '.sizeof($offers).';';
						echo 'var types 			= ["'.implode('", "', $types).'"];';
						echo '</script>';
					?>
					<div id="coupon-exists">
						<?php
						$exists = false;
						if( isset($_GET['coupon']) && !empty($_GET['coupon']) ) {
							$coupons = get_posts( array("post_type" => "shop_coupon", "number" => -1, "showposts" => -1, "posts_per_page" => -1) );
							foreach( $coupons as $coupon ) {
								if( $coupon->post_title == htmlspecialchars( trim( preg_replace('/\s+/',' ', $_GET['coupon']))) ) {
									$exists = true;
									break;
								}
							}
							echo ($exists) ? "1" : "0";
						}
						?>
					</div>
					<table width="100%" id="offers" border="1">
						<thead>
							<tr>
								<th>CSS Class</th>
								<th width="250">Desc(Optional)</th>
								<th width="250">Type</th>
								<th>Value</th>
								<th width="100">Discount Type</th>
								<th>Discount</th>
								<th>Coupon Name</th>
								<th>Override</th>
								<th><i id="loader" style="display: none; background: url(<?php echo SIP_FEBW_URL ; ?>assets/img/ajax-loader.GIF); width: 35px; height: 35px;"></i></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><input type="text" name="offer[]" id="css" class="ifield" /></td>
								<td><input type="text" name="offer[]" id="desc" class="ifield" /></td>
								<td align="center">
									<select id="type">
										<?php
											foreach($types as $key => $type) {
												echo '<option value="'.$key.'">'.$type.'</option>';
											}
										?>
									</select>
									<select id="type-product" style="display: none;">
										<?php
											$products = get_posts(array("post_type" => "product", "showposts" => -1, "posts_per_page" => -1));
											foreach( $products as $key => $product ) {
												echo '<option value="'.$product->ID.'">SKU: '.get_post_meta($product->ID, '_sku', true).'</option>';
											}
										?>
									</select>
								</td>
								<td><input type="text" name="offer[]" id="value" class="ifield" /></td>
								<td><select name="offer[]" id="discount-type"><option value="amount">amount</option><option value="percent">percent</option></select></td>
								<td><input type="text" name="offer[]" id="discount" class="ifield" /></td>
								<td><input type="text" name="offer[]" id="coupon-name" class="ifield coupon" /></td>
								<td align="center"><input type="checkbox" name="offer[]" id="override" class="ifield" disabled /></td>
								<td><a href="#" class="button" id="newoffer">+</a></td>
							</tr>
							<?php
							$count = -1;
							while( $count != (sizeof($offers)-1) ) {
								$count++;
								
								echo '<tr class="data">';
								echo '<td data-id="#css"><input type="text" value="'.( (isset($offers[$count]["css"]) ? $offers[$count]["css"] : "") ).'" name="bundle[offers]['.$count.'][css]" /></td>';
								echo '<td data-id="#desc"><input type="text" value="'.( (isset($offers[$count]["desc"]) ? $offers[$count]["desc"] : "") ).'" name="bundle[offers]['.$count.'][desc]" /></td>';
								echo '<td data-id="#type">' . ( (isset($offers[$count]["type"]) ? $types[$offers[$count]["type"]] : "") ) . ( (isset($offers[$count]["type-product"]) && !empty($offers[$count]["type-product"])) ? ' SKU: '.get_post_meta($offers[$count]["type-product"], '_sku', true) : "" ) . '<span class="product-id" style="display: none;">'.$offers[$count]["type-product"].'</span></td>';
								echo '<td data-id="#value"><input type="text" value="'.( (isset($offers[$count]["value"]) ? $offers[$count]["value"] : "") ).'" name="bundle[offers]['.$count.'][value]" /></td>';
								echo '<td data-id="#discount-type">'.( (isset($offers[$count]["discount-type"]) ? $offers[$count]["discount-type"] : "") ).'</td>';
								echo '<td data-id="#discount"><input type="text" value="'.( (isset($offers[$count]["discount"]) ? $offers[$count]["discount"] : "") ).'" name="bundle[offers]['.$count.'][discount]" /></td>';
								echo '<td data-id="#coupon-name">'.( (isset($offers[$count]["coupon-name"]) ? $offers[$count]["coupon-name"] : "") ).'</td>';
								echo '<td data-id="#override" align="center"><input type="checkbox"' . ( (isset($offers[$count]["override"]) && $offers[$count]["override"] == "on") ? " checked" : "" ) . ' name="bundle[offers]['.$count.'][override]" /></td>';
								echo '<td><a href="#" class="button removebtn">x</a></td>';
								echo '</tr>';
								
								echo '<tr class="hidden">';
								echo '<td><input type="hidden" value="'.( (isset($offers[$count]["id"]) ? $offers[$count]["id"] : "") ).'" name="bundle[offers]['.$count.'][id]" /></td>';
								echo '<td><input type="hidden" value="'.( (isset($offers[$count]["type"]) ? $offers[$count]["type"] : "") ).'" name="bundle[offers]['.$count.'][type]" /><input type="hidden" value="'.( (isset($offers[$count]["type-product"]) ? $offers[$count]["type-product"] : "") ).'" name="bundle[offers]['.$count.'][type-product]" /></td>';
								echo '<td><input type="hidden" value="'.( (isset($offers[$count]["coupon-name"]) ? $offers[$count]["coupon-name"] : "") ).'" name="bundle[offers]['.$count.'][coupon-name]" /></td>';
								echo '<td><input type="hidden" value="'.( (isset($offers[$count]["discount-type"]) ? $offers[$count]["discount-type"] : "") ).'" name="bundle[offers]['.$count.'][discount-type]" /></td>';
								echo '</tr>';
								
							};
							?>
						</tbody>
					</table>
				</div>
				<div class="field">
					<label><input type="checkbox" name="bundle[shipping]" <?php echo ( (isset($bundle["shipping"])) ? "checked" : "" ); ?> /> Display free shipping notice</label>
					<small>Note: the plugin only displays a notice but does not enable free shipping. The value above which free shipping is enabled can be configured in woocommerce settings</small>
				</div>
			</div>
		<?php break; ?>
		<?php case "woocommerce": ?>
			<div class="bundle-fields">
				<div class="field">
					<label>Weight</label>
					<input type="text" name="bundle[weight]" value="<?php echo ( (isset($bundle["weight"]) && !empty($bundle["weight"])) ? $bundle["weight"] : "" ); ?>" />
				</div>
			</div>
		<?php break; ?>
		<?php
	}
}

/**
 * save the meta box data
 *
 * @since      1.0.0
 */
function bundles_save_meta_box( $postid ) {

	
	if( isset($_POST['bundle']) ) {
	

		global $core;
		update_post_meta( $postid, 'bundle', $_POST['bundle'] );
		$bundle = get_post_meta( $postid, 'bundle', true );
		if( isset($_POST['bundle']['offers']) ) {
			$count = -1;
			
			$oldoffers_bundle = get_posts( array("post_type" => "shop_coupon", "meta_key" => "bundleid", "meta_value" => $postid, "number" => -1, "showposts" => -1, "posts_per_page" => -1) );
			foreach( $oldoffers_bundle as $oldoffers_bundle_ ) {
				wp_delete_post( $oldoffers_bundle_->ID, true );
			}
			
			foreach($_POST['bundle']['offers'] as $key => $offer) {
				$count++;
				
				$coupon_number = rand_();
				if( isset($_POST['bundle']['offers'][$key]['coupon-name']) && !empty($_POST['bundle']['offers'][$key]['coupon-name']) ) {
					$coupon_number = $_POST['bundle']['offers'][$key]['coupon-name'];
				}
				
				$args = array(
							"post_title"	=> $coupon_number,
							"post_status"	=> 'publish',
							"post_type"		=> 'shop_coupon'
						);
				$wp_error = "";
				
				remove_action('save_post', 'bundles_save_meta_box');
				$coupon = wp_insert_post( $args, $wp_error );
				add_action('save_post', 'bundles_save_meta_box');

				if( $coupon ) {
					
					$_POST['bundle']['offers'][$key]['coupon'] = $coupon;
					
					update_post_meta( $coupon, 'bundleid', $postid );
					update_post_meta( $coupon, 'discount_type', ( (isset($_POST['bundle']['offers'][$key]['discount-type']) && $_POST['bundle']['offers'][$key]['discount-type'] == 'amount') ? 'fixed_cart' : 'percent_product' ) );
					update_post_meta( $coupon, 'coupon_amount', $_POST['bundle']['offers'][$key]['discount'] );
					update_post_meta( $coupon, 'product_ids', $bundle['products'] );
					update_post_meta( $coupon, 'minimum_amount', $bundle['offers'][$count]['value'] );
				}
			}
		}
		update_post_meta( $postid, 'bundle', $_POST['bundle'] );
	}
}
add_action( 'save_post', 'bundles_save_meta_box' );

/**
 * load the css and javascript for meta box
 *
 * @since      1.0.0
 */
function bundles_style_meta_box() {
	global $typenow, $core;
	if( $typenow == $core->posttype ) {
		wp_enqueue_style('jquery-ui-core', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css');
		
		wp_enqueue_script('jquery-ui-core-js', '//code.jquery.com/ui/1.11.4/jquery-ui.js', array('jquery'), '', true);
		wp_enqueue_script('woo_bundles_admin-js');
	}
}
add_action( 'admin_print_styles', 'bundles_style_meta_box' );

/**
 * get the type of the offer
 *
 * @since      1.0.0
 * @return 	 	 array string
 */
function get_offer_types() {
	return array( "Minimum amount", "Total qty. cart", "Product qty." );
}

/**
 * get offers detail
 *
 * @since      1.0.0
 * @return 	 	 array string
 */
function get_offers( $bundleid, $type = "minimum" ) {
		
	$bundle = get_post_meta( $bundleid, 'bundle', true );
	$offers = isset($bundle["offers"]) ? $bundle["offers"] : array();
	
	$types = get_offer_types();
	$return = array();
	
	$override = false;
	foreach( $offers as $key => $offer ) {
		if(isset($offer["override"]) && $offer["override"] == 'on') {
			$override = true;
			break;
		}
	}
	
	if( $type == 'product-quantity' ) {
		foreach( $offers as $key => $offer ) {
			if( $offer["type"] == '2' ) {
				$goffer = array("id" => $offer["type-product"], "quantity" => $offer["value"], "discount" => $offer["discount"], "discount_type" => $offer["discount-type"], "applied" => false, "coupon" => $offer["coupon"], "css" => $offer["css"], "desc" => $offer["desc"], "override" => ( isset($offer["override"]) && $offer["override"] == "on" ) ? true : false );
				array_push( $return, $goffer );
			}
		}
	} else if( $type == 'minimum' ) {
		foreach( $offers as $key => $offer ) {
			if( $offer["type"] == '0' ) {
				
				$goffer = array("id" => $key, "discount" => $offer["discount"], "discount_type" => $offer["discount-type"], "applied" => false, "coupon" => $offer["coupon"], "css" => $offer["css"], "desc" => $offer["desc"], "override" => ( isset($offer["override"]) && $offer["override"] == "on" ) ? true : false );
				$goffer["minimum"] = $offer["value"];
				
				array_push( $return, $goffer );
			}
		}
	} else if( $type == 'total-cart' ) {
		foreach( $offers as $key => $offer ) {
			if( $offer["type"] == '1' ) {
				
				$goffer = array("id" => $key, "discount" => $offer["discount"], "discount_type" => $offer["discount-type"], "applied" => false, "coupon" => $offer["coupon"], "css" => $offer["css"], "desc" => $offer["desc"], "override" => ( isset($offer["override"]) && $offer["override"] == "on" ) ? true : false );
				$goffer["cart"] = $offer["value"];
				
				array_push( $return, $goffer );
			}
		}
	} else {
		foreach( $offers as $key => $offer ) {
			//if( (($override == true && isset($offer["override"]) && $offer["override"] == 'on') || $override == false) ) {
				
				$goffer = array("id" => ( isset($offer["type-product"]) ? $offer["type-product"] : $key ), "discount" => $offer["discount"], "discount_type" => $offer["discount-type"], "applied" => false, "coupon" => $offer["coupon"], "type" => $offer["type"], "css" => $offer["css"], "desc" => $offer["desc"], "override" => ( isset($offer["override"]) && $offer["override"] == "on" ) ? true : false );
				
				if( $offer["type"] == '0' )
					$goffer["minimum"] = $offer["value"];
				else if( $offer["type"] == '1' )
					$goffer["cart"] = $offer["value"];
				else if( $offer["type"] == '2' )
					$goffer["quantity"] = $offer["value"];
				
				array_push( $return, $goffer );
			//}
		}
	}
	return $return;
}

/**
 * to generate random string
 *
 * @since      1.0.0
 * @return 	 	 string
 */
function rand_($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * custome styleing 
 *
 * @since      1.0.0
 */
add_action("custom_css", "function_do_custom_css", 10, 1);
function function_do_custom_css( $fields ) {
	if( isset($fields["customcss"]) && !empty($fields["customcss"]) ) {
		echo '<style>' . ( $fields["customcss"] ) . '</style>';
	}
}

/**
 * give the error if any problem
 *
 * @since      1.0.0
 */
add_action("script_errors", "function_do_script_errors", 10, 1);
function function_do_script_errors( $fields ) {
	$errors["combination_error"] = (!empty($fields["combination-error"]) ? $fields["combination-error"] : "Sorry, the combination chosen for the product \"[product-name]\" is unavailable. Please choose a different combination.");
	$errors["outstock"] = (!empty($fields["outstock"]) ? $fields["outstock"] : "Not enough stock");
	
	echo '<script>var wooerrors = '.json_encode( $errors ).';</script>';
}


/**
* Modify the coupon errors:
*/
add_filter( 'woocommerce_coupon_error', 'sip_coupon_error', 10, 2 );

function sip_coupon_error( $err, $err_code ) {	
  return ( '103' == $err_code ) ? '' : $err;
}