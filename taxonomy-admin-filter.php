<?php
/*
Plugin Name: Taxonomy admin filter
Description: Adds an input field to filter taxonomy values on admin post pages and, for some user, hides several taxonomy terms according to admin settings
Author: Lisal Expert
Author URI: http://lisal.ro
Text Domain: taxonomy_admin_filter
Domain Path: /languages/
Version: 1.2.3
License: GPL v3

Taxonomy admin filter
Copyright (C) 2013-2016, Lisal Expert - office@lisal.ro

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Security check
if (!defined('ABSPATH'))
  die('Access denied');

// Path missing "__DIR__" constant on environment
if (!defined('__DIR__'))
{
  define('__DIR__', dirname(__FILE__));
}

/* * *************************************************
  INCLUDES
 * ************************************************* */

require_once( 'taxonomy-admin-filter-constants.php' );
require_once( 'taxonomy-admin-filter-settings.php' );
require_once( 'taxonomy-admin-filter-users.php' );
require_once( 'lib/gkd_urlify.php' );

/* * *************************************************
  PLUGIN ACTIVATION
 * ************************************************* */

/**
 * Register activation hook
 */
function taxonomy_admin_filter_activation()
{
  // Update default settings
  $options = taxonomy_admin_filter_category_default_options();
  update_option('taxonomy_admin_filter', $options);
  add_option('taxonomy_admin_filter', $options);
}

register_activation_hook(__FILE__, 'taxonomy_admin_filter_activation');

/**
 * Retrieve default category options (for standard settings)
 *
 * @return stdClass
 */
function taxonomy_admin_filter_category_default_options()
{
  // Retrieve hierarchical taxonomies
  $args = array('hierarchical' => true);
  $taxonomies = get_taxonomies($args, 'objects');
  $options = new stdClass();
  $options->select2_js = 0;
  $options->select2_js_path = '';
  $options->select2_css = 0;
  $options->select2_css_path = '';
  $options->taxonomies = new stdClass();
  // Loop taxonomies
  foreach ($taxonomies as &$taxonomy)
  {
    // Set data (from current taxonomy object)
    $tax = $taxonomy->name;
    $replace = TAFP_DEFAULT_REPLACE;
    $hide_blank = TAFP_DEFAULT_HIDE_BLANK;
    $select2 = TAFP_DEFAULT_SELECT2;
    $option = new stdClass();

    // Save taxonomy slug
    $option->slug = $tax;
    // Save replace value (1 = replace, 0 = WordPress default)
    $option->replace = $replace;
    // Save hide blank value (1 = hide, 0 = show)
    $option->hide_blank = $hide_blank;
    // Save select2 value (1 = hide, 0 = show)
    $option->select2 = $select2;

    // Add current taxonomy to options class
    $options->taxonomies->$tax = $option;
  }
  return $options;
}

/* * *************************************************
  PLUGIN DEACTIVATION
 * ************************************************* */

/**
 * Register deactivation hook
 */
function taxonomy_admin_filter_deactivation()
{
  delete_option('taxonomy_admin_filter');
}

register_deactivation_hook(__FILE__, 'taxonomy_admin_filter_deactivation');

/* * *************************************************
  PLUGIN ACTIONS
 * ************************************************* */

/**
 * Add menu settings
 */
function taxonomy_admin_filter_setting_menu()
{
  // Register stylesheet
  wp_register_style('taxonomy_admin_filter_style', plugins_url('taxonomy-admin-filter/css/tafp.css'));
  wp_enqueue_style('taxonomy_admin_filter_style');

  // Add option page
  add_options_page('Taxonomy admin filter', 'Tax. admin filter', 'manage_options', 'taxonomy_admin_filter', 'taxonomy_admin_filter_settings');
}

add_action('admin_menu', 'taxonomy_admin_filter_setting_menu');

/**
 * Retrieve plugin options per taxonomy.
 * @staticvar stdClass|false $taxonomy_admin_filter
 * @param string $taxonomy Retrieve plugin options for specified taxonomy.
 * @return stdClass|false
 */
