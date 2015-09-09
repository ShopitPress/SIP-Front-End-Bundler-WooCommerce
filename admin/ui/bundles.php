<div class="sip-tab-content">
  <div style="display: block;">
		<?php
		global $wpdb;

    $table = new Sip_Front_End_Bundler_WC_Admin_Post_Tab();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'front-end-bundler'), count($_REQUEST['id'])) . '</p></div>';
    }
  	?>
		<div class="wrap">
	    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	    <h2><?php _e('Bundles', 'front-end-bundler')?> <a class="add-new-h2"
	        href="<?php echo get_admin_url(get_current_blog_id(), 'post-new.php?post_type=sip-bundles');?>"><?php _e('Add new', 'front-end-bundler')?></a>
	    </h2>
	    <?php echo $message; ?>

	    <form id="bundles-table" method="GET">
	      <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
	      <?php $table->display() ?>
	    </form>

		</div>
  </div>
</div>