<?php
/**
 * Plugin Name: Products Compare In WooCommerce
 * Plugin URI: https://meetrajfreelancer.wordpress.com
 * Description: A WooCommerce plugin to compare products with advanced features user side.
 * Version: 1.0
 * Author: Meet Raj
 * Author URI: https://meetrajfreelancer.wordpress.com
 * Text Domain: products-compare-in-woocommerce
 * License: GPL2
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('PCIW_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PCIW_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once PCIW_PLUGIN_PATH . 'includes/class-admin-menu.php';
require_once PCIW_PLUGIN_PATH . 'includes/class-compare-handler.php';
require_once PCIW_PLUGIN_PATH . 'includes/class-frontend-display.php';
require_once PCIW_PLUGIN_PATH . 'includes/class-scripts-handler.php'; 
require_once PCIW_PLUGIN_PATH . 'includes/helpers.php';

// Initialize the plugin
function pciw_init() {
    \PCIW\AdminMenu::init();
    \PCIW\CompareHandler::init();
    \PCIW\FrontendDisplay::init();
    \PCIW\ScriptsHandler::init();
}
add_action('plugins_loaded', 'pciw_init');

register_activation_hook(__FILE__, 'pciw_create_compare_page');

function pciw_create_compare_page() {
    $page_check = get_page_by_title('Compare Products');
    if (!$page_check) {
        $page_data = array(
            'post_title'   => 'Compare Products',  
            'post_content' => '[compare_products]',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        );
        wp_insert_post($page_data);
    }
}
