<?php

	/**
	 * It will display the default template of SIP Front End Bundler for WooCommerce
	 *
	 *
	 * @since      1.0.0
	 *
	 * @package    Sip_Front_End_Bundler_Woocommerce
	 * @subpackage Sip_Front_End_Bundler_Woocommerce/template
	 */
	$bundler->do_actions();

	$perqty = $bundler->get_field("allow-product-quantity");
	$bundleqty = $bundler->get_field("allow-bundle-quantity");

	$products = $bundler->get_products();
?>

<div id="woobundle" class="template-one">
	<form method="POST" id="woobundler-form" action="<?php echo $bundler->action; ?>">
		<h2><?php echo $bundler->get_the_title(); ?></h2>
		<?php echo ( $bundler->get_field("header") ) ? '<div class="desc"><span>' . $bundler->get_field("header") . '</span></div>' : ""; ?>
		<?php if( !empty(array_filter($products)) ) { ?>
			<div class="woos woonine woos-products">
			<?php
				$prdcount = $bundler->product_count();
				
				$boxclass = "woothree";
				if( $prdcount < 4 ) {
					switch($prdcount) {
						case 3:
							$boxclass = "woofour";
						break;
						case 2:
							$boxclass = "woosix";
						break;
						case 1:
							$boxclass = "wootwelve";
						break;
					}
				}
				
				$counter = -1;
				foreach( $products as $product ) {
					$counter = $product;
					
					$wbproduct = new wbproduct( $product );
					$product_metas = $wbproduct->get_product_metadata();
					
					?>
					<div class="woos <?php echo $boxclass; ?>">
						<div class="woos-product<?php echo ( ($perqty) ? "" : " noqty" ); ?>">
							<div class="woos-quantity">
								<div class="field">
									<div class="woos woofour">Qty: </div>
									<div class="woos wooseven qtyinput">
										<input type="number" min="0" max="<?php echo $wbproduct->get_stock(); ?>" name="bundler[product][<?php echo $counter; ?>][quantity]" readonly class="woos-quantity-input<?php echo ( ($perqty) ? "" : " readonly" ); ?>" data-error="<?php echo $bundler->get_field("outstock"); ?>" />
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="clearfix"></div>
								<div class="toggle">
									<label><input name="bundler[product][<?php echo $counter; ?>][added]" type="checkbox" class="woocheckbox" value="<?php echo $product; ?>" /> <span class="text">ADD</span></label>
									<?php
									if( $wbproduct->is_variable() && $bundler->get_field("display_vars") == 'hover' ) {
										$wbproduct->display_variations();
									}
									?>
								</div>
							</div>
							<div class="woos-product-details">
								<div class="image">
									<?php
										if( $wbproduct->is_variable() && ( ($bundler->get_field("display_vars") == 'active') || (! $bundler->get_field("display_vars")) ) ) {
											$wbproduct->display_variations( false );
										}
										echo $wbproduct->get_product_thumbnail();									
									?>
								</div>
								<div class="details">
									<h4 class="woos-product-title"><?php echo $wbproduct->get_product_title( $bundler->get_field("title-character-length") ); ?></h4>
									<?php
									if( $bundler->get_field("display-product-prices") ) {
										echo $wbproduct->get_price_html();
									}
									?>
									<span style="display: none;"><?php echo ( isset($product_metas["weight"]) && !empty($product_metas["weight"]) ) ? $product_metas["weight"] : ""; ?></span>
								</div>
							</div>
							<?php $wbproduct->hidden_inputs(); ?>
							<?php if( $wbproduct->is_variable() ) $wbproduct->script_variations(); ?>
						</div>
					</div>
				<?php } ?>
				<p style="text-align:center;">
					<span class="sip-febwc-icon-image">		
						<?php if(get_option('sip-febwc-affiliate-check-box') == "true") { ?>
							<?php $options = get_option('sip-febwc-affiliate-radio'); ?>
							<?php if( 'value1' == $options['option_three'] ) { $url = "https://shopitpress.com/?utm_source=referral&utm_medium=credit&utm_campaign=sip-front-end-bundler" ; } ?>
							<?php if( 'value2' == $options['option_three'] ) { $url = "https://shopitpress.com/?offer=". esc_attr( get_option('sip-febwc-affiliate-affiliate-username')) ; } ?>
							<a class="sip-febwc-credit" href="<?php echo $url ; ?>" target="_blank" data-tooltip="This bundle was created with SIP Front End Bundler Plugin">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
							Bundler powered by <a href="https://shopitpress.com/?utm_source=referral&amp;utm_medium=credit&amp;utm_campaign=sip-front-end-bundler" target="_blank">Shopitpress</a>							
						<?php } ?>
					</span>
				</p>
				<?php $bundler->script_offers(); ?>
				</div>
				<div class="woos woothree woos-total">
					<?php
					echo '<div class="errors"></div>';
					echo '<div class="savings">';
					echo '<h3>Total Savings</h3>';
					echo '<hr width="50%" />';
					echo '<h1>';
					$bundler->discount_html();
					echo '</h1>';
					echo '</div>';
					
					echo ($bundler->get_field("description")) ? '<hr /><p>' . $bundler->get_field("description") . '</p>' : "<p></p>";
					
					$bundler->quantity_input();
					$bundler->bundle_price();
					$bundler->hidden_inputs();

					$bundler->submit_html();
					?>
				</div>
			<?php } else { ?>
				<h3 style="text-align: center;">No product with this bundle.</h3>
			<?php } ?>
	</form>
</div>