<?php
/*
Test file to verify admin settings are working in single page preview
*/

// Include WordPress
require_once('../../../wp-config.php');

// Test if admin settings are properly loaded
echo "<h1>Testing Admin Settings Integration</h1>";

// Check if classes are loaded
if (class_exists('RTP_Admin_Settings')) {
    echo "<p style='color: green;'>✓ RTP_Admin_Settings class is loaded</p>";

    // Get settings
    $settings = RTP_Admin_Settings::get_settings();

    echo "<h2>Current Admin Settings:</h2>";
    echo "<pre>";
    print_r($settings);
    echo "</pre>";

    // Check if advanced settings methods work
    if (class_exists('RTP_Advanced_Settings')) {
        echo "<p style='color: green;'>✓ RTP_Advanced_Settings class is loaded</p>";

        // Test CSS generation
        $css = RTP_Advanced_Settings::generate_css($settings);
        echo "<h2>Generated CSS:</h2>";
        echo "<pre>" . htmlspecialchars($css) . "</pre>";

        // Test JS config generation
        $js_config = RTP_Advanced_Settings::generate_js_config($settings);
        echo "<h2>Generated JavaScript Configuration:</h2>";
        echo "<pre>";
        print_r($js_config);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>✗ RTP_Advanced_Settings class is NOT loaded</p>";
    }
} else {
    echo "<p style='color: red;'>✗ RTP_Admin_Settings class is NOT loaded</p>";
}

// Test default settings
if (class_exists('RTP_Advanced_Settings')) {
    $defaults = RTP_Advanced_Settings::get_defaults();
    echo "<h2>Default Settings:</h2>";
    echo "<pre>";
    print_r($defaults);
    echo "</pre>";
}

echo "<p><a href='" . admin_url('edit.php?post_type=responsive_preview&page=rtp-settings') . "'>Go to Admin Settings</a></p>";
