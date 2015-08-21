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
class Type {
	
	/**
	 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $slug, $name, $taxonomies, $extras, $extra_labels
	 */	
	private $slug, $name, $taxonomies, $extras, $extra_labels;
	
	/**
	 * A constructor, to create objects from a class.
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function __construct( $slug, $name, $taxonomies, $extras = array(), $extra_labels = array() ) {
		$this->slug = $slug;
		$this->name = $name;
		$this->taxonomies = $taxonomies;
		$this->extras = $extras;
		$this->extra_labels = $extra_labels;
		
		$this->register();
	}
	
	/**
	 * xxxxxxxxxxxxxxxxxxxxx
	 *		 		
	 * @since    1.0.0
	 * @access   public		 
	 */
	public function register() {
		$labels = array(
			"name" => _x( $this->name, "post type general name", "MLP"),
			"name_admin_bar" => __($this->name, "add new on admin bar", "MLP"),
		);
		
		$labels = array_merge($labels, $this->extra_labels);
		
		$arguments = array(
			"labels" => $labels,
			"rewrite" => array(
				"slug" => $this->slug,
				"with_front" => false
			),
			"has_archives" => true,
			"taxonomies" => $this->taxonomies,
			"supports" => array(
				"title",
				"editor",
				"thumbnail",
				"comments"
			)
		);
		
		$arguments = array_merge( $arguments, $this->extras );
		
		if (!post_type_exists( $this->slug ))
			register_post_type($this->slug, $arguments);
		
		flush_rewrite_rules();
	}
	
}