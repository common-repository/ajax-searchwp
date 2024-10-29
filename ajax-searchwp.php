<?php
/**
 * Plugin Name: Ajax SearchWP
 * Description: Ajax search results based on custom post types. Use the shortcode [ajax_searchwp] to display the search form with live search results.
 * Version: 1.2.0
 * Author: Naveen Gaur
 * Author URI: https://techwithnavi.com/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ajax-searchwp
 *
 * Ajax SearchWP provides live search results as users type, allowing searches across various content types like posts, pages, custom post types, WooCommerce products, and custom fields.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define constants for the plugin path and URL
define('AJAX_SEARCHWP_PATH', plugin_dir_path(__FILE__));
define('AJAX_SEARCHWP_URL', plugin_dir_url(__FILE__));

// Require the necessary files
require_once AJAX_SEARCHWP_PATH . 'includes/class-ajax-searchwp.php';
require_once AJAX_SEARCHWP_PATH . 'includes/class-ajax-searchwp-admin.php';

// Initialize the plugin classes
function ajax_searchwp_init() {
    new Ajax_SearchWP();
    new Ajax_SearchWP_Admin();
}
add_action('plugins_loaded', 'ajax_searchwp_init');