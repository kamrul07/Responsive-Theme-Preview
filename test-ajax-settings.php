<?php

/**
 * Test file for AJAX settings functionality
 * This file can be used to verify that the AJAX settings are working properly
 */

// Load WordPress
require_once('../../../wp-config.php');

// Check if the settings are being saved correctly
$settings = get_option('rtp_settings', array());

echo "<h1>Current RTP Settings</h1>";
echo "<pre>";
print_r($settings);
echo "</pre>";

// Test if the AJAX handler is registered
echo "<h1>AJAX Actions Registered</h1>";
if (has_action('wp_ajax_rtp_save_settings')) {
    echo "<p style='color: green;'>✓ rtp_save_settings AJAX handler is registered</p>";
} else {
    echo "<p style='color: red;'>✗ rtp_save_settings AJAX handler is NOT registered</p>";
}

if (has_action('wp_ajax_rtp_reset_settings')) {
    echo "<p style='color: green;'>✓ rtp_reset_settings AJAX handler is registered</p>";
} else {
    echo "<p style='color: red;'>✗ rtp_reset_settings AJAX handler is NOT registered</p>";
}

// Test if the admin settings class is properly initialized
echo "<h1>Admin Settings Class</h1>";
if (class_exists('RTP_Admin_Settings')) {
    echo "<p style='color: green;'>✓ RTP_Admin_Settings class exists</p>";

    // Test the get_settings method
    $current_settings = RTP_Admin_Settings::get_settings();
    echo "<p>Current settings count: " . count($current_settings) . "</p>";
} else {
    echo "<p style='color: red;'>✗ RTP_Admin_Settings class does NOT exist</p>";
}

// Test if the advanced settings class is properly initialized
echo "<h1>Advanced Settings Class</h1>";
if (class_exists('RTP_Advanced_Settings')) {
    echo "<p style='color: green;'>✓ RTP_Advanced_Settings class exists</p>";

    // Test the get_defaults method
    $defaults = RTP_Advanced_Settings::get_defaults();
    echo "<p>Default settings count: " . count($defaults) . "</p>";
} else {
    echo "<p style='color: red;'>✗ RTP_Advanced_Settings class does NOT exist</p>";
}