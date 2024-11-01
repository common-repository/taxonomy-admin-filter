=== Taxonomy admin filter ===
Contributors: tchirvasa
Donate link: https://lisal.ro/
Tags: usability, filter, admin, category, tag, term, taxonomy, hierarchy, organize, manage
Requires at least: 3.0
Tested up to: 5.2.1
Stable tag: 1.2.3
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Fast and Simple taxonomy assignments for your admin section. Advanced filtering and visibility options, Allows progressive loading, bulk selections. Now with Gutenberg support.

== Description ==

Advanced filtering system for **hierarchical term taxonomies** with the following functionalities:

1. You can choose which taxonomies should have the filter from the plugin settings;
2. Present in inline/bulk edit;
3. Can use Select2 for progressive loading (very useful for large taxonomies);
4. Provides a way to hide certain terms from specific users, by selecting them in the user profile;
5. Advanced text-filtering system presented below:

By default the filtering is done in a non-case-sensitive, "contains strings" way. But it makes use of key letters (-!+^$=) placed before each sentence that tells the filter what to do with that specific sentence. A sentence in this filter means a bunch of words separated by the letter '+'. E.g. my+example+is.
There are three groups of letters that can be used in combination, when used in their specific order. See the screenshots for details.

Based on your input, I may be able to enhance this plugin to better suit your needs.
It was a definite pleasure developing it, and it gives me great satisfaction knowing that I helped someone somewhere. Thank you for using my plugin.

= Usage =

1. Go to `WP-Admin` -> `Settings` -> `Tax. admin filter` and enable the filter for an existing hierarchical taxonomy in the plugin settings: E.g. Post Categories by checking the box near the Taxonomy name.
2. Go to `WP-Admin` -> `Posts` -> `Add New`.
3. Find the taxonomy checkbox list on page sidebar.
4. Use the filtering input, bulk selection and visibility checkboxes.

Links: [Author's Site](http://lisal.ro)

== Installation ==

1. Unzip the downloaded `taxonomy-admin-filter` zip file
2. Upload the `taxonomy-admin-filter` folder and its contents into the `wp-content/plugins/` directory of your WordPress installation
3. Activate `taxonomy-admin-filter` from Plugins page

== Frequently Asked Questions ==

= Works on multisite? =

Of course, you just have to enable valid taxonomies on settings page for *every site*.

= Works on with custom post types? =

Yes, **it is the same** as dealing with the native post.

= Works on with custom taxonomies? =

Sure, you can enable the filter on all hierarchical taxonomies present in your wordpress site in taxonomy admin filter settings page.

= Why is this plugin better? =

Well, it definitely provides **faster filtering** and also **bulk selection**, and the speed improvement of page loading time when using Select2 is also a major contributing factor.

= When is it best used? =

It kind of depends on the amount of terms, the parent-child number of levels, the performance of your PC and the browser you are using. As an example without this plugin ~7000 terms in my taxonomy killed my IE browser and I had to reboot, and my PC is brand new, so in this case, the Select2 option saved me. If you have parent-child nesting, a standard filtering is recommended because the nesting is preserved.

== Screenshots ==

1. Plugin settings page
2. The standard filter layout
3. The Select2 layout
4. Using Select2
5. Filtering mechanism
6. Bulk edit section
7. User hidden taxonomy terms selection

== Changelog ==

= 1.0.0 - 2016-08-08 =
* First release
= 1.1.0 - 2016-10-14 =
* Changed settings table layout
* Translation-ready fix
= 1.2.0 - 2019-04-19 =
* Multilanguage support
* Gutenberg support
= 1.2.1 - 2019-04-21 =
* Small fix for incorrect total in multi-language sites. (Thanks Anton)
= 1.2.2 - 2019-06-07 =
* Fixed custom taxonomies being filled with categories on gutenberg (Thanks David)
= 1.2.3 - 2019-06-07 =
* Fixed custom taxonomies being shown on all post types on gutenberg (Thanks David)
* Probably fixed notices regarding strtolower on array on bulk actions (Thanks David)

== Upgrade Notice ==

= 1.0.0 =
This version requires PHP 5.3.3+
