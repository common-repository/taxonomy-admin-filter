<?php
// Check request data before save main settings
if (( isset($_GET['page']) && $_GET['page'] == 'taxonomy_admin_filter' ) && isset($_POST['taxonomy_admin_filter_main_action']))
{
  taxonomy_admin_filter_save_main_settings();
}

/**
 * Load internalization supports
 */
function taxonomy_admin_filter_load_textdomain()
{
  load_plugin_textdomain('taxonomy_admin_filter', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'taxonomy_admin_filter_load_textdomain');

/**
 * Add taxonomy filter settings before the admin page is rendered
 */
function taxonomy_admin_filter_admin_init()
{
  register_setting('taxonomy_admin_filter_options', TAFP_OPTIONS);
  add_settings_section('taxonomy_admin_filter_main', '', 'taxonomy_admin_filter_option_main_show', 'taxonomy_admin_filter_main_section');
}

add_action('admin_init', 'taxonomy_admin_filter_admin_init');

/**
 * Render admin settings page
 */
function taxonomy_admin_filter_settings()
{
  settings_fields('taxonomy_admin_filter_options');
  settings_fields('taxonomy_admin_filter_user_options');
  ?>
  <div class="wrap">
    <div class="icon32"><img src="<?php echo plugins_url('taxonomy-admin-filter/images/icon32.png') ?>" /></div>
    <h2><?php _e('Taxonomy admin filter settings', 'taxonomy_admin_filter') ?></h2>

    <script type="text/javascript">
      jQuery(document).ready(function ($) {
        jQuery('body').on('click', '.button-primary.reset:not(.confirm, .disabled)', function (e) {
          e.preventDefault();
          jQuery(this).addClass('confirm');
          jQuery(this).val();
          jQuery(this).val('<?php _e('Confirm reset', 'taxonomy_admin_filter') ?>');
        }).on('click', '.button-primary.taxonomy_admin_filter_main.reset.confirm:not(.disabled)', function () {
          jQuery('#form_main_action').val('taxonomy_admin_filter_reset');
          jQuery(this).val('<?php _e('Resetting..', 'taxonomy_admin_filter') ?>').addClass('disabled');
        });
  <?php
  if (isset($_GET['updated']))
  {
    ?>
        jQuery('#setting-error-settings_updated').delay(3000).slideUp(400);
  <?php } ?>
      });
    </script>

    <div id="poststuff">
      <div id="post-body" class="metabox-holder">
        <div id="post-body-content">
          <form method="post" action="">
            <p>
              <?php _e('Choose in which taxonomy admin page box show filter field. Change "hide filter" checkbox if you want to hide filter field if taxonomy has no values. You can enable a taxonomy filter by checking his checkbox input beside taxonomy names.', 'taxonomy_admin_filter') ?>
              <?php _e('When you enable a taxonomy filter, a section for choosing hidden taxonomy terms is displayed in every WordPress user profile\'s settings page. In that page you can select a list of taxonomy terms that are automatically removed from hierarchical term taxonomies inside admin pages.', 'taxonomy_admin_filter') ?><br />
              <span class="description"><?php _e('Note: The plugin does not support non-hierarchical tags.', 'taxonomy_admin_filter') ?>
            </p>
            <?php
            // Prints out all settings of taxonomy filter main settings page
            do_settings_sections('taxonomy_admin_filter_main_section');
            ?>
            <input id="form_main_action" name="taxonomy_admin_filter_main_action" type="hidden" value="taxonomy_admin_filter_main_update" /><br />
            <p>
              <input name="submit" type="submit" class="button-primary taxonomy_admin_filter_main" value="<?php _e('Save', 'taxonomy_admin_filter') ?>" />
              <input name="reset" type="submit" class="button-primary taxonomy_admin_filter_main reset" value="<?php _e('Reset', 'taxonomy_admin_filter') ?>" />
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php
}

/**
 * Render admin main settings page options and fields
 */
function taxonomy_admin_filter_main_fields()
{
  $options = get_option(TAFP_OPTIONS);
  ?>
  <h2 class="title"><?php _e('Select2 javascript URL', 'taxonomy_admin_filter') ?></h2>
  <table class="form-table taxonomy-admin-filter-table">
    <tbody>
      <tr>
        <th>
          <label><input name="select2_js" type="radio" value="0" <?php echo!$options || empty($options->select2_js) ? ' checked="checked"' : ''; ?>> <?php _e('Default', 'taxonomy_admin_filter'); ?></label>
        </th>
        <td>
          <code class="default-example" style="display: inline;"><?php echo plugins_url('taxonomy-admin-filter/js/select2.min.js'); ?></code>
        </td>
      </tr>
      <tr>
        <th>
          <label><input name="select2_js" type="radio" value="1" <?php echo $options && !empty($options->select2_js) ? 'checked="checked"' : ''; ?>> <?php _e('Custom URL', 'taxonomy_admin_filter'); ?></label></th>
        <td>
          <input name="select2_js_path" type="text" value="<?php echo $options && !empty($options->select2_css_path) ? esc_attr($options->select2_css_path) : '' ?>" class="regular-text code"> <span class="description"><?php _e('Please enter the Select2 Javascript file full URL. E.g.', 'taxonomy_admin_filter'); ?></span>
          <code class="default-example" style="display: inline;"><?php echo 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js'; ?></code>
        </td>
      </tr>
    </tbody>
  </table>
  <hr>
  <h2 class="title"><?php _e('Select2 css URL', 'taxonomy_admin_filter') ?></h2>
  <table class="form-table taxonomy-admin-filter-table">
    <tbody>
      <tr>
        <th>
          <label><input name="select2_css" type="radio" value="0"  <?php echo!$options || empty($options->select2_css) ? ' checked="checked"' : ''; ?>> <?php _e('Default', 'taxonomy_admin_filter'); ?></label>
        </th>
        <td>
          <code class="default-example" style="display: inline;"><?php echo plugins_url('taxonomy-admin-filter/css/select2.min.css'); ?></code>
        </td>
      </tr>
      <tr>
        <th>
          <label><input name="select2_css" type="radio" value="1" <?php echo $options && !empty($options->select2_css) ? 'checked="checked"' : ''; ?>> <?php _e('Custom URL', 'taxonomy_admin_filter'); ?></label></th>
        <td>
          <input name="select2_css_path" type="text" class="regular-text code" value="<?php echo $options && !empty($options->select2_css_path) ? esc_attr($options->select2_css_path) : '' ?>"> <span class="description"><?php _e('Please enter the Select2 CSS file full URL. E.g.', 'taxonomy_admin_filter'); ?></span>
          <code class="default-example" style="display: inline;"><?php echo 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css'; ?></code>
        </td>
      </tr>
    </tbody>
  </table>
  <hr>
  <table class="wp-list-table widefat fixed posts taxonomy-admin-filter-table" cellspacing="0">
    <thead>
      <tr>
        <th scope="col" class="manage-column label-column"><?php _e('Name', 'taxonomy_admin_filter') ?></th>
        <th scope="col" class="manage-column slug-column"><?php _e('Slug', 'taxonomy_admin_filter') ?></th>
        <th scope="col" class="manage-column slug-column"><?php _e('Rewrite slug', 'taxonomy_admin_filter') ?></th>
        <th scope="col" class="manage-column enable-column"><?php _e('Enable', 'taxonomy_admin_filter') ?></th>
        <th scope="col" class="manage-column select2-column"><?php _e('Use Select2', 'taxonomy_admin_filter') ?></th>
        <th scope="col" class="manage-column hide_blank-column"><?php _e('Hide filter if taxonomy is blank', 'taxonomy_admin_filter') ?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th scope="col" class="manage-column label-column"><?php _e('Name', 'taxonomy_admin_filter') ?></th>
        <th scope="col" class="manage-column slug-column"><?php _e('Slug', 'taxonomy_admin_filter') ?></th>
        <th scope="col" class="manage-column slug-column"><?php _e('Rewrite slug', 'taxonomy_admin_filter') ?></th>
        <th scope="col" class="manage-column enable-column"><?php _e('Enable', 'taxonomy_admin_filter') ?></th>
        <th scope="col" class="manage-column select2-column"><?php _e('Use Select2', 'taxonomy_admin_filter') ?></th>
        <th scope="col" class="manage-column hide_blank-column"><?php _e('Hide filter if taxonomy is blank', 'taxonomy_admin_filter') ?></th>
      </tr>
    </tfoot>
    <tbody id="the-list">
      <?php
      $tax_options = new stdClass;
      if ($options && is_object($options) && !empty($options->taxonomies) && is_object($options->taxonomies))
      {
        $tax_options = $options->taxonomies;
      }
      $tax_list = '';
      $i = 1;

      // Retrieve hierarchical taxonomies
      $args = array('hierarchical' => true);
      $taxonomies = get_taxonomies($args, 'objects');

      if ($taxonomies)
      {
        // Loop taxonomies
        foreach ($taxonomies as &$taxonomy)
        {
          // Retrieve current taxonomy data
          $slug = $taxonomy->rewrite['slug'];
          $name = $taxonomy->name;

          // Append taxonomy name to a variable containing taxonomy list
          $tax_list .= $name . ',';

          // Check if current taxonomy is checked
          $checked = '';
          if (!empty($tax_options->$name) && !empty($tax_options->$name->replace))
          {
            $checked = 'checked="checked"';
          }
          ?>
          <tr id="post-<?php echo $i ?>" <?php echo ($i % 2 == 0) ? 'class="alternate"' : '' ?> valign="top">
            <td class="label-column">
              <label for="<?php echo $name ?>">
                <?php
                echo $taxonomy->labels->name;
                if ($taxonomy->_builtin == 1)
                  echo ' <span class="description" style="color:#ababab">(builtin)</span>'
                  ?>
              </label>
            </td>
            <td class="slug-column"><?php echo $name ?></td>
            <td class="slug-column"><?php echo $slug ?></td>
            <td scope="row" class="enable-column">
              <?php echo '<input type="checkbox" id="' . $name . '" name="taxonomies[' . $name . ']" value="1" ' . $checked . '>' ?>
            </td>
            <td scope="row" class="select2-column">
              <input type="checkbox" name="select2_opt[<?php echo $name ?>]" value="1" <?php if (!empty($tax_options->$name) && $tax_options->$name->select2 == 1) echo 'checked="checked"'; ?> />
            </td>
            <td scope="row" class="hide_blank-column">
              <input type="checkbox" name="hide_blank_opt[<?php echo $name ?>]" value="1" <?php if (!empty($tax_options->$name) && $tax_options->$name->hide_blank == 1) echo 'checked="checked"'; ?> />
            </td>
          </tr>
          <?php
          $i++;
        }
      }
      ?>
    </tbody>
  </table>
  <input type="hidden" name="tax" value="<?php echo $tax_list ?>"/>
  <?php
}

/**
 * Show admin main settings page
 */
function taxonomy_admin_filter_option_main_show()
{
  taxonomy_admin_filter_main_fields();
}

/**
 * Save admin page main settings
 */
function taxonomy_admin_filter_save_main_settings()
{
  // Explode taxonomy list
  $taxonomies = explode(',', $_POST['tax']);
  $options = new stdClass();
  $tax_options = new stdClass();
  // Manage save actions
  if ($_POST['taxonomy_admin_filter_main_action'] != 'taxonomy_admin_filter_reset')
  {
    // Read post data
    if (isset($_POST['taxonomies']))
    {
      $taxs = $_POST['taxonomies'];
    }
    else
    {
      $taxs = array();
    }
    if (isset($_POST['hide_blank_opt']))
    {
      $hide_blank_opt = $_POST['hide_blank_opt'];
    }
    else
    {
      $hide_blank_opt = array();
    }
    if (isset($_POST['select2_opt']))
    {
      $select2_opt = $_POST['select2_opt'];
    }
    else
    {
      $select2_opt = array();
    }
    // Loop taxonomies
    foreach ($taxonomies as $taxonomy)
    {
      if (!empty($taxonomy))
      {
        // Set data (from post request)
        if ($taxs[$taxonomy] == 1)
        {
          $replace = 1;
        }
        else
        {
          $replace = 0;
        }
        if (!empty($hide_blank_opt[$taxonomy]))
        {
          $hide_blank = 1;
        }
        else
        {
          $hide_blank = 0;
        }
        if (!empty($select2_opt[$taxonomy]))
        {
          $select2 = 1;
        }
        else
        {
          $select2 = 0;
        }
        $option = new stdClass();

        // Save taxonomy slug
        $option->slug = $taxonomy;
        // Save replace value (1 = replace, 0 = WordPress default)
        $option->replace = $replace;
        // Save hide blank value (1 = hide, 0 = show)
        $option->hide_blank = $hide_blank;
        // Save hide blank value (1 = hide, 0 = show)
        $option->select2 = $select2;

        // Add current taxonomy to options class
        $tax_options->$taxonomy = $option;
      }
    }
  }
  else
  {
    // Loop taxonomies
    foreach ($taxonomies as $taxonomy)
    {
      if (!empty($taxonomy))
      {
        // Set data (from defaults)
        $replace = TAFP_DEFAULT_REPLACE;
        $hide_blank = TAFP_DEFAULT_HIDE_BLANK;
        $select2 = TAFP_DEFAULT_SELECT2;

        $option = new stdClass();

        // Save taxonomy slug
        $option->slug = $taxonomy;
        // Save replace value (1 = replace, 0 = WordPress default)
        $option->replace = $replace;
        // Save hide blank value (1 = hide, 0 = show)
        $option->hide_blank = $hide_blank;
        // Save select2 value (1 = hide, 0 = show)
        $option->select2 = $select2;

        // Add current taxonomy to options class
        $tax_options->$taxonomy = $option;
      }
    }
  }
  $select2_js = isset($_POST['select2_js']) && is_numeric($_POST['select2_js']) ? (int) $_POST['select2_js'] : 0;
  $options->select2_js = $select2_js ? 1 : 0;

  $select2_css = isset($_POST['select2_css']) && is_numeric($_POST['select2_css']) ? (int) $_POST['select2_css'] : 0;
  $options->select2_css = $select2_css ? 1 : 0;

  $options->select2_js_path = isset($_POST['select2_js_path']) && is_string($_POST['select2_js_path']) ? sanitize_text_field($_POST['select2_js_path']) : '';
  $options->select2_css_path = isset($_POST['select2_css_path']) && is_string($_POST['select2_css_path']) ? sanitize_text_field($_POST['select2_css_path']) : '';
  $options->taxonomies = $tax_options;
  // Save main options
  update_option(TAFP_OPTIONS, $options);

  // Reload admin settings page
  header("Location: options-general.php?page=" . 'taxonomy_admin_filter' . "&updated=true");
}