function taxonomy_admin_filter_get_options($taxonomy = null)
{
  static $taxonomy_admin_filter = null;
  if (is_null($taxonomy_admin_filter))
  {
    $taxonomy_admin_filter = get_option(TAFP_OPTIONS);
    if (!$taxonomy_admin_filter || !is_object($taxonomy_admin_filter))
    {
      $taxonomy_admin_filter = false;
    }
  }
  if (is_null($taxonomy))
  {
    return $taxonomy_admin_filter;
  }
  if (!property_exists($taxonomy_admin_filter, 'taxonomies') || empty($taxonomy_admin_filter->taxonomies) || !is_object($taxonomy_admin_filter->taxonomies))
  {
    return false;
  }
  return property_exists($taxonomy_admin_filter->taxonomies, $taxonomy) ?
      $taxonomy_admin_filter->taxonomies->{$taxonomy} : null;
}

/**
 * Override the taxonomy item walker
 * @param array $args Walker arguments
 * @param int $post_id Post ID
 * @return array
 */
function taxonomy_admin_filter_add_count_to_meta_checkboxes($args, $post_id)
{
  $taxonomy = isset($args['taxonomy']) ? $args['taxonomy'] : null;
  if (!$taxonomy)
  {
    return;
  }
  if (isset($args['walker']) && !($args['walker'] instanceof Walker))
  {
    return $args;
  }
  $options = taxonomy_admin_filter_get_options($taxonomy);
  if ($options && $options->replace)
  {
    require_once( __DIR__ . '/override/class-walker-category-checklist-override.php' );
    $args['walker'] = new Walker_Category_Checklist_Override;
  }
  return $args;
}

/**
 * Returns the hidden user taxonomies
 * @staticvar type $user_hidden_taxonomies
 * @return array
 */
function taxonomy_admin_filter_get_hidden_tax()
{
  static $user_hidden_taxonomies = null;
  if (is_null($user_hidden_taxonomies))
  {
    $user = wp_get_current_user();
    $user_hidden_taxonomies = get_user_meta($user->ID, TAFP_META_HIDDEN_TAXONOMIES, true);
    if (empty($user_hidden_taxonomies))
    {
      $user_hidden_taxonomies = array();
    }
    else
    {
      $user_hidden_taxonomies = array_map('intval', $user_hidden_taxonomies);
      $user_hidden_taxonomies = array_unique($user_hidden_taxonomies);
    }
  }
  return $user_hidden_taxonomies;
}

/**
 *
 * @global type $wpdb
 * @global type $walker_counter
 * @param type $term_id
 * @param type $taxonomy
 * @param type $post_type
 * @return int
 */
function taxonomy_admin_filter_count_by_type($term_id, $taxonomy, $post_type, $term_ids = array())
{
  static $post_counts = array();
  $hash = md5(json_encode($term_ids));
  if (!isset($post_counts[$taxonomy . '-' . $hash]))
  {
    $post_counts[$taxonomy . '-' . $hash] = array();
  }
  if (!isset($post_counts[$taxonomy . '-' . $hash][$post_type]))
  {
    global $wpdb, $walker_counter;
    $post_counts[$taxonomy . '-' . $hash][$post_type] = array();
    $post_status = 'publish';
    
    $terms = (array) get_terms( $taxonomy, array( 'get' => 'all' ) );
    $all_terms = array();
    foreach($terms as $k=> $term){
      $all_terms[$term->term_id] = null;
      unset($terms[$k]);
    }
    $all_terms = array_keys($all_terms);
    if(empty($all_terms)){
      $all_terms = array(0);
    }

    $query = ''
        . 'SELECT tt.term_id,count(sp.ID) as total '
        . 'FROM ' . $wpdb->posts . ' sp,' . $wpdb->term_relationships . ' tr,' . $wpdb->term_taxonomy . ' tt '
        . 'WHERE sp.ID = tr.object_id AND tr.term_taxonomy_id = tt.term_taxonomy_id '
        . 'AND post_type=%s AND post_status = %s '
        . 'AND tt.taxonomy=%s AND tt.count>0'
        . ($term_ids ? ' AND tt.term_id IN (' . implode(',', $term_ids) . ') ' : ' AND tt.term_id IN (' . implode(',', $all_terms) . ')')
        . 'GROUP BY term_id'
        . '';
    $results = $wpdb->get_results($wpdb->prepare($query, $post_type, $post_status, $taxonomy));

    foreach ($results as &$result)
    {
      $post_counts[$taxonomy . '-' . $hash][$post_type][$result->term_id] = $result->total;
    }
  }
  if (isset($post_counts[$taxonomy . '-' . $hash][$post_type][$term_id]))
  {
    return $post_counts[$taxonomy . '-' . $hash][$post_type][$term_id];
  }
  return 0;
}

