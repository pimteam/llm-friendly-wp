<?php
/*
Plugin Name: LLM-Friendly
Description: A plugin to generate AI and LLM-Friendly Markdown versions of WordPress posts and create llms.txt files for Large Language Models.
Version: 0.5.4
Author: Kiboko Labs
Text-domain: llm-friendly
License: MIT
*/

namespace LLM_Friendly;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'LLMFWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LLMFWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include required files
require_once LLMFWP_PLUGIN_DIR . 'includes/admin-menu.php';
require_once LLMFWP_PLUGIN_DIR . 'includes/markdown-generator.php';
require_once LLMFWP_PLUGIN_DIR . 'lib/html-to-markdown/vendor/autoload.php';

// Activation hook
register_activation_hook( __FILE__, __NAMESPACE__.'\\activate' );

function activate() {
    // Code to execute on plugin activation, such as creating options or setting defaults
    add_option( 'llm_friendly_wp_enabled', true );
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook( __FILE__, __NAMESPACE__.'\\deactivate' );

function deactivate() {
    // Code to execute on plugin deactivation
    delete_option( 'llm_friendly_wp_enabled' );
}

add_action('init', function(){
    load_plugin_textdomain( 'llm-friendly', false, plugin_dir_path( __FILE__ ) . 'languages' );

    // Save markdown
    add_action( 'save_post', __NAMESPACE__.'\\on_save_post', 10, 2 );
});

// load the markdown version of any post
add_action('template_redirect', function () {
    $request_uri = $_SERVER['REQUEST_URI'];
    global $post;

    if (isset($_GET['md']) && $_GET['md'] == 1) {
        // check if the user is allowed to view the post
        if (!is_user_logged_in() and $post->post_status !== 'publish') {
            return;
        }

        if (!empty($post->post_password) and !post_password_required($post)) {
            return;
        }

        // check for plugin restrictions
        $can_view = true;

        // PBP
        if (has_filter('wppb_can_user_view_post')) {
            $can_view = apply_filters('wppb_can_user_view_post', true, $post);
        }

        if (!$can_view) {
            return;
        }

        // Check if the post is eligible based on the current settings
        if (!is_post_eligible($post->ID)) return;

        // get Markdown
        $markdown_content = get_post_meta($post->ID, '_llm_markdown_content', true);

        if ($markdown_content) {
            header('Content-Type: text/plain; charset=UTF-8');
            echo $markdown_content;
            exit;
        }
    }
});
