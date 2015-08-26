<div class="sip-credit-affiliate-link-warp">
  <h2>Be awesome</h2>
  <p>Do you like this plugin? Would you like to see even more great features? Please be awesome and help us maintain and develop free plugins by checking the option below</p>
	<form method="post" action="options.php">
	  <?php settings_fields( 'sip-febwc-affiliate-settings-group' ); ?>
	  <?php $options = get_option('sip-febwc-affiliate-radio'); ?>
			<label><input id="spc-febwc-affiliate-checkbox" type="checkbox" name="sip-febwc-affiliate-check-box" value="true" <?php echo esc_attr( get_option('sip-febwc-affiliate-check-box', false))?' checked="checked"':''; ?> /> Yes, I want to help development of this plugin</label><br />
			<div id="spc-febwc-diplay-affiliate-toggle">

				<label><input id="spc-febwc-discreet-credit" type="radio" name="sip-febwc-affiliate-radio[option_three]" value="value1"<?php checked( 'value1' == $options['option_three'] ); ?> checked/> Add a credit</label><br />
				<label><input id="spc-febwc-affiliate-link" 	type="radio" name="sip-febwc-affiliate-radio[option_three]" value="value2"<?php checked( 'value2' == $options['option_three'] ); ?> /> Add my affiliate link</label><br />
				<div id="spc-febwc-affiliate-link-box">
					<label><input type="text" name="sip-febwc-affiliate-affiliate-username" value="<?php echo esc_attr( get_option('sip-febwc-affiliate-affiliate-username')) ?>" /> Input affiliate username/ID</label><br />
				</div>
				<p class="sip-text">Make money recommending our plugins. Register for an affiliate account at <a href="https://shopitpress.com/affiliate-area/?utm_source=wordpress.org&amp;utm_medium=affiliate&amp;utm_campaign=sip-front-end-bundler-woocommerce" target="_blank">Shopitpress</a>.</p>
					
			</div>
		<?php submit_button(); ?>
	</form>
</div>

<script type="text/javascript">
	jQuery(document).ready(function(){

		jQuery("#spc-febwc-diplay-affiliate-toggle").hide();
		jQuery("#spc-febwc-affiliate-link-box").hide();

		if (jQuery('#spc-febwc-affiliate-checkbox').is(":checked"))
		{
		  jQuery("#spc-febwc-diplay-affiliate-toggle").show('slow');
		}

		jQuery('#spc-febwc-affiliate-checkbox').click(function() {
		  jQuery('#spc-febwc-diplay-affiliate-toggle').toggle('slow');
		})

		if (jQuery('#spc-febwc-affiliate-link').is(":checked"))
		{
		  jQuery("#spc-febwc-affiliate-link-box").show('slow');
		}

		jQuery('#spc-febwc-affiliate-link').click(function() {
		  jQuery('#spc-febwc-affiliate-link-box').show('slow');
		})

		jQuery('#spc-febwc-discreet-credit').click(function() {
		  jQuery('#spc-febwc-affiliate-link-box').hide('slow');
		})

	});
</script>
