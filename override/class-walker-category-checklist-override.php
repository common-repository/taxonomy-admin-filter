<?php

class Walker_Category_Checklist_Override extends Walker_Category_Checklist
{

  protected $no_end = false;

  public function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0)
  {
    global $pagenow;
    $is_bulk = false;
    if ('edit.php' == $pagenow)
    {
      $is_bulk = true;
    }
    $hidden_tax = taxonomy_admin_filter_get_hidden_tax();
    $post_type = get_post_type();

    if (empty($args['taxonomy']))
    {
      $taxonomy = 'category';
    }
    else
    {
      $taxonomy = $args['taxonomy'];
    }
    $options = taxonomy_admin_filter_get_options($taxonomy);
    $select2 = false;
    if ($options->select2)
    {
      $select2 = true;
    }
    if ('profile.php' == $pagenow)
    {
      $name = TAFP_META_HIDDEN_TAXONOMIES;
    }
    else
    {
      if ($taxonomy == 'category')
      {
        $name = 'post_category';
      }
      else
      {
        $name = 'tax_input[' . $taxonomy . ']';
      }
    }

    $args['popular_cats'] = empty($args['popular_cats']) ? array() : $args['popular_cats'];
    $is_popular = in_array($category->term_id, $args['popular_cats']);
    $class = $is_popular ? 'popular-category' : '';
    $class .= in_array($category->term_id, $hidden_tax) ? ' hiddentax' : '';
    $class_str = !empty($class) ? ' class="' . $class . '"' : '';

    $args['selected_cats'] = empty($args['selected_cats']) ? array() : $args['selected_cats'];

    if (!empty($args['list_only']))
    {
      $aria_cheched = 'false';
      $inner_class = 'category';

      if (in_array($category->term_id, $args['selected_cats']))
      {
        $inner_class .= ' selected';
        $aria_cheched = 'true';
      }

      /** This filter is documented in wp-includes/category-template.php */
      $output .= "\n" . '<li' . $class_str . '>' .
          '<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
          ' tabindex="0" role="checkbox" aria-checked="' . $aria_cheched . '">' .
          esc_html(apply_filters('the_category', $category->name)) . '</div>';
    }
    else
    {
      $selected = in_array($category->term_id, $args['selected_cats']);

      if ($select2 && !$selected && ($is_bulk || (!$is_bulk && !$is_popular)))
      {
        $this->no_end = true;
        return;
      }
      $term_ids = array();
      if ($select2)
      {
        $term_ids = $args['selected_cats'];
      }
      if (!$is_bulk)
      {
        $term_ids = array_merge($term_ids, $args['popular_cats']);
      }
      $count = taxonomy_admin_filter_count_by_type($category->term_id, $taxonomy, $post_type, $term_ids);
      $category_name = esc_html(apply_filters('the_category', $category->name));
      $safe_category_name = TaxonomyAdminFilterURLify::downcode($category_name, 'en');
      $normal_category_name = addslashes($safe_category_name);
      $lower_category_name = strtolower($normal_category_name);
      $category_name = $category_name
          . '<span title="' . esc_html(__('Assigned to current post type', 'taxonomy_admin_filter') . ' (' . $post_type . ')') . '" class="category_post_type_count">(' . $count . ')</span>' . '<span title="' . esc_html(__('Assigned to other post types', 'taxonomy_admin_filter')) . '" class="category_count">[' . ($category->count - $count) . ']</span>';
      /** This filter is documented in wp-includes/category-template.php */
      $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class_str data-contentlc='" . $lower_category_name . "' data-content='" . $normal_category_name . "' data-cnt='" . ($category->count - $count) . "' data-count='" . $count . "' data-selected='" . ((int) $selected) . "'>" .
          '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="' . $name . '[]" id="in-' . $taxonomy . '-' . $category->term_id . '"' .
          checked($selected, true, false) .
          disabled(empty($args['disabled']), false, false) . ' /> ' .
          $category_name . '</label>';
    }
  }

  public function end_el(&$output, $category, $depth = 0, $args = array())
  {
    if ($this->no_end)
    {
      return;
    }
    parent::end_el($output, $category, $depth, $args);
  }

  public function start_lvl(&$output, $depth = 0, $args = array())
  {
    if ($this->no_end)
    {
      return;
    }
    parent::start_lvl($output, $depth, $args);
  }

  public function end_lvl(&$output, $depth = 0, $args = array())
  {
    if ($this->no_end)
    {
      return;
    }
    parent::end_lvl($output, $depth, $args);
  }

}
