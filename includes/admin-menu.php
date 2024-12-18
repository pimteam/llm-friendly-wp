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
        __('LLM-Friendly', 'llm-friendly'),
        __('LLM-Friendly', 'llm-friendly'),
        'manage_options',
        'llm-friendly',
        __NAMESPACE__.'\\render_settings_page',
        'dashicons-format-aside',
        90
    );

    add_submenu_page(
        'llm-friendly',
        __('Content Settings', 'llm-friendly'),
        __('Content Settings', 'llm-friendly'),
        'manage_options',
        'llm-friendly',
        __NAMESPACE__.'\\render_settings_page',
        'dashicons-format-aside',
        90
    );

    add_submenu_page(
        'llm-friendly',
        __('LLMs.txt', 'llm-friendly'),
        __('LLMs.txt', 'llm-friendly'),
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
        <h1><?php esc_html_e('LLM-Friendly Content Settings', 'llm-friendly')?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('llm_friendly_settings_action', 'llm_friendly_settings_nonce'); ?>

            <h2><?php esc_html_e('Choose what content to be available also in Markdown LLM-Friendly format:', 'llm-friendly')?></h2>

            <p><b><?php _e('Note: this markdown content will be <span style="color:red;">public</span> unless your post/page is in Private mode, is password protected, or is protected via the Profile Builder plugin. At this time no other protection methods are supported.', 'llm-friendly')?></b></p>

            <p><?php _e('Every post or page that matches the criteria below will support a Markdown version on the same URL with added "?md=1" or "&md=1" parameter to it. If you choose to <a href="admin.php?page=llm-friendly-llmstxt">generate a LLM.txt file</a>, you can see all the URLs there.', 'llm-friendly');?></p>

            <!-- Categories selector -->
            <p>
                <strong><?php esc_html_e('Categories:', 'llm-friendly')?></strong><br>
                <select name="llm_friendly_categories[]" multiple style="width: 100%; max-width: 500px;">
                    <option value=""  <?php echo (empty($selected_categories) or !count($selected_categories)) ? 'selected' : ''; ?>>
                        <?php esc_html_e('All Categories', 'llm-friendly')?>
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
                <strong><?php esc_html_e('Filter by Tag (optional):', 'llm-friendly')?></strong><br>
                <input type="text" name="llm_friendly_filter_tags" value="<?php echo esc_attr($filter_tags); ?>"
                    style="width: 100%; max-width: 500px;" placeholder="<?php esc_html_e('Enter tag(s), separated by commas', 'llm-friendly')?>">
            </p>

            <!-- Action buttons -->
            <p>
                <button type="submit" name="llm_friendly_save_settings" class="button button-primary"><?php esc_html_e('Save Settings', 'llm-friendly')?></button>
                <button type="submit" name="llm_friendly_generate_markdown" class="button button-secondary"><?php esc_html_e('Generate Markdown Now', 'llm-friendly')?></button>
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
        $selected_categories = array_filter($selected_categories);
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
    $title = get_option('llm_friendly_title', '');

    // Handle form submission
    if (isset($_POST['llm_friendly_generate_llms_txt'])) {
        check_admin_referer('llm_friendly_generate_llms_txt_action', 'llm_friendly_generate_llms_txt_nonce');

        // Save title and description
        $title = sanitize_text_field($_POST['llm_friendly_title']);
        update_option('llm_friendly_title', $title);
        $description = sanitize_text_field($_POST['llm_friendly_description']);
        update_option('llm_friendly_description', $description);

        // Generate the llms.txt file
        $llms_full = empty($_POST['llms_full']) ? 0 : 1;
        generate_llms_txt_file($llms_full);
    }
    ?>
    <div class="wrap">
        <h1>Generate LLMS.txt File</h1>
        <form method="post" action="">
            <?php wp_nonce_field('llm_friendly_generate_llms_txt_action', 'llm_friendly_generate_llms_txt_nonce'); ?>

            <p><?php _e('This file will be created according to the <a href="https://llmstxt.org/" target="_blank">llmstxt.org</a> proposal to make documentation AI/LLM-Friendly.', 'llm-friendly')?></p>

            <p>
                <strong><?php esc_html_e('Title:', 'llm-friendly')?></strong><br>
                <input name="llm_friendly_title" value="<?php echo stripslashes(esc_attr($title));?>" style="width: 100%; max-width: 900px;" required>
            </p>

            <p>
                <strong><?php esc_html_e('Description:', 'llm-friendly')?></strong><br>
                <textarea name="llm_friendly_description" style="width: 100%; max-width: 900px;" rows="15"><?php echo stripslashes(esc_textarea($description)); ?></textarea>
            </p>

            <p><input type="checkbox" name="llms_full" value="1"> <?php _e('Also generate lllms-full.txt - a file that will contain all the markdown combined so it can be fed into large-content AI / LLM bots', 'llm-friendly');?>

            <p>
                <button type="submit" name="llm_friendly_generate_llms_txt" class="button button-primary"><?php esc_html_e('Save & Generate', 'llm-friendly')?></button>
            </p>
        </form>
    </div>
    <?php
}

/**
 * Generate the llms.txt file
 */
function generate_llms_txt_file($full = false) {
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
    $description = get_option('llm_friendly_title', '');

    // Generate the llms.txt content
    $llms_txt_content = "# ".stripslashes(esc_attr($title))."\n\n";
    $llms_full_content = "# ".stripslashes(esc_attr($title))."\n\n";
    $llms_txt_content .= stripslashes($description) . "\n\n";
    $llms_full_content .= stripslashes($description) . "\n\n";

    foreach ($grouped_posts as $category_name => $category_posts) {
        $llms_txt_content .= "## " . esc_html($category_name) . "\n\n";
        $llms_full_content .= "## " . esc_html($category_name) . "\n\n";

        foreach ($category_posts as $post) {
            $permalink = get_permalink($post->ID);
            $permalink = add_query_arg('md', 1, $permalink);
            $llms_txt_content .= "- [" . esc_html(get_the_title($post->ID)) . "](" . $permalink.")\n";
        }
        $llms_txt_content .= "\n";

        if($full) {
            $markdown_content = get_post_meta($post->ID, '_llm_markdown_content', true);

            $llms_full_content .= $markdown_content."\n\n";
        }
    }

    // Ensure UTF-8 encoding and add BOM
    $llms_txt_content = "\xEF\xBB\xBF" . mb_convert_encoding($llms_txt_content, 'UTF-8', 'auto');

    // Save the llms.txt content to a file in the root directory
    $file_path = ABSPATH . 'llms.txt';
    file_put_contents($file_path, $llms_txt_content);

    if($full) {
        $llms_full_content = "\xEF\xBB\xBF" . mb_convert_encoding($llms_full_content, 'UTF-8', 'auto');
        $full_file_path = ABSPATH . 'llms-full.txt';
        file_put_contents($file_path, $llms_full_content);
    }

    // Provide a download link
    $download_url = home_url('/llms.txt');
    echo '<p>'.__('LLMS.txt file generated successfully. <a href="' . esc_url($download_url) . '" target="_blank">View llms.txt</a>', 'llm-friendly').'</p>';

    if($full) {
        $full_download_url = home_url('/llms.txt');
        echo '<p>'.__('llms-full.txt file generated successfully. <a href="' . esc_url($full_download_url) . '" target="_blank">llms-full LLMS.txt</a>', 'llm-friendly').'</p>';
    }
}


