<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Hook to add the admin menu item
add_action('admin_menu', __NAMESPACE__.'\\settings_page');

/**
 * Add a settings page for the plugin
 */
function settings_page() {
    add_menu_page(
        'LLM-Friendly Content Settings', // Page title
        'LLM-Friendly WP',               // Menu title
        'manage_options',                // Capability
        'llm-friendly-settings',         // Menu slug
        __NAMESPACE__.'\\render_settings_page', // Callback function
        'dashicons-format-aside',        // Icon
        90                               // Position
    );
}

/**
 * Render the settings page
 */
function render_settings_page() {
    // Get saved settings
    $selected_categories = get_option('llm_friendly_categories', []);
    $filter_tags = get_option('llm_friendly_filter_tags', '');

    // Get all categories
    $categories = get_categories(['hide_empty' => false]);
    ?>
    <div class="wrap">
        <h1>LLM-Friendly Content Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('llm_friendly_settings_action', 'llm_friendly_settings_nonce'); ?>

            <h2>Choose what content to be available also in Markdown LLM-Friendly format:</h2>

            <!-- Categories selector -->
            <p>
                <strong>Categories:</strong><br>
                <select name="llm_friendly_categories[]" multiple style="width: 100%; max-width: 500px;">
                    <option value="all" <?php echo in_array('all', $selected_categories) ? 'selected' : ''; ?>>
                        All Categories
                    </option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo esc_attr($category->term_id); ?>"
                            <?php echo in_array($category->term_id, $selected_categories) ? 'selected' : ''; ?>>
                            <?php echo esc_html($category->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <!-- Tag filter -->
            <p>
                <strong>Filter by Tag (optional):</strong><br>
                <input type="text" name="llm_friendly_filter_tags" value="<?php echo esc_attr($filter_tags); ?>"
                    style="width: 100%; max-width: 500px;" placeholder="Enter tag(s), separated by commas">
            </p>

            <!-- Action buttons -->
            <p>
                <button type="submit" name="llm_friendly_save_settings" class="button button-primary">Save Settings</button>
                <button type="submit" name="llm_friendly_generate_markdown" class="button button-secondary">Generate Markdown Now</button>
            </p>
        </form>
    </div>
    <?php
}

// Handle form submissions
add_action('admin_init', __NAMESPACE__.'\\handle_settings_form');

/**
 * Process form submissions for the settings page
 */
function handle_settings_form() {
    if (!isset($_POST['llm_friendly_settings_nonce']) ||
        !wp_verify_nonce($_POST['llm_friendly_settings_nonce'], 'llm_friendly_settings_action')) {
        return;
    }

    // Save settings
    if (isset($_POST['llm_friendly_save_settings'])) {
        $selected_categories = isset($_POST['llm_friendly_categories']) ? (array) $_POST['llm_friendly_categories'] : [];
        $filter_tags = sanitize_text_field($_POST['llm_friendly_filter_tags']);

        // Save options
        update_option('llm_friendly_categories', $selected_categories);
        update_option('llm_friendly_filter_tags', $filter_tags);

        // Redirect to settings page
        add_settings_error('llm_friendly_settings', 'settings_updated', 'Settings saved successfully.', 'updated');
    }

    // Generate Markdown
    if (isset($_POST['llm_friendly_generate_markdown'])) {
        // Placeholder: Call your Markdown generation logic here
        generate_all_markdown();
        add_settings_error('llm_friendly_settings', 'markdown_generated', 'Markdown generated successfully.', 'updated');
    }
}
