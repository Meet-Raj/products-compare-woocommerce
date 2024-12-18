<?php
namespace PCIW;

class ScriptsHandler {
    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
    }

    public static function enqueue_scripts() {
        wp_enqueue_style('toastr-css', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css', [], null);
        wp_enqueue_script('toastr-js', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js', ['jquery'], null, true);
        wp_enqueue_script(
            'compare-script',
            PCIW_PLUGIN_URL . 'assets/js/woo_compare.js',
            ['jquery'],
            filemtime(PCIW_PLUGIN_PATH . 'assets/js/woo_compare.js'),
            true
        );
        wp_localize_script('compare-script', 'compare_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'compare_page_url' => site_url('/compare/'),
        ]);
    }
}
