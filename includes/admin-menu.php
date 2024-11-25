<?php
namespace LLM_Friendly;

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
        'LLM-Friendly Content Settings',
        'LLM-Friendly WP',
        'manage_options',
        'llm-friendly',
        __NAMESPACE__.'\\render_settings_page',
        'dashicons-format-aside',
        90
    );

    add_submenu_page(
        'llm-friendly',
        'Content Settings',
        'Content Settings',
        'manage_options',
        'llm-friendly',
        __NAMESPACE__.'\\render_settings_page',
        'dashicons-format-aside',
        90
    );

    add_submenu_page(
        'llm-friendly',
        'LLMs.txt',
        'LLMs.txt',
        'manage_options',
        'llm-friendly-llmstxt',
        __NAMESPACE__.'\\render_llms_txt_page',
        'dashicons-format-aside',
        90
    );
}

/**
 * Render the settings page
 */
function render_settings_page() {
    // Get saved settings
    $selected_categories = get_option('llm_friendly_categories', []);
    $selected_categories = array_filter($selected_categories);
    $filter_tags = get_option('llm_friendly_filter_tags', '');

    // Get all categories
    $categories = get_categories(['hide_empty' => false]);
    ?>
    <div class="wrap">
        <h1>LLM-Friendly Content Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('llm_friendly_settings_action', 'llm_friendly_settings_nonce'); ?>

            <h2>Choose what content to be available also in Markdown LLM-Friendly format:</h2>

            <p><b>Note: this markdown content will be <span style="color:red;">public</span> unless your post/page is in Private mode, is password protected, or is protected via the Profile Builder plugin. At this time no other protection methods are supported.</b></p>

            <!-- Categories selector -->
            <p>
                <strong>Categories:</strong><br>
                <select name="llm_friendly_categories[]" multiple style="width: 100%; max-width: 500px;">
                    <option value=""  <?php echo (empty($selected_categories) or !count($selected_categories)) ? 'selected' : ''; ?>>
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
    if (isset($_POST['llm_friendly_save_settings']) or isset($_POST['llm_friendly_generate_markdown'])) {
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


/**
 * Render the LLMS.txt generation page
 */
function render_llms_txt_page() {
    // Get saved description
    $description = get_option('llm_friendly_description', '');

    // Handle form submission
    if (isset($_POST['llm_friendly_generate_llms_txt'])) {
        check_admin_referer('llm_friendly_generate_llms_txt_action', 'llm_friendly_generate_llms_txt_nonce');

        // Save the description
        $description = sanitize_text_field($_POST['llm_friendly_description']);
        update_option('llm_friendly_description', $description);

        // Generate the llms.txt file
        generate_llms_txt_file();
    }
    ?>
    <div class="wrap">
        <h1>Generate LLMS.txt File</h1>
        <form method="post" action="">
            <?php wp_nonce_field('llm_friendly_generate_llms_txt_action', 'llm_friendly_generate_llms_txt_nonce'); ?>

            <p>
                <strong>Description:</strong><br>
                <textarea name="llm_friendly_description" style="width: 100%; max-width: 500px;" rows="5"><?php echo esc_textarea($description); ?></textarea>
            </p>

            <p>
                <button type="submit" name="llm_friendly_generate_llms_txt" class="button button-primary">Save & Generate</button>
            </p>
        </form>
    </div>
    <?php
}

/**
 * Generate the llms.txt file
 */
function generate_llms_txt_file() {
    $query = get_eligible_posts_query();
    $posts = $query->posts;

    // Initialize an array to group posts by category
    $grouped_posts = [];

    foreach ($posts as $post) {
        $categories = get_the_category($post->ID);
        foreach ($categories as $category) {
            $grouped_posts[$category->name][] = $post;
        }
    }

    $description = get_option('llm_friendly_description', '');

    // Generate the llms.txt content
    $llms_txt_content = "# LLM-Friendly Content\n\n";
    $llms_txt_content .= $description . "\n\n";

    foreach ($grouped_posts as $category_name => $category_posts) {
        $llms_txt_content .= "## " . esc_html($category_name) . "\n\n";
        foreach ($category_posts as $post) {
            $llms_txt_content .= "- [" . esc_html(get_the_title($post->ID)) . "](" . get_permalink($post->ID) . '.md' . ")\n";
        }
        $llms_txt_content .= "\n";
    }

    // Ensure UTF-8 encoding and add BOM
    $llms_txt_content = "\xEF\xBB\xBF" . mb_convert_encoding($llms_txt_content, 'UTF-8', 'auto');

    // Save the llms.txt content to a file in the root directory
    $file_path = ABSPATH . 'llms.txt';
    file_put_contents($file_path, $llms_txt_content);

    // Provide a download link
    $download_url = home_url('/llms.txt');
    echo '<p>LLMS.txt file generated successfully. <a href="' . esc_url($download_url) . '" target="_blank">View LLMS.txt</a></p>';
}