/**
 * Add taxonomy filter boxes to postbox
 */
global $taxonomy_admin_filter_taxonomy_objects;
global $taxonomy_admin_filter_post_type;
global $taxonomy_admin_filter_post_id;

function taxonomy_admin_filter_add_boxes()
{
  global $pagenow, $wpdb;
  global $taxonomy_admin_filter_taxonomy_objects;
  global $taxonomy_admin_filter_post_type;
  global $taxonomy_admin_filter_post_id;
  if (!in_array($pagenow, array('post.php', 'post-new.php', 'edit.php', 'profile.php')))
  {
    return;
  }
  $options = taxonomy_admin_filter_get_options();
  if (!$options)
  {
    return;
  }
  if (empty($options->taxonomies) || !is_object($options->taxonomies))
  {
    return;
  }

  $tax_options = $options->taxonomies;
  $post_type = ( isset($_REQUEST['post_type']) ) ? sanitize_key($_REQUEST['post_type']) : '';
  $taxonomy_admin_filter_post_id = $post_id = ( isset($_REQUEST['post']) && is_numeric($_REQUEST['post']) ) ? sanitize_key($_REQUEST['post']) : '';
  
  if (!$post_type && $post_id)
  {
    $post_type = get_post_type($post_id);
  }
  if (!$post_type)
  {
    $post_type = 'post';
  }
  $taxonomy_admin_filter_post_type = $post_type;
  $enabled_options = array();
  $select2 = false;
  // Loop over taxonomy_admin_filter option items
  $taxonomy_admin_filter_taxonomy_objects = array();
  foreach ($tax_options as &$taxonomy)
  {
    // If current taxonomy is enabled for replace add filter box
    if ($taxonomy->replace != 1)
    {
      continue;
    }
    if (!empty($taxonomy->select2))
    {
      $select2 = true;
    }
    if ($select2)
    {
      $hidden_tax = array();
      if ('profile.php' !== $pagenow)
      {
        $hidden_tax = taxonomy_admin_filter_get_hidden_tax();
      }
      $query = ''
          . 'SELECT count(*) as total '
          . 'FROM ' . $wpdb->term_taxonomy . ' '
          . 'WHERE taxonomy=%s';
          
      $terms = (array) get_terms( $taxonomy->slug, array( 'get' => 'all' ) );
      $all_terms = array();
      foreach($terms as $k=> $term){
        $all_terms[$term->term_id] = null;
        unset($terms[$k]);
      }
      $all_terms = array_keys($all_terms);
      if(empty($all_terms)){
        $all_terms = array(0);
      }
      $query .= ' AND term_id IN (' . implode(',', $all_terms) . ')';
      if ($hidden_tax)
      {
        $query .= ' AND term_id NOT IN(' . implode(',', $hidden_tax) . ')';
      }
      $total = $wpdb->get_results($wpdb->prepare($query, $taxonomy->slug));
      $taxonomy->total = $total[0]->total;
    }
    $tax_obj = get_taxonomy($taxonomy->slug);
    $taxonomy_admin_filter_taxonomy_objects[$taxonomy->slug] = $tax_obj;
    $taxonomy->label = $tax_obj->label;
    $taxonomy->labels = $tax_obj->labels;
    $enabled_options[$taxonomy->slug] = $taxonomy;
  }
  if (!$enabled_options)
  {
    return;
  }
  if ($select2)
  {
    if (empty($options->select2_js) || empty($options->select2_js_path))
    {
      $select2_js_url = plugins_url('taxonomy-admin-filter/js/select2.min.js');
    }
    else
    {
      $select2_js_url = $options->select2_js_path;
    }
    if (empty($options->select2_css) || empty($options->select2_css_path))
    {
      $select2_css_url = plugins_url('taxonomy-admin-filter/css/select2.min.css');
    }
    else
    {
      $select2_css_url = $options->select2_css_path;
    }
    wp_enqueue_script('taxonomy_admin_filter_select2_script', $select2_js_url, array('jquery'), null, true);
    wp_register_style('taxonomy_admin_filter_select2_style', $select2_css_url);
    wp_enqueue_style('taxonomy_admin_filter_select2_style');
  }
  add_filter('wp_terms_checklist_args', 'taxonomy_admin_filter_add_count_to_meta_checkboxes', 99, 2);
  $js_options = array(
      'lang' => array(
          'show_selected' => __('Show only the selected items', 'taxonomy_admin_filter'),
          'show_selected_label' => __('Only selected', 'taxonomy_admin_filter'),
          'show_unallocated' => __('Show only the items that are not assigned to the current post-type (0)', 'taxonomy_admin_filter'),
          'show_unallocated_label' => __('Hide allocated', 'taxonomy_admin_filter'),
          'hide_empty' => __('Hide items with no items assigned from other post-types [0]', 'taxonomy_admin_filter'),
          'hide_empty_label' => __('Hide empty', 'taxonomy_admin_filter'),
          'select_all' => __('Select all visible items', 'taxonomy_admin_filter'),
          'select_all_label' => __('Select all', 'taxonomy_admin_filter'),
          'deselect_all' => __('Deselect all visible items', 'taxonomy_admin_filter'),
          'deselect_all_label' => __('Deselect all', 'taxonomy_admin_filter'),
          'total_items' => __('Total: ', 'taxonomy_admin_filter'),
          'total_results' => __('Visible: ', 'taxonomy_admin_filter'),
          'total_selected' => __('Selected: ', 'taxonomy_admin_filter'),
          'placeholder' => __('Filter', 'taxonomy_admin_filter'),
          'item_count_tax' => __('Assigned to current post type', 'taxonomy_admin_filter'),
          'item_count' => __('Assigned to other post types', 'taxonomy_admin_filter'),
      ),
      'select2' => $select2,
      'post_type' => $post_type,
      'admin_ajax_url' => esc_url(admin_url('admin-ajax.php')),
      'taxonomies' => $enabled_options
  );
  if ('post.php' == $pagenow || 'post-new.php' == $pagenow)
  {
    add_action( 'enqueue_block_editor_assets', 'taxonomy_admin_filter_add_metas');
    
    $post_id = ( ('post.php' == $pagenow) && isset($_REQUEST['post']) && is_numeric($_REQUEST['post']) ) ? (int) sanitize_key($_REQUEST['post']) : '';
    wp_enqueue_script('taxonomy_admin_filter_footer_script', plugins_url('taxonomy-admin-filter/js/taxonomy-admin-filter.js'), array('jquery'), null, true);
    $js_options['selector'] = '#taxonomy-' . implode(', #taxonomy-', array_keys($enabled_options));
    $js_options['post_id'] = $post_id;
  }
  elseif ('edit.php' == $pagenow)
  {
    wp_enqueue_script('taxonomy_admin_filter_footer_script', plugins_url('taxonomy-admin-filter/js/taxonomy-admin-filter-bulk.js'), array('jquery'), null, true);
    $js_options['selector'] = '.cat-checklist.' . implode('-checklist, .cat-checklist.', array_keys($enabled_options)) . '-checklist';
  }
  elseif ('profile.php' == $pagenow)
  {
    wp_enqueue_script('taxonomy_admin_filter_footer_script', plugins_url('taxonomy-admin-filter/js/taxonomy-admin-filter-profile.js'), array('jquery'), null, true);
    $js_options['selector'] = '#taxonomy-hidden-term-' . implode(', #taxonomy-hidden-term-', array_keys($enabled_options));
    $js_options['profile_name'] = TAFP_META_HIDDEN_TAXONOMIES;
  }
  wp_register_style('taxonomy_admin_filter_admin_style', plugins_url('taxonomy-admin-filter/css/taxonomy-admin-filter.css'));
  wp_enqueue_style('taxonomy_admin_filter_admin_style');
  wp_localize_script('taxonomy_admin_filter_footer_script', 'taxonomy_admin_filter_options', $js_options);
}

