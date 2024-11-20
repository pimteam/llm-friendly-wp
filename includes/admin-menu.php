<?php
// Create admin menu
function llm_friendly_wp_admin_menu() {
    add_menu_page(
        'LLM-Friendly WP',              // Page title
        'LLM-Friendly WP',              // Menu title
        'manage_options',               // Capability
        'llm-friendly-wp',              // Menu slug
        'llm_friendly_wp_settings_page', // Callback function
        'dashicons-admin-generic',      // Icon
        100                             // Position
    );
}
add_action( 'admin_menu', 'llm_friendly_wp_admin_menu' );

function llm_friendly_wp_generate_llms_file_action() {
    if ( isset( $_POST['llm_generate_llms_txt'] ) && check_admin_referer( 'llm_generate_llms_txt_action' ) ) {
        // Fetch all posts marked for inclusion
        $post_ids = get_posts( [
            'post_type'   => 'post',
            'post_status' => 'publish',
            'fields'      => 'ids',
        ] );

        // Generate llms.txt
        $file_path = llm_friendly_wp_generate_llms_txt( $post_ids );

        echo '<div class="updated"><p>llms.txt has been generated: ' . esc_html( $file_path ) . '</p></div>';
    }
}
add_action( 'admin_notices', 'llm_friendly_wp_generate_llms_file_action' );

function llm_friendly_wp_settings_page() {
    ?>
    <div class="wrap">
        <h1>LLM-Friendly WP Settings</h1>
        <form method="post">
            <?php
            wp_nonce_field( 'llm_generate_llms_txt_action' );
            submit_button( 'Generate llms.txt', 'primary', 'llm_generate_llms_txt' );
            ?>
        </form>
    </div>
    <?php
}


// Register settings
function llm_friendly_wp_register_settings() {
    register_setting( 'llm_friendly_wp_settings_group', 'llm_friendly_wp_enabled' );

    add_settings_section(
        'llm_friendly_wp_main_section', // Section ID
        'General Settings',             // Title
        'llm_friendly_wp_main_section_callback',
        'llm-friendly-wp'               // Page slug
    );

    add_settings_field(
        'llm_friendly_wp_enabled',      // Field ID
        'Enable Plugin',                // Title
        'llm_friendly_wp_enabled_callback',
        'llm-friendly-wp',              // Page slug
        'llm_friendly_wp_main_section'  // Section ID
    );
}
add_action( 'admin_init', 'llm_friendly_wp_register_settings' );

function llm_friendly_wp_main_section_callback() {
    echo '<p>General plugin settings.</p>';
}

function llm_friendly_wp_enabled_callback() {
    $enabled = get_option( 'llm_friendly_wp_enabled', false );
    ?>
    <input type="checkbox" name="llm_friendly_wp_enabled" value="1" <?php checked( $enabled, 1 ); ?> />
    Enable LLM-Friendly WP
    <?php
}


