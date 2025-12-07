<?php

/**
 * Test file for category filtering functionality
 * This file demonstrates how to use the new category filtering features
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

echo '<!DOCTYPE html>';
echo '<html lang="en">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<title>Category Filtering Test - Responsive Theme Preview</title>';
echo '<style>';
echo 'body { font-family: Arial, sans-serif; margin: 20px; }';
echo '.test-section { margin: 40px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }';
echo '.test-section h2 { margin-top: 0; color: #333; }';
echo '.shortcode-demo { background: #f9f9f9; padding: 15px; margin: 15px 0; border-radius: 5px; }';
echo '.usage-note { background: #e7f3ff; padding: 10px; border-left: 4px solid #0073aa; margin: 10px 0; }';
echo '</style>';
echo '</head>';
echo '<body>';

echo '<h1>Category Filtering Test - Responsive Theme Preview</h1>';

// Test 1: Shortcode with category filter
echo '<div class="test-section">';
echo '<h2>Test 1: Shortcode with Category Filter</h2>';
echo '<div class="usage-note">';
echo '<strong>Note:</strong> This shortcode shows only previews from the "business" category.';
echo '</div>';
echo '<div class="shortcode-demo">';
echo '<code>[responsive_preview source="dynamic" count="3" category="business" columns="3"]</code>';
echo '</div>';
echo do_shortcode('[responsive_preview source="dynamic" count="3" category="business" columns="3"]');
echo '</div>';

// Test 2: Shortcode with frontend filter enabled
echo '<div class="test-section">';
echo '<h2>Test 2: Shortcode with Frontend Category Filter</h2>';
echo '<div class="usage-note">';
echo '<strong>Note:</strong> This shortcode shows all previews with a dropdown filter that users can interact with.';
echo '</div>';
echo '<div class="shortcode-demo">';
echo '<code>[responsive_preview source="dynamic" count="6" enable_filter="true" columns="3"]</code>';
echo '</div>';
echo do_shortcode('[responsive_preview source="dynamic" count="6" enable_filter="true" columns="3"]');
echo '</div>';

// Test 3: Shortcode with both category filter and frontend filter
echo '<div class="test-section">';
echo '<h2>Test 3: Shortcode with Category Filter + Frontend Filter</h2>';
echo '<div class="usage-note">';
echo '<strong>Note:</strong> This shortcode shows previews from "portfolio" category with a dropdown filter for sub-categories.';
echo '</div>';
echo '<div class="shortcode-demo">';
echo '<code>[responsive_preview source="dynamic" count="4" category="portfolio" enable_filter="true" columns="2"]</code>';
echo '</div>';
echo do_shortcode('[responsive_preview source="dynamic" count="4" category="portfolio" enable_filter="true" columns="2"]');
echo '</div>';

// Test 4: Static shortcode (no filtering)
echo '<div class="test-section">';
echo '<h2>Test 4: Static Shortcode (No Filtering)</h2>';
echo '<div class="usage-note">';
echo '<strong>Note:</strong> This static shortcode shows manual items without any filtering.';
echo '</div>';
echo '<div class="shortcode-demo">';
echo '<code>[responsive_preview source="static" items="https://example.com/image1.jpg|Preview 1|https://example.com/preview1|Preview;https://example.com/image2.jpg|Preview 2|https://example.com/preview2|Preview" columns="2"]</code>';
echo '</div>';
echo do_shortcode('[responsive_preview source="static" items="https://picsum.photos/400/300?random=1|Static Preview 1|https://example.com/preview1|Preview;https://picsum.photos/400/300?random=2|Static Preview 2|https://example.com/preview2|Preview" columns="2"]');
echo '</div>';

// Display available categories
echo '<div class="test-section">';
echo '<h2>Available Categories</h2>';
$categories = get_terms(array(
    'taxonomy' => 'rtp-category',
    'hide_empty' => false,
));

if (!is_wp_error($categories) && !empty($categories)) {
    echo '<ul>';
    foreach ($categories as $category) {
        $count = $category->count;
        echo '<li><strong>' . esc_html($category->name) . '</strong> (slug: ' . esc_html($category->slug) . ') - ' . $count . ' items</li>';
    }
    echo '</ul>';
} else {
    echo '<p>No categories found. Please create some categories and assign them to preview posts.</p>';
}
echo '</div>';

// Display sample preview posts
echo '<div class="test-section">';
echo '<h2>Sample Preview Posts</h2>';
$previews = new WP_Query(array(
    'post_type' => 'rtp_preview',
    'posts_per_page' => 10,
    'post_status' => 'publish',
));

if ($previews->have_posts()) {
    echo '<table style="width: 100%; border-collapse: collapse;">';
    echo '<tr style="background: #f0f0f0;">';
    echo '<th style="padding: 8px; border: 1px solid #ddd;">Title</th>';
    echo '<th style="padding: 8px; border: 1px solid #ddd;">Categories</th>';
    echo '<th style="padding: 8px; border: 1px solid #ddd;">URL</th>';
    echo '</tr>';

    while ($previews->have_posts()) {
        $previews->the_post();
        $title = get_the_title();
        $url = get_post_meta(get_the_ID(), '_rtp_url', true);
        $categories = get_the_terms(get_the_ID(), 'rtp-category');
        $category_names = array();

        if (!is_wp_error($categories) && !empty($categories)) {
            foreach ($categories as $category) {
                $category_names[] = $category->name;
            }
        }

        echo '<tr>';
        echo '<td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($title) . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #ddd;">' . esc_html(implode(', ', $category_names)) . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($url) . '</td>';
        echo '</tr>';
    }

    echo '</table>';
    wp_reset_postdata();
} else {
    echo '<p>No preview posts found. Please create some preview posts.</p>';
}
echo '</div>';

echo '<div class="usage-note">';
echo '<h3>How to Test:</h3>';
echo '<ol>';
echo '<li>Create some preview posts in WordPress admin</li>';
echo '<li>Assign categories to your preview posts</li>';
echo '<li>Use the shortcodes above in a page or post</li>';
echo '<li>Test the category filtering functionality</li>';
echo '<li>For Elementor and Bricks Builder, use the respective widgets with category controls</li>';
echo '</ol>';
echo '</div>';

echo '</body>';
echo '</html>';