add_action('admin_menu', 'taxonomy_admin_filter_add_boxes');

function taxonomy_admin_filter_add_metas() {
  global $taxonomy_admin_filter_taxonomy_objects;
  global $taxonomy_admin_filter_post_type;
  global $taxonomy_admin_filter_post_id;
  
  foreach($taxonomy_admin_filter_taxonomy_objects as $slug => $tax_obj){
    if(!in_array($taxonomy_admin_filter_post_type, $tax_obj->object_type)){
      continue;
    }
    add_meta_box( 'taxonomy_admin_filter_' . $slug . '_post_meta', $tax_obj->label, 'post_categories_meta_box', $taxonomy_admin_filter_post_type, 'side', 'high', array('taxonomy' => $slug));
  }
}

add_action('wp_ajax_taxonomy_admin_filter', 'taxonomy_admin_filter_ajax');
function taxonomy_admin_filter_ajax()
{
  global $wpdb; // this is how you get access to the database

  $post_type = ( isset($_POST['post_type']) ) ? sanitize_key($_POST['post_type']) : '';
  $taxonomy = ( isset($_POST['taxonomy']) ) ? sanitize_key($_POST['taxonomy']) : '';
  
  $terms = (array) get_terms( $taxonomy, array( 'get' => 'all' ) );
  $all_terms = array();
  foreach($terms as $k=> $term){
    $all_terms[$term->term_id] = null;
    unset($terms[$k]);
  }
  $all_terms = array_keys($all_terms);
  if(empty($all_terms)){
    $all_terms = array(0);
  }
  
  $post_id = ( isset($_POST['post_id']) ) ? (int) sanitize_key($_POST['post_id']) : '';
  $search = ( isset($_POST['query']) ) ? sanitize_text_field($_POST['query']) : '';
  $page = ( isset($_POST['page']) ) ? (int) sanitize_key($_POST['page']) : '';
  $ignore_hidden = ( isset($_POST['ignore_hidden']) ) ? (int) sanitize_key($_POST['ignore_hidden']) : '';
  $terms = ( isset($_POST['terms']) ) ? $_POST['terms'] : array();

  $page = $page <= 0 ? 1 : $page;
  $post_status = 'publish';
  $limit = 50;
  $offset = $limit * ($page - 1);
  $hidden_tax = array();
  if (!$ignore_hidden)
  {
    $hidden_tax = taxonomy_admin_filter_get_hidden_tax();
  }
  $from_where = $wpdb->prepare(
      ' FROM ' . $wpdb->term_taxonomy . ' tt, ' . $wpdb->terms . ' t'
      . ' WHERE tt.term_id=t.term_id'
      . ' AND tt.taxonomy=%s AND t.term_id IN (' . implode(',', $all_terms) . ')'
      , $taxonomy);
  if (trim($search))
  {
    $sentences = explode(' ', $search);

    foreach ($sentences as $sentence)
    {
      if (!$sentence)
      {
        continue;
      }
      $sentence = str_replace('+', ' ', $sentence);

      $operator = '';
      $not = false;
      $case_sensitive = false;

      // negate the statement
      if (strpos($sentence, '-') === 0)
      {
        $not = true;
        $sentence = substr($sentence, 1);
      }
      // case sensitive
      if (strpos($sentence, '!') === 0)
      {
        $case_sensitive = true;
        $sentence = substr($sentence, 1);
      }

      if (!$sentence)
      {
        continue;
      }
      // ends with
      if (strpos($sentence, '$') === 0)
      {
        $sentence = substr($sentence, 1);
        if (!$sentence)
        {
          continue;
        }
        $from_where .= $wpdb->prepare(' AND ' . ($case_sensitive ? 'BINARY ' : '') . 't.name ' . ($not ? 'NOT ' : '') . 'LIKE %s', '%' . $wpdb->esc_like($sentence, '%') . '');
      }
      // begins with
      elseif (strpos($sentence, '^') === 0)
      {
        $sentence = substr($sentence, 1);
        if (!$sentence)
        {
          continue;
        }
        $from_where .= $wpdb->prepare(' AND ' . ($case_sensitive ? 'BINARY ' : '') . 't.name ' . ($not ? 'NOT ' : '') . 'LIKE %s', '' . $wpdb->esc_like($sentence, '%') . '%');
      }
      // contains word
      elseif (strpos($sentence, '=') === 0)
      {
        $sentence = substr($sentence, 1);
        if (!$sentence)
        {
          continue;
        }
        $from_where .= $wpdb->prepare(' AND ('
            . 't.name' . ($case_sensitive ? ' BINARY' : '') . ' ' . ($not ? '!=' : '=') . ' %s'
            . ($not ? 'AND' : 'OR') . ' ' . ($case_sensitive ? 'BINARY ' : '') . 't.name ' . ($not ? 'NOT ' : '') . 'LIKE %s'
            . ($not ? 'AND' : 'OR') . ' ' . ($case_sensitive ? 'BINARY ' : '') . 't.name ' . ($not ? 'NOT ' : '') . 'LIKE %s'
            . ($not ? 'AND' : 'OR') . ' ' . ($case_sensitive ? 'BINARY ' : '') . 't.name ' . ($not ? 'NOT ' : '') . 'LIKE %s'
            . ')', '' . $wpdb->esc_like($sentence, '%') . '', '' . $wpdb->esc_like($sentence, '%') . ' %', '% ' . $wpdb->esc_like($sentence, '%') . '', '% ' . $wpdb->esc_like($sentence, '%') . ' %'
        );
      }
      else
      {
        $from_where .= $wpdb->prepare(' AND ' . ($case_sensitive ? 'BINARY ' : '') . 't.name ' . ($not ? 'NOT ' : '') . 'LIKE %s', '%' . $wpdb->esc_like($sentence, '%') . '%');
      }
    }
  }
  if ($hidden_tax)
  {
    $from_where .= ' AND tt.term_id NOT IN(' . implode(',', $hidden_tax) . ')';
  }
  if ($terms)
  {
    $terms = array_map('intval', $terms);
    $terms = array_unique($terms);
    $from_where .= ' AND tt.term_id IN(' . implode(',', $terms) . ')';
  }
  $count_query = ''
      . 'SELECT COUNT(*) as total'
      . $from_where
      . '';
  $total_results = $wpdb->get_results($count_query);
  $total = 0;
  if ($total_results)
  {
    $total = $total_results[0]->total;
  }
  $results = array();
  if ($total)
  {
    $query = ''
        . 'SELECT t.term_id as id, CONCAT(IF(tt.parent>0," > ",""),t.name) as text, tt.count'
        . $from_where
        . ' ORDER BY IF(tt.parent=0,tt.term_id,tt.parent+0.5) ASC,tt.parent!=0, t.name ASC'
        . ' LIMIT ' . $offset . ', ' . $limit;
    $results = $wpdb->get_results($query, OBJECT_K);
    $term_ids = array_keys($results);
    foreach ($results as &$result)
    {
      $result->text = apply_filters('the_category', $result->text);
      $count = taxonomy_admin_filter_count_by_type($result->id, $taxonomy, $post_type, $term_ids);
      $result->count_tax = $count ? $count : 0;
    }
  }
  $response = new stdClass;
  $response->results = array_values($results);
  $response->total = $total;
  $response->page = $page;
  $response->more = $page * $limit < $total;

  echo json_encode($response);
  wp_die(); // this is required to terminate immediately and return a proper response
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'taxonomy_admin_filter__action_links');

