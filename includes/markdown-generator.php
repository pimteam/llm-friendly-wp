<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Function to convert a WordPress post to Markdown
function llm_friendly_wp_convert_to_markdown( $post_id ) {
    // Ensure the HTML to Markdown library is available
    if ( ! class_exists( 'Html2Text\Html2Text' ) ) {
        require_once ABSPATH . 'vendor/autoload.php'; // Adjust path to your autoload
    }

    // Get the post object
    $post = get_post( $post_id );

    // Validate post
    if ( ! $post || 'publish' !== $post->post_status ) {
        return false; // Only convert published posts
    }

    // Extract post content and title
    $html_content = apply_filters( 'the_content', $post->post_content );
    $title = $post->post_title;

    // Convert HTML to Markdown
    try {
        $converter = new Html2Text\Html2Text( $html_content );
        $markdown = "# " . $title . "\n\n" . $converter->getText();
    } catch ( Exception $e ) {
        return false; // Handle errors gracefully
    }

    // Optionally save Markdown to post meta
    update_post_meta( $post_id, '_llm_markdown_content', $markdown );

    return $markdown;
}
