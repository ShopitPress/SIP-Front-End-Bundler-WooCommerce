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
			"menu_icon" => "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSI4MDBweCIgaGVpZ2h0PSI2NTQuNTcxcHgiIHZpZXdCb3g9IjAgMCA4MDAgNjU0LjU3MSIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgODAwIDY1NC41NzEiIHhtbDpzcGFjZT0icHJlc2VydmUiPjxnIGlkPSJYTUxJRF8yM18iPjxnPjxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik03OTUuMjc1LDExOC42MWM0LjY4Niw0LjQ0MiwxLjk0MSwxMy4yNSwxLjk0MSwxMy4yNWwtODcuMzg0LDkwLjUzN2MwLDAsNzIuMDk1LDExNC44NjQsNzIuNTI0LDExNS41NzVjNC4zODcsOS4xMTUtMi4wMTcsMTUuMzg1LTIuMDE3LDE1LjM4NVM0NTguNzcxLDQ2Mi42NjEsNDU3LjUwMiw0NjIuOTY5Yy0xOC44OTIsNS45MDMtMzAuNTk3LTIuMDM4LTMwLjU5Ny0yLjAzOEwzMzUuNDM0LDMyNC41MWwtMTc5LjIxLTEuMjg4bC0xOS4zMDItMTEuNTM5YzAsMC0xMzAuMDIxLTE1OC4xMTItMTMxLjI5LTE1OS42MTJjLTEzLjM2Ni0xNi4yMywxMS44MTYtMTQuMDk2LDExLjgxNi0xNC4wOTZsMTIxLjM0LDcuMDU4YzAsMC0zMS43MzUtODcuNzI5LTMxLjczNS04OC40NmMtMS44NDgtMTEuNDgsNi44NTEtMTQuMTE1LDYuODUxLTE0LjExNXMyNDUuNjI5LTMyLjQ0MSwyNDYuNDE0LTMyLjAzOGMxMC44NDYtMC4yMzEsMTguMzUxLDkuMjg4LDE4LjM1MSw5LjI4OGw1Ny41NzEsNzMuNDAzYzAsMCw1MS45NzEtNTYuMzg0LDUyLjI3LTU2LjQyMmMxNS42ODEtMTEuMTkyLDM3LjgwMy00LjM2NSwzNy44MDMtNC4zNjVTNzk0LjU2NSwxMTcuNjQ4LDc5NS4yNzUsMTE4LjYxeiIvPjwvZz48Zz48L2c+PC9nPjxwb2x5Z29uIGZpbGw9IiMyMzI4MkQiIHBvaW50cz0iMTQwLjA1MiwxNDUuNjY1IDQ0MS4yMjEsMTEyLjMzNCA2NzYuNDM0LDIxOC43NDQgMzM3LjMwNiwzMjIuNTg2ICIvPjxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0xNTUuMTc3LDMzNC4wNjF2NjkuMTM2YzAsMCwyLjk2NCwxMC45MjgsOC4yMDgsMTYuNzRjMC44ODIsMS4wNjksMTgxLjcwMSwyMjkuMjQ5LDE5MS4wMzIsMjMxLjQ5YzguNzEyLDQuNDg3LDE4Ljc2MSwwLDE4Ljc2MSwwUzY1Mi4wODEsNTM2Ljk1LDY1Mi45MzIsNTM2Ljg2OWM4LjE3OS0zLjMzNiwxNi4wODQtMTcuNjgsMTYuMDg0LTE3LjY4bDExLjk5Ni0xMTkuNjE0YzAsMC0yMzUuMiw3Ny45MjctMjM3Ljg0MSw3OS4yODJjLTEwLjYyOCwzLjgzNC0xNy43NDEsMC40OTQtMTcuNzQxLDAuNDk0TDMzNC4yNjYsMzM0LjJMMTU1LjE3NywzMzQuMDYxeiIvPjxwb2x5Z29uIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iNDEwLjU3MSwyOTQuMjE0IDMzOS4wMDYsMzE2LjE3NiAyODUuOTcsMjY5LjcwMyAzNjAuMDQsMjUwLjUwNSAiLz48cG9seWdvbiBmaWxsPSIjRkZGRkZGIiBwb2ludHM9IjQ4MC42NTEsMjcyLjM0MiA0MTQuNTM3LDI5Mi42MzQgMzY1LjU0MSwyNDkuNzAyIDQzMy45NjksMjMxLjk3ICIvPjxwb2x5Z29uIGZpbGw9IiNGRkZGRkYiIHBvaW50cz0iNjQ4Ljk1NiwyMjAuMzg0IDQ4NS45NzUsMjY5LjM1OSA0MjcuMjUsMjE2LjU4NiA1NjQuNTg5LDE3Ny4wMDkgIi8+PHBvbHlnb24gZmlsbD0iI0ZGRkZGRiIgcG9pbnRzPSI0MjIuNDE1LDIyNi41MjcgMjgxLjI3MSwyNjEuODQ4IDE2My44NTcsMTU2LjA4IDMxOS4yMDIsMTMzLjIwNyAiLz48cG9seWdvbiBmaWxsPSIjRkZGRkZGIiBwb2ludHM9IjU1NC41NDksMTcxLjgzNSA0MTQuOTIxLDIxMS4xMDUgMzMxLjA2NCwxMzQuNTg3IDQ0MS4zMTgsMTE4Ljc2NSAiLz48L3N2Zz4=",
			"labels" => $labels,
			"rewrite" => array(
				"slug" => $this->slug,
				"with_front" => false,
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