<?php
/**
 * Sip_Front_End_Bundler_WC_Admin_Post_Tab class that will display our custom table
 * records in nice table
 */
class Sip_Front_End_Bundler_WC_Admin_Post_Tab extends WP_List_Table
{
  /**
   * declare constructor and give some basic params
   */
  function __construct()
  {
    global $status, $page;

    parent::__construct(array(
      'singular'  => 'Bundles',
      'plural'    => 'Bundles',
    ));
  }

  /**
   * this is a default column renderer
   *
   * @param $item - row (key, value array)
   * @param $column_name - string (key)
   * @return HTML
   */
  function column_default($item, $column_name)
  {
    return $item[$column_name];
  }

  /**
   * how to render specific column
   *
   *
   * @param $item - row (key, value array)
   * @return HTML
   */
  function column_post_date($item)
  {
    return '<em>' . $item['post_date'] . '</em>';
  }

  /**
   *  render column with actions,
   *  hover row "Edit | Delete" links showed
   *
   * @param $item - row (key, value array)
   * @return HTML
   */
  function column_post_title($item)
  {
      
    $actions    = array(
      'edit'  => sprintf('<a href="post.php?post=%s&action=edit">%s</a>', $item['ID'], __('Edit', 'front-end-bundler')),
      'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['ID'], __('Delete', 'front-end-bundler')),
    );


    return sprintf('%s %s',
      $item['post_title'],
      $this->row_actions($actions)
    );
  }

  /**
   * checkbox column renders
   *
   * @param $item - row (key, value array)
   * @return HTML
   */
  function column_cb($item)
  {
    return sprintf(
      '<input type="checkbox" name="id[]" value="%s" />',
      $item['ID']
    );
  }

  /**
   * return columns to display in table
   * like content, or description
   *
   * @return array
   */
  function get_columns()
  {
    $columns = array(
      'cb'            => '<input type="checkbox" />', //Render a checkbox instead of text
      'post_title'    => __('Title', 'front-end-bundler'),
      'meta_value'    => __('Shortcode','front-end-bundler'),
      'post_date'     => __('Date', 'front-end-bundler'),
      'post_status'   => __('Status','front-end-bundler'),
    );
    return $columns;
  }

  /**
   * return columns that may be used to sort table
   * all strings in array - is column names
   *
   * @return array
   */
  function get_sortable_columns()
  {
    $sortable_columns = array(
      'post_title'    => array('title', true),
      'post_date'     => array('post_date', false),
      'post_status'   => array('post_status', false),
      'meta_value'    => array('meta_value', false),
    );
    return $sortable_columns;
  }

  /**
   * bult actions if has any
   *
   * @return array
   */
  function get_bulk_actions()
  {
    $actions = array(
      'delete' => 'Delete'
    );
    return $actions;
  }

  /**
   * processes bulk actions
   * message about successful deletion will be shown on page in next part
   */
  function process_bulk_action()
  {
    global $wpdb;

    if ('delete' === $this->current_action()) {
      $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();

      if( is_array($ids) ){
        foreach ($ids as $id) {

          $coupon_ids = $wpdb->get_results(
            $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta where meta_key = %s AND meta_value = %s ", 'front_end_bundler_delete' , $id )
          );

          if( count($coupon_ids) > 0 ){
            foreach ($coupon_ids as $coupon_id) {
              $wpdb->query("DELETE FROM $wpdb->posts WHERE id IN($coupon_id->post_id)");
            }
          }
        }
      } else {
        $coupon_ids = $wpdb->get_results(
          $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta where meta_key = %s AND meta_value = %s ", 'front_end_bundler_delete' , $ids )
        );

        if( count($coupon_ids) > 0 ){
          foreach ($coupon_ids as $coupon_id) {
            $wpdb->query("DELETE FROM $wpdb->posts WHERE id IN($coupon_id->post_id)");
          }
        }
      }

      if (is_array($ids)) $ids = implode(',', $ids);
      if (!empty($ids)) {
          $wpdb->query("DELETE FROM $wpdb->posts WHERE id IN($ids)");
      }
    }
  }

  /**
   * important method
   *
   * It will get rows from database and prepare them to be showed in table
   */
  function prepare_items()
  {
    global $wpdb;

    $per_page   = 10; // constant, how much records will be shown per page
    $columns    = $this->get_columns();
    $hidden     = array(); 
    $sortable   = $this->get_sortable_columns();
    
    // here we configure table headers, defined in our methods
    $this->_column_headers = array($columns, $hidden, $sortable);

    // process bulk action if any
    $this->process_bulk_action();

    // will be used in pagination settings
    $total_items = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE `post_type` = 'sip-bundles' AND (`post_status`='publish' OR `post_status`='draft') ");

    $paged      = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
    $orderby    = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'post_title';
    $order      = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

    $paged=$per_page*$paged;
    $querystr = "
        SELECT $wpdb->posts.ID, $wpdb->posts.post_title, $wpdb->posts.post_date, $wpdb->posts.post_status, $wpdb->postmeta.meta_value 
        FROM $wpdb->posts, $wpdb->postmeta
        WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
        AND $wpdb->postmeta.meta_key = 'front_end_bundler_shortcode' 
        AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'draft') 
        AND $wpdb->posts.post_type = 'sip-bundles'
        ORDER BY $orderby $order
        LIMIT $per_page OFFSET $paged"
     ;

      $pageposts = $wpdb->get_results($wpdb->prepare($querystr,''), ARRAY_A);
      $this->items = $pageposts;

      $this->set_pagination_args(array(
          'total_items'   => $total_items, // total items defined above
          'per_page'      => $per_page, // per page constant defined at top of method
          'total_pages'   => ceil($total_items / $per_page) // calculate pages count
      ));
    }
	}

?>