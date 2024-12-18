<?php

namespace PCIW;

class FrontendDisplay {
    public static function init() {
        add_shortcode('compare_products', [__CLASS__, 'display_compare_table']);
        add_action('woocommerce_after_shop_loop_item', [__CLASS__, 'add_dynamic_compare_button'], 30);
        add_action('woocommerce_single_product_summary', [__CLASS__, 'add_dynamic_compare_button'], 50);
        add_action('wp_footer', [__CLASS__, 'display_compare_count']);
    }

    public static function display_compare_table() {
        // Start the session if not already started
        if (!session_id()) {
            session_start();
        }

        // Ensure the compare_products session variable exists
        if (!isset($_SESSION['compare_products'])) {
            $_SESSION['compare_products'] = [];
        }

        $product_ids = $_SESSION['compare_products'];

        ob_start();
        // Start the table container
        echo '<div class="compare-products-table-container" style="overflow-x: auto;">';
        echo '<table class="compare-products-table" style="width: 100%; border-collapse: collapse; text-align: left;">';

        // Table Header
        echo '<thead>';
        echo '<tr><th>Attribute</th>';
        for ($i = 0; $i < 3; $i++) {
            if (isset($product_ids[$i])) {
                $product = wc_get_product($product_ids[$i]);
                echo '<th>' . esc_html($product->get_name()) . '</th>';
            } else {
                echo '<th><div class="compare_search_wrapper">';
                // Search Input
                echo '<input type="text" class="product-search" data-slot="' . $i . '" placeholder="Search Product..." />';
                // Search Results Container (Initially hidden)
                echo '<div class="search-results" data-slot="' . $i . '" style="display: none;"></div>';
                echo '</div></th>';
            }
        }
        echo '</tr>';
        echo '</thead>';

        // Table Body: Product Attributes
        echo '<tbody>';

        // Product Image
        echo '<tr><td>Image</td>';
        for ($i = 0; $i < 3; $i++) {
            if (isset($product_ids[$i])) {
                $product = wc_get_product($product_ids[$i]);
                $product_url = get_permalink($product_ids[$i]);
                echo '<td><a href="' . esc_url($product_url) . '">' . $product->get_image('thumbnail') . '</a></td>';
            } else {
                echo '<td>N/A</td>';
            }
        }
        echo '</tr>';

        // Product Category
        echo '<tr><td>Category</td>';
        for ($i = 0; $i < 3; $i++) {
            if (isset($product_ids[$i])) {
                $product = wc_get_product($product_ids[$i]);
                $categories = wp_strip_all_tags(wc_get_product_category_list($product_ids[$i]));
                echo '<td>' . esc_html($categories) . '</td>';
            } else {
                echo '<td>N/A</td>';
            }
        }
        echo '</tr>';

        // Fetch all global attributes
        $all_attributes = wc_get_attribute_taxonomies();

        foreach ($all_attributes as $attribute) {
            $attribute_name = 'pa_' . $attribute->attribute_name; // WooCommerce prepends "pa_" to custom attributes.
            $attribute_label = esc_html($attribute->attribute_label);

            echo '<tr><td>' . $attribute_label . '</td>';
            for ($i = 0; $i < 3; $i++) {
                if (isset($product_ids[$i])) {
                    $terms = get_the_terms($product_ids[$i], $attribute_name);
                    $terms_list = $terms ? implode(', ', wp_list_pluck($terms, 'name')) : 'N/A';
                    echo '<td>' . esc_html($terms_list) . '</td>';
                } else {
                    echo '<td>N/A</td>';
                }
            }
            echo '</tr>';
        }

        // Action Buttons
        echo '<tr><td>Actions</td>';
        for ($i = 0; $i < 3; $i++) {
            if (isset($product_ids[$i])) {
                echo '<td><button class="remove-from-compare" data-product-id="' . esc_attr($product_ids[$i]) . '">Remove</button></td>';
            } else {
                echo '<td>N/A</td>';
            }
        }
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';

        // Clear Compare List Button
        echo '<button class="clear-compare-list" style="margin-top: 20px;">Clear Compare List</button>';
        echo '</div>';

        // Return the buffered output
        return ob_get_clean();
    }

    public static function add_dynamic_compare_button() {
        $product_id = get_the_ID();
        $compare_products = $_SESSION['compare_products'] ?? [];

        if (in_array($product_id, $compare_products)) {
            echo '<button class="browse-compare" data-product-id="' . $product_id . '">Browse Compare</button>';
        } else {
            echo '<button class="add-to-compare" data-product-id="' . $product_id . '">Add to Compare</button>';
        }
    }

    public static function display_compare_count() {
        $compare_count = isset($_SESSION['compare_products']) ? count($_SESSION['compare_products']) : 0;

        if ($compare_count > 0) {
            echo '<div class="sticky-compare-counter">';
            echo '<a href="' . site_url('/compare/') . '">Compare (' . $compare_count . ')</a>';
            echo '</div>';
        }
    }
}
