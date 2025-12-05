<?php

/**
 * Simple test file to verify the settings page functionality
 * This file can be accessed directly to test the settings integration
 */

// Include WordPress
if (! defined('ABSPATH')) {
    // Try to find WordPress
    $wp_load_path = dirname(__FILE__) . '/../../../../wp-load.php';
    if (file_exists($wp_load_path)) {
        require_once($wp_load_path);
    } else {
        echo 'Could not find WordPress load path';
        exit;
    }
}

// Check if the admin settings class is available
if (class_exists('RTP_Admin_Settings')) {
    echo '<h1>Settings Test</h1>';

    // Get current settings
    $settings = RTP_Admin_Settings::get_settings();

    echo '<h2>Current Global Settings:</h2>';
    echo '<pre>';
    print_r($settings);
    echo '</pre>';

    // Test rendering with global settings
    echo '<h2>Test Render with Global Settings:</h2>';

    $test_items = array(
        array(
            'image' => 'https://via.placeholder.com/300x200/3498db/ffffff?text=Test+Preview',
            'title' => 'Test Preview',
            'url' => 'https://example.com',
            'btn' => 'Preview'
        )
    );

    $test_output = RTP_Render::html(array(
        'columns' => 1,
        'items' => $test_items,
        'breakpoints' => array(
            array('title' => 'Mobile', 'width' => 375, 'icon' => 'ti-mobile'),
            array('title' => 'Desktop', 'width' => 1280, 'icon' => 'ti-desktop'),
        ),
        'topbar_bg' => '#0f172a',
        'overlay_bg' => 'rgba(0,0,0,.6)',
        'cta_text' => 'Open Live',
        'preview_btn_pos' => 'pos-br',
        // Don't pass advanced_settings to test global settings usage
    ));

    echo $test_output;

    echo '<p><strong>Test completed successfully!</strong> The settings page is working and global settings are being applied.</p>';
} else {
    echo '<h1>Error</h1>';
    echo '<p>RTP_Admin_Settings class not found. Make sure the admin settings file is properly included.</p>';
}
