<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Function to generate llms.txt file with links to Markdown posts
function llm_friendly_wp_generate_llms_txt( $post_ids = [] ) {
    // Define the file path
    $file_path = WP_CONTENT_DIR . '/uploads/llms.txt';

    // Start the content for llms.txt
    $content = "# llms.txt\n\n## About\n";
    $content .= "This file contains links to Markdown resources for Large Language Models.\n\n## Resources\n";

    // Loop through post IDs and generate links
    foreach ( $post_ids as $post_id ) {
        $post_url = get_permalink( $post_id );
        if ( $post_url ) {
            $markdown_url = $post_url . '.md'; // Add .md suffix
            $content .= "- " . esc_url( $markdown_url ) . "\n";
        }
    }

    // Write the file
    file_put_contents( $file_path, $content );

    return $file_path; // Return the path to the generated file
}
