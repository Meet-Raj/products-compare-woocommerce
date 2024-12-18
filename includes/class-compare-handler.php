<?php

namespace PCIW;

class CompareHandler {
    public static function init() {
        add_action('init', [__CLASS__, 'start_session']);
        add_action('wp_ajax_add_to_compare', [__CLASS__, 'add_to_compare']);
        add_action('wp_ajax_nopriv_add_to_compare', [__CLASS__, 'add_to_compare']);
        add_action('wp_ajax_remove_from_compare', [__CLASS__, 'remove_from_compare']);
        add_action('wp_ajax_nopriv_remove_from_compare', [__CLASS__, 'remove_from_compare']);
        add_action('wp_ajax_clear_compare_list', [__CLASS__, 'clear_compare_list']);
        add_action('wp_ajax_nopriv_clear_compare_list', [__CLASS__, 'clear_compare_list']);
        add_action('wp_ajax_search_product', [__CLASS__, 'search_product']);
        add_action('wp_ajax_nopriv_search_product', [__CLASS__, 'search_product']);
    }

    public static function start_session() {
        if (!session_id()) {
            session_start();
        }
        if (!isset($_SESSION['compare_products'])) {
            $_SESSION['compare_products'] = [];
        }
    }

    public static function add_to_compare() {
        $product_id = intval($_POST['product_id']);
        $compare_products = $_SESSION['compare_products'] ?? [];

        if (!in_array($product_id, $compare_products)) {
            if (count($compare_products) < 3) {
                $compare_products[] = $product_id;
                $_SESSION['compare_products'] = $compare_products;
                wp_send_json_success(['message' => 'Product added to compare list.']);
            } else {
                wp_send_json_error(['message' => 'You can only compare up to 3 products.']);
            }
        } else {
            wp_send_json_error(['message' => 'Product already in compare list.']);
        }
    }

    public static function remove_from_compare() {
        $product_id = intval($_POST['product_id']);
        $compare_products = $_SESSION['compare_products'] ?? [];

        if (($key = array_search($product_id, $compare_products)) !== false) {
            unset($compare_products[$key]);
            $_SESSION['compare_products'] = array_values($compare_products);
            wp_send_json_success(['message' => 'Product removed from compare list.']);
        } else {
            wp_send_json_error(['message' => 'Product not found in compare list.']);
        }
    }

    public static function clear_compare_list() {
        $_SESSION['compare_products'] = [];
        wp_send_json_success(['redirect_url' => home_url('/product-category/mattress')]);
    }

    public static function search_product() {
        if (isset($_GET['query'])) {
            $query = sanitize_text_field($_GET['query']);
            $args = [
                'post_type' => 'product',
                'posts_per_page' => 5,
                's' => $query,
            ];
            $query_result = new \WP_Query($args);

            if ($query_result->have_posts()) {
                $products = [];
                while ($query_result->have_posts()) {
                    $query_result->the_post();
                    $product = wc_get_product(get_the_ID());
                    $products[] = [
                        'id' => $product->get_id(),
                        'name' => $product->get_name(),
                        'url' => get_permalink(),
                        'image' => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'),
                    ];
                }
                wp_send_json_success(['products' => $products]);
            } else {
                wp_send_json_error(['message' => 'No products found.']);
            }
        }
    }
}
