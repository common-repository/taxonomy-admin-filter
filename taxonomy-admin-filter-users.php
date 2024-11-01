<?php

/**
 * Show custom profile fields
 *
 * @param \WP_User $user
 */
function taxonomy_admin_filter_show_custom_profile_fields($user)
{
  if (current_user_can('list_users'))
  {
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
    ?>
    <h3><?php _e('Taxonomy Filters Management', 'taxonomy_admin_filter') ?></h3>
    <div class="description"><?php _e('Choose hidden taxonomy terms for the user. By default, all taxonomy terms are visible in the hierarchical term taxonomies sections inside admin pages. You can choose only from max 2 nested levels but all the children of a hidden term are automatically removed from admin pages. Keep in mind that the hidden terms are not searchable and filterable. Only taxonomies with at least one term are shown below.', 'taxonomy_admin_filter') ?></div>
    <table class="form-table">
      <tr>
        <th><label for="<?php echo TAFP_META_HIDDEN_TAXONOMIES ?>"><?php _e('Hidden terms', 'taxonomy_admin_filter') ?></label></th>
        <?php
        $user_hidden_taxonomies = get_user_meta($user->ID, TAFP_META_HIDDEN_TAXONOMIES, true);
        if (empty($user_hidden_taxonomies))
          $user_hidden_taxonomies = array();

        // Loop over taxonomy_admin_filter option items
        $empty_taxonomy = true;
        if (!empty($tax_options))
        {
          foreach ($tax_options as &$taxonomy)
          {
            // If current taxonomy is enabled for replace add filter box
            if ($taxonomy->replace == 1)
            {
              ?>
              <td class="taxonomy_admin_filter_hidden_taxonomy">
              <?php
              ?>
                <div class="taxonomy_admin_filter_hidden_taxonomy"><?php _e('Taxonomy:', 'taxonomy_admin_filter') ?><br /><span class="taxonomy_admin_filter_hidden_taxonomy"><?php echo $taxonomy->slug ?></span></div>
                <ul id="taxonomy-hidden-term-<?php echo $taxonomy->slug; ?>" class="cat-checklist form-no-clear">
              <?php wp_terms_checklist(0, array('taxonomy' => $taxonomy->slug, 'selected_cats' => $user_hidden_taxonomies, 'popular_cats' => array())); ?>
                </ul>
              </td>
              <?php
              $empty_taxonomy = false;
            }
          }
        }

        // No taxonomy to filter
        if ($empty_taxonomy)
        {
          ?><td>-</td><?php
    }
    ?>
      </tr>
    </table>
    <?php
  }
}

add_action('show_user_profile', 'taxonomy_admin_filter_show_custom_profile_fields');
add_action('edit_user_profile', 'taxonomy_admin_filter_show_custom_profile_fields');

/**
 * Update custom profile fields
 *
 * @param int $user_id
 */
function taxonomy_admin_filter_update_custom_profile_fields($user_id)
{
  if (!current_user_can('edit_user', $user_id))
    return;

  // Read selected taxonomies array values
  $selected_taxonomies = $_POST[TAFP_META_HIDDEN_TAXONOMIES];

  if (current_user_can('list_users'))
  {
    // Save for admins (update every time)
    update_user_meta($user_id, TAFP_META_HIDDEN_TAXONOMIES, $selected_taxonomies);
  }
  else
  {
    // Save for other users (update only if not empty)
    if (!empty($_POST[TAFP_META_HIDDEN_TAXONOMIES]))
      update_user_meta($user_id, TAFP_META_HIDDEN_TAXONOMIES, $selected_taxonomies);
  }
}

add_action('personal_options_update', 'taxonomy_admin_filter_update_custom_profile_fields');
add_action('edit_user_profile_update', 'taxonomy_admin_filter_update_custom_profile_fields');
