<div class="wrap">
    <h1><?php esc_html_e('Compare Button Settings', 'products-compare-in-woocommerce'); ?></h1>

    <h2 class="nav-tab-wrapper">
        <a href="?page=compare-products-settings&tab=button-settings" class="nav-tab <?php echo ($active_tab == 'button-settings') ? 'nav-tab-active' : ''; ?>">Button Settings</a>
        <a href="?page=compare-products-settings&tab=other-settings" class="nav-tab <?php echo ($active_tab == 'other-settings') ? 'nav-tab-active' : ''; ?>">Other Settings</a>
    </h2>

    <form method="post" action="options.php">
        <?php
        if ($active_tab == 'button-settings') {
            settings_fields('compare_button_options_group');
            do_settings_sections('compare-buttons-settings');
            submit_button();
        } elseif ($active_tab == 'other-settings') {
            echo '<p>No settings for this tab yet.</p>';
        }
        ?>
    </form>
</div>
