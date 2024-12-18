<?php

namespace PCIW;

class AdminMenu {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'register_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    public static function register_menu() {
        add_menu_page(
            __('Compare Products Settings', 'products-compare-in-woocommerce'),
            __('Compare Products', 'products-compare-in-woocommerce'),
            'manage_options',
            'compare-products-settings',
            [__CLASS__, 'settings_page'],
            'dashicons-products',
            30
        );
    }

    public static function settings_page() {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'button-settings';
        include PCIW_PLUGIN_PATH . 'templates/settings-page.php';
    }

    public static function register_settings() {
        // Register your settings here as before
        register_setting('compare_button_options_group', 'add_to_compare_button_text');
        register_setting('compare_button_options_group', 'browse_compare_button_text');
        register_setting('compare_button_options_group', 'disable_button_single_page', ['default' => '0']);
        register_setting('compare_button_options_group', 'enable_button_shop_page', ['default' => '0']);
    }
}