function taxonomy_admin_filter__action_links($links)
{
  $links[] = '<a href="' . esc_url(get_admin_url(null, 'options-general.php?page=taxonomy_admin_filter')) . '">' . __('Settings', 'taxonomy_admin_filter') . '</a>';
  return $links;
}

add_action('wp_ajax_taxonomy-admin-filter-get-post-taxonomies', 'taxonomy_admin_filter_get_post_taxonomies_ajax');

function taxonomy_admin_filter_get_post_taxonomies_ajax(){
  $post_type = ( isset($_POST['post_type']) ) ? sanitize_key($_POST['post_type']) : '';
  $post_id = ( isset($_POST['post_id']) ) ? (int) sanitize_key($_POST['post_id']) : '';
  $post_type_obj = get_post_type_object( $post_type );
  $rest_base = ! empty( $post_type_obj->rest_base ) ? $post_type_obj->rest_base : $post_type_obj->name;
  $taxonomy = ( isset($_POST['taxonomy']) ) ? sanitize_key($_POST['taxonomy']) : '';
  $select2 = ( isset($_POST['select2']) ) ? (int) sanitize_key($_POST['select2']) : '';
  $taxonomies = ( isset($_POST['taxonomies']) ) ? $_POST['taxonomies'] : array();
  add_filter('wp_terms_checklist_args', 'taxonomy_admin_filter_add_count_to_meta_checkboxes', 99, 2);
  
  foreach($taxonomies as $taxonomy => $options){
    $tax = get_taxonomy( $taxonomy );
  ?>
  <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv" data-rest_base="<?php echo $tax->rest_base; ?>" data-product_rest_base="<?php echo $rest_base; ?>">
    <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
      <ul id="<?php echo $taxonomy; ?>checklist">
      <?php wp_terms_checklist( $post_id, array( 'taxonomy' => $taxonomy ) ); ?>
      </ul>
    </div>
  </div>
    <?php
  } 
  
  wp_die(); // this is required to terminate immediately and return a proper response
}

