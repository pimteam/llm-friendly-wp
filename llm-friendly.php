<?php
/*
Plugin Name: LLM-Friendly WP
Description: A plugin to generate Markdown versions of WordPress posts and create llms.txt files for Large Language Models.
Version: 1.0.0
Author: Kiboko Labs
License: GPLv2 or later
*/

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
require_once LLMFWP_PLUGIN_DIR . 'includes/llms-txt-generator.php';

// Activation hook
register_activation_hook( __FILE__, 'llm_friendly_wp_activate' );

function llm_friendly_wp_activate() {
    // Code to execute on plugin activation, such as creating options or setting defaults
    add_option( 'llm_friendly_wp_enabled', true );
}

// Deactivation hook
register_deactivation_hook( __FILE__, 'llm_friendly_wp_deactivate' );

function llm_friendly_wp_deactivate() {
    // Code to execute on plugin deactivation
    delete_option( 'llm_friendly_wp_enabled' );
}
