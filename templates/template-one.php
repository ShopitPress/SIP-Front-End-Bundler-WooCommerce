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
										<input type="number" min="1" max="1" name="bundler[product][<?php echo $counter; ?>][quantity]" readonly class="woos-quantity-input readonly" data-error="<?php echo $bundler->get_field("outstock"); ?>" value="1"/>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="clearfix"></div>
								<div class="toggle">
									<label><input name="bundler[product][<?php echo $counter; ?>][added]" type="checkbox" checked class="woocheckbox" value="1" /></label>
								</div>
							</div>
							<div class="woos-product-details">
								<div class="image">
									<?php
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
						</div>
					</div>
				<?php } ?>
			
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