function taxonomy_admin_filter_unregister_taxonomy($taxonomy){
  static $taxonomy_admin_filter_unregister_taxonomy = null;
  if(!isset($taxonomy_admin_filter_unregister_taxonomy)){
    $taxonomy_admin_filter_unregister_taxonomy = array();
    $options = taxonomy_admin_filter_get_options();
    if (!$options){
      return;
    }
    if (empty($options->taxonomies) || !is_object($options->taxonomies)){
      return;
    }
    foreach ($options->taxonomies as &$taxonomy){
      // If current taxonomy is enabled for replace add filter box
      if ($taxonomy->replace != 1){
        continue;
      }
      $taxonomy_admin_filter_unregister_taxonomy[] = $taxonomy->slug;
    }
  }
  if(!in_array($taxonomy, $taxonomy_admin_filter_unregister_taxonomy)){
    return;
  }
  global $wp_taxonomies;
  $taxonomy_object = get_taxonomy( $taxonomy );
  $taxonomy_object->remove_rewrite_rules();
  $taxonomy_object->remove_hooks();
  unset( $wp_taxonomies[ $taxonomy ] );
}
if(strpos($_SERVER['REQUEST_URI'],'wp-json/wp/v2/taxonomies') !== false){
  add_action( 'registered_taxonomy', 'taxonomy_admin_filter_unregister_taxonomy' );
}