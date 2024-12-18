<?php
// Uninstall the plugin and clean up any stored options
if (!defined('ABSPATH')) {
    exit;
}

delete_option('compare_button_options_group');
