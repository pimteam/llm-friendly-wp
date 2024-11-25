<?php
namespace LLM_Friendly;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use League\HTMLToMarkdown\HtmlConverter;

/**
 * Convert a WordPress post to Markdown and store it in post meta.
 *
 * @param int $post_id The ID of the post to convert.
 * @return string|false The generated Markdown or false on failure.
 */
function post_to_markdown( int $post_id ) : string|false {
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
        $converter = new HtmlConverter();
        $html = '<h1>'.$title.'</h1>'.$html_content;
        $markdown = $converter->convert($html);
    } catch ( Exception $e ) {
        return false; // Handle errors gracefully
    }

    // Optionally save Markdown to post meta
    update_post_meta( $post_id, '_llm_markdown_content', $markdown );

    return $markdown;
}

/**
 * Generate Markdown for all posts matching the criteria set in the plugin's settings.
 */
function generate_all_markdown() {
    // Fetch settings
    $categories = get_option( 'llm_friendly_categories', [] ); // Selected categories
    $tags = get_option( 'llm_friendly_tags', [] ); // Selected tags
    $include_all = get_option( 'llm_friendly_include_all', false ); // Include all categories flag

    // Build query arguments
    $args = [
        'post_type'      => ['post', 'page'],
        'post_status'    => 'publish',
        'posts_per_page' => -1, // Get all matching posts
    ];

    if ( ! $include_all && ! empty( $categories ) ) {
        $args['category__in'] = $categories; // Filter by categories
    }

    if ( ! empty( $tags ) ) {
        $args['tag__in'] = $tags; // Filter by tags
    }

    // Query posts
    $query = new \WP_Query( $args );

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();

            $post_id = get_the_ID();
            post_to_markdown( $post_id ); // Convert each post to Markdown
        }
    }

    // Restore global post data
    wp_reset_postdata();
}

/**
 * Check if a post matches the plugin criteria for Markdown conversion.
 *
 * @param WP_Post $post The post object.
 * @return bool True if the post matches the criteria, false otherwise.
 */
function should_convert( object $post ) : bool {
    // Fetch settings
    $categories = get_option( 'llm_friendly_categories', [] ); // Selected categories
    $tags = get_option( 'llm_friendly_tags', [] ); // Selected tags
    $include_all = get_option( 'llm_friendly_include_all', false ); // Include all categories flag

    // If all categories are included, no further checks needed
    if ( $include_all ) {
        return true;
    }

    // Check post categories
    $post_categories = wp_get_post_categories( $post->ID );
    if ( array_intersect( $post_categories, $categories ) ) {
        return true;
    }

    // Check post tags
    $post_tags = wp_get_post_tags( $post->ID, [ 'fields' => 'ids' ] );
    if ( array_intersect( $post_tags, $tags ) ) {
        return true;
    }

    return false;
}

/**
 * Hook into WordPress' save_post action to convert posts/pages to Markdown.
 *
 * @param int $post_id The ID of the post being saved.
 * @param WP_Post $post The post object being saved.
 */
function on_save_post($post_id, $post) {
    // Only process if it's a post or page and not an autosave
    if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
        return;
    }

    // Ensure the post status is "publish"
    if ( 'publish' !== $post->post_status ) {
        return;
    }

    // Check if the post matches the criteria for Markdown conversion
    if ( should_convert( $post ) ) {
        post_to_markdown( $post_id );
    }
}
