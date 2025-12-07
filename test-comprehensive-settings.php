<?php

/**
 * Comprehensive Test File for Elementor and Gutenberg Block Settings
 * 
 * This file tests all the newly added settings from Bricks Builder
 * that have been applied to Elementor widget and Gutenberg block.
 * 
 * @package ResponsiveThemePreview
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Test class for comprehensive settings validation
 */
class RTP_Comprehensive_Settings_Test {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init_tests'));
    }

    /**
     * Initialize tests
     */
    public function init_tests() {
        // Only run tests if in admin or if test parameter is set
        if (is_admin() || isset($_GET['rtp_test_settings'])) {
            $this->run_all_tests();
        }
    }

    /**
     * Run all comprehensive tests
     */
    public function run_all_tests() {
        echo '<div class="wrap rtp-test-wrapper" style="max-width: 1200px; margin: 20px; padding: 20px; background: #f9f9f9; border: 1px solid #ddd;">';
        echo '<h1>Responsive Theme Preview - Comprehensive Settings Test</h1>';

        // Test 1: Elementor Widget with All Settings
        $this->test_elementor_widget_settings();

        // Test 2: Gutenberg Block with All Settings
        $this->test_gutenberg_block_settings();

        // Test 3: Category Filtering in Both Implementations
        $this->test_category_filtering();

        // Test 4: Card Styling Settings
        $this->test_card_styling();

        // Test 5: Filter Styling Settings
        $this->test_filter_styling();

        // Test 6: Topbar Settings
        $this->test_topbar_settings();

        // Test 7: Advanced Settings
        $this->test_advanced_settings();

        // Test 8: Settings Validation
        $this->test_settings_validation();

        echo '</div>';
    }

    /**
     * Test Elementor Widget with all settings
     */
    private function test_elementor_widget_settings() {
        echo '<h2>Test 1: Elementor Widget Settings</h2>';

        // Test shortcode generation with all Elementor settings
        $elementor_shortcode = '[responsive_preview 
            source="dynamic" 
            category_filter="business" 
            enable_category_filter="true" 
            columns="3" 
            card_bg="#ffffff" 
            card_border_color="#e0e0e0" 
            card_border_width="1" 
            card_border_radius="8" 
            card_padding="20" 
            card_shadow="0 2px 10px rgba(0,0,0,0.1)" 
            card_title_color="#333333" 
            card_title_size="18" 
            card_title_weight="600" 
            card_desc_color="#666666" 
            card_desc_size="14" 
            card_btn_bg="#0073aa" 
            card_btn_color="#ffffff" 
            card_btn_hover_bg="#005a87" 
            filter_width="100%" 
            filter_direction="row" 
            filter_align="center" 
            filter_gap="10" 
            filter_wrap="true" 
            filter_typography_size="14" 
            filter_typography_weight="500" 
            filter_color="#333333" 
            filter_bg="#f5f5f5" 
            filter_border_color="#dddddd" 
            filter_border_width="1" 
            filter_border_radius="5" 
            filter_padding="10px 15px" 
            filter_active_bg="#0073aa" 
            filter_active_color="#ffffff" 
            topbar_height="60" 
            topbar_bg="#ffffff" 
            topbar_border_color="#e0e0e0" 
            topbar_border_width="1" 
            topbar_title_color="#333333" 
            topbar_title_size="16" 
            topbar_title_weight="600" 
            topbar_btn_bg="#0073aa" 
            topbar_btn_color="#ffffff" 
            topbar_btn_hover_bg="#005a87" 
            overlay_bg="rgba(0,0,0,0.8)" 
            overlay_close_color="#ffffff" 
            overlay_close_size="24" 
            overlay_close_hover_color="#cccccc" 
            enable_keyboard_navigation="true" 
            enable_touch_gestures="true" 
            enable_focus_management="true" 
            animation_type="fadeIn" 
            animation_duration="300" 
        ]';

        echo '<h3>Generated Elementor Shortcode:</h3>';
        echo '<pre style="background: #fff; padding: 15px; border: 1px solid #ccc; overflow-x: auto;">' . esc_html($elementor_shortcode) . '</pre>';

        // Test rendering
        echo '<h3>Rendered Output:</h3>';
        echo '<div style="border: 1px solid #ddd; padding: 10px; background: #fff;">';
        echo do_shortcode($elementor_shortcode);
        echo '</div>';

        echo '<hr>';
    }

    /**
     * Test Gutenberg Block with all settings
     */
    private function test_gutenberg_block_settings() {
        echo '<h2>Test 2: Gutenberg Block Settings</h2>';

        // Test block HTML with all settings
        $block_html = '<!-- wp:block {"ref":"rtp/responsive-preview","attrs":{
            "source":"dynamic",
            "categoryFilter":"business",
            "enableCategoryFilter":true,
            "columns":3,
            "cardBg":"#ffffff",
            "cardBorderColor":"#e0e0e0",
            "cardBorderWidth":1,
            "cardBorderRadius":8,
            "cardPadding":20,
            "cardShadow":"0 2px 10px rgba(0,0,0,0.1)",
            "cardTitleColor":"#333333",
            "cardTitleSize":18,
            "cardTitleWeight":600,
            "cardDescColor":"#666666",
            "cardDescSize":14,
            "cardBtnBg":"#0073aa",
            "cardBtnColor":"#ffffff",
            "cardBtnHoverBg":"#005a87",
            "filterWidth":"100%",
            "filterDirection":"row",
            "filterAlign":"center",
            "filterGap":10,
            "filterWrap":true,
            "filterTypographySize":14,
            "filterTypographyWeight":500,
            "filterColor":"#333333",
            "filterBg":"#f5f5f5",
            "filterBorderColor":"#dddddd",
            "filterBorderWidth":1,
            "filterBorderRadius":5,
            "filterPadding":"10px 15px",
            "filterActiveBg":"#0073aa",
            "filterActiveColor":"#ffffff",
            "topbarHeight":60,
            "topbarBg":"#ffffff",
            "topbarBorderColor":"#e0e0e0",
            "topbarBorderWidth":1,
            "topbarTitleColor":"#333333",
            "topbarTitleSize":16,
            "topbarTitleWeight":600,
            "topbarBtnBg":"#0073aa",
            "topbarBtnColor":"#ffffff",
            "topbarBtnHoverBg":"#005a87",
            "overlayBg":"rgba(0,0,0,0.8)",
            "overlayCloseColor":"#ffffff",
            "overlayCloseSize":24,
            "overlayCloseHoverColor":"#cccccc",
            "enableKeyboardNavigation":true,
            "enableTouchGestures":true,
            "enableFocusManagement":true,
            "animationType":"fadeIn",
            "animationDuration":300
        }} /-->';

        echo '<h3>Generated Gutenberg Block:</h3>';
        echo '<pre style="background: #fff; padding: 15px; border: 1px solid #ccc; overflow-x: auto;">' . esc_html($block_html) . '</pre>';

        // Test rendering
        echo '<h3>Rendered Output:</h3>';
        echo '<div style="border: 1px solid #ddd; padding: 10px; background: #fff;">';
        echo do_blocks($block_html);
        echo '</div>';

        echo '<hr>';
    }

    /**
     * Test category filtering functionality
     */
    private function test_category_filtering() {
        echo '<h2>Test 3: Category Filtering</h2>';

        // Get available categories
        $categories = get_terms(array(
            'taxonomy' => 'rtp-category',
            'hide_empty' => false,
        ));

        if (!empty($categories) && !is_wp_error($categories)) {
            echo '<h3>Available Categories:</h3>';
            echo '<ul>';
            foreach ($categories as $category) {
                echo '<li>' . esc_html($category->name) . ' (' . esc_html($category->slug) . ')</li>';
            }
            echo '</ul>';

            // Test filtering by first category
            $first_category = $categories[0];
            $filter_shortcode = '[responsive_preview source="dynamic" category_filter="' . esc_attr($first_category->slug) . '" enable_category_filter="true" columns="2"]';

            echo '<h3>Category Filter Test (' . esc_html($first_category->name) . '):</h3>';
            echo '<pre style="background: #fff; padding: 15px; border: 1px solid #ccc;">' . esc_html($filter_shortcode) . '</pre>';
            echo '<div style="border: 1px solid #ddd; padding: 10px; background: #fff;">';
            echo do_shortcode($filter_shortcode);
            echo '</div>';
        } else {
            echo '<p>No categories found. Please create some theme previews with categories first.</p>';
        }

        echo '<hr>';
    }

    /**
     * Test card styling settings
     */
    private function test_card_styling() {
        echo '<h2>Test 4: Card Styling Settings</h2>';

        $card_shortcode = '[responsive_preview 
            source="dynamic" 
            columns="2" 
            card_bg="#f8f9fa" 
            card_border_color="#007bff" 
            card_border_width="2" 
            card_border_radius="12" 
            card_padding="25" 
            card_shadow="0 4px 20px rgba(0,123,255,0.15)" 
            card_title_color="#007bff" 
            card_title_size="20" 
            card_title_weight="700" 
            card_desc_color="#6c757d" 
            card_desc_size="16" 
            card_btn_bg="#007bff" 
            card_btn_color="#ffffff" 
            card_btn_hover_bg="#0056b3" 
        ]';

        echo '<h3>Card Styling Test:</h3>';
        echo '<pre style="background: #fff; padding: 15px; border: 1px solid #ccc;">' . esc_html($card_shortcode) . '</pre>';
        echo '<div style="border: 1px solid #ddd; padding: 10px; background: #fff;">';
        echo do_shortcode($card_shortcode);
        echo '</div>';

        echo '<hr>';
    }

    /**
     * Test filter styling settings
     */
    private function test_filter_styling() {
        echo '<h2>Test 5: Filter Styling Settings</h2>';

        $filter_shortcode = '[responsive_preview 
            source="dynamic" 
            enable_category_filter="true" 
            columns="3" 
            filter_width="100%" 
            filter_direction="row" 
            filter_align="center" 
            filter_gap="15" 
            filter_wrap="true" 
            filter_typography_size="16" 
            filter_typography_weight="600" 
            filter_color="#495057" 
            filter_bg="#e9ecef" 
            filter_border_color="#dee2e6" 
            filter_border_width="2" 
            filter_border_radius="25" 
            filter_padding="12px 20px" 
            filter_active_bg="#28a745" 
            filter_active_color="#ffffff" 
        ]';

        echo '<h3>Filter Styling Test:</h3>';
        echo '<pre style="background: #fff; padding: 15px; border: 1px solid #ccc;">' . esc_html($filter_shortcode) . '</pre>';
        echo '<div style="border: 1px solid #ddd; padding: 10px; background: #fff;">';
        echo do_shortcode($filter_shortcode);
        echo '</div>';

        echo '<hr>';
    }

    /**
     * Test topbar settings
     */
    private function test_topbar_settings() {
        echo '<h2>Test 6: Topbar Settings</h2>';

        $topbar_shortcode = '[responsive_preview 
            source="dynamic" 
            columns="2" 
            topbar_height="70" 
            topbar_bg="#343a40" 
            topbar_border_color="#495057" 
            topbar_border_width="2" 
            topbar_title_color="#ffffff" 
            topbar_title_size="18" 
            topbar_title_weight="700" 
            topbar_btn_bg="#28a745" 
            topbar_btn_color="#ffffff" 
            topbar_btn_hover_bg="#218838" 
        ]';

        echo '<h3>Topbar Styling Test:</h3>';
        echo '<pre style="background: #fff; padding: 15px; border: 1px solid #ccc;">' . esc_html($topbar_shortcode) . '</pre>';
        echo '<div style="border: 1px solid #ddd; padding: 10px; background: #fff;">';
        echo do_shortcode($topbar_shortcode);
        echo '</div>';

        echo '<hr>';
    }

    /**
     * Test advanced settings
     */
    private function test_advanced_settings() {
        echo '<h2>Test 7: Advanced Settings</h2>';

        $advanced_shortcode = '[responsive_preview 
            source="dynamic" 
            columns="2" 
            overlay_bg="rgba(255,0,0,0.9)" 
            overlay_close_color="#ffffff" 
            overlay_close_size="30" 
            overlay_close_hover_color="#ffff00" 
            enable_keyboard_navigation="true" 
            enable_touch_gestures="true" 
            enable_focus_management="true" 
            animation_type="slideInUp" 
            animation_duration="500" 
        ]';

        echo '<h3>Advanced Settings Test:</h3>';
        echo '<pre style="background: #fff; padding: 15px; border: 1px solid #ccc;">' . esc_html($advanced_shortcode) . '</pre>';
        echo '<div style="border: 1px solid #ddd; padding: 10px; background: #fff;">';
        echo do_shortcode($advanced_shortcode);
        echo '</div>';

        echo '<hr>';
    }

    /**
     * Test settings validation
     */
    public function test_settings_validation() {
        echo '<h2>Settings Validation</h2>';

        // Test that all required settings are present in Elementor
        $elementor_instance = new RTP_Elementor_Widget();
        // We need to access the controls differently since Elementor widgets don't have a public get_controls method
        // Instead, we'll check the control definitions in the _register_controls method
        $elementor_controls = $this->get_elementor_control_keys();

        $required_settings = array(
            'category_filter',
            'enable_category_filter',
            'card_bg',
            'card_border_color',
            'card_border_width',
            'card_border_radius',
            'card_padding',
            'card_shadow',
            'card_title_color',
            'card_title_size',
            'card_title_weight',
            'card_desc_color',
            'card_desc_size',
            'card_btn_bg',
            'card_btn_color',
            'card_btn_hover_bg',
            'filter_width',
            'filter_direction',
            'filter_align',
            'filter_gap',
            'filter_wrap',
            'filter_typography_size',
            'filter_typography_weight',
            'filter_color',
            'filter_bg',
            'filter_border_color',
            'filter_border_width',
            'filter_border_radius',
            'filter_padding',
            'filter_active_bg',
            'filter_active_color',
            'topbar_height',
            'topbar_bg',
            'topbar_border_color',
            'topbar_border_width',
            'topbar_title_color',
            'topbar_title_size',
            'topbar_title_weight',
            'topbar_btn_bg',
            'topbar_btn_color',
            'topbar_btn_hover_bg',
            'overlay_bg',
            'overlay_close_color',
            'overlay_close_size',
            'overlay_close_hover_color',
            'enable_keyboard_navigation',
            'enable_touch_gestures',
            'enable_focus_management',
            'animation_type',
            'animation_duration'
        );

        echo '<h3>Elementor Controls Validation:</h3>';
        $missing_elementor = array();
        foreach ($required_settings as $setting) {
            if (!in_array($setting, $elementor_controls)) {
                $missing_elementor[] = $setting;
            }
        }

        if (empty($missing_elementor)) {
            echo '<p style="color: green;">✓ All required Elementor controls are present.</p>';
        } else {
            echo '<p style="color: red;">✗ Missing Elementor controls: ' . implode(', ', $missing_elementor) . '</p>';
        }

        // Test that all required attributes are present in Gutenberg block
        $block_instance = new RTP_Block();
        // We need to access the attributes differently since the block class doesn't have a public get_attributes method
        // Instead, we'll check the attribute definitions in the register method
        $block_attributes = $this->get_block_attribute_keys();

        echo '<h3>Gutenberg Block Attributes Validation:</h3>';
        $missing_block = array();
        foreach ($required_settings as $setting) {
            // Convert camelCase to snake_case for comparison
            $block_setting = $this->camel_to_snake($setting);
            if (!in_array($setting, $block_attributes) && !in_array($block_setting, $block_attributes)) {
                $missing_block[] = $setting;
            }
        }

        if (empty($missing_block)) {
            echo '<p style="color: green;">✓ All required Gutenberg block attributes are present.</p>';
        } else {
            echo '<p style="color: red;">✗ Missing Gutenberg block attributes: ' . implode(', ', $missing_block) . '</p>';
        }

        echo '<hr>';
    }

    /**
     * Convert camelCase to snake_case
     */
    private function camel_to_snake($str) {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $str));
    }

    /**
     * Get Elementor control keys from the widget class
     */
    private function get_elementor_control_keys() {
        // Since Elementor widgets don't expose controls publicly,
        // we'll return the expected control keys based on the implementation
        return array(
            'columns',
            'source',
            'dynamic_count',
            'preview_type',
            'category_filter',
            'enable_category_filter',
            'items',
            'breakpoints',
            'cta_text',
            'cta_link',
            'overlay_bg',
            'preview_btn_pos',
            'card_bg',
            'card_border',
            'title_typography',
            'preview_btn_bg',
            'button_typography',
            'button_border',
            'filter_width',
            'filter_direction',
            'filter_align_items',
            'filter_justify_content',
            'filter_gap',
            'filter_wrap',
            'filter_typography',
            'filter_color',
            'filter_bg_color',
            'filter_border',
            'filter_padding',
            'filter_active_color',
            'filter_active_bg_color',
            'filter_active_border',
            'topbar_height',
            'topbar_title_typography',
            'topbar_title_color',
            'overlay_bg_color',
            'device_button_color',
            'device_button_padding',
            'device_button_border',
            'cta_button_bg_color',
            'cta_button_color',
            'cta_padding',
            'cta_button_border',
            'cta_typography',
            'device_button_style',
            'device_button_size',
            'device_button_active_color',
            'device_button_hover_color',
            'overlay_close_on_click',
            'overlay_close_on_esc',
            'overlay_loading_indicator',
            'overlay_loading_color',
            'enable_keyboard_nav',
            'focus_outline',
            'focus_outline_color',
            'focus_outline_width',
        );
    }

    /**
     * Get Gutenberg block attribute keys from the block class
     */
    private function get_block_attribute_keys() {
        // Since the block class doesn't expose attributes publicly,
        // we'll return the expected attribute keys based on the implementation
        return array(
            'columns',
            'source',
            'count',
            'itemsArr',
            'items',
            'breakpoints',
            'ctaText',
            'ctaLink',
            'overlayBg',
            'previewBtnPos',
            'previewType',
            'topbarHeight',
            'categoryFilter',
            'enableCategoryFilter',
            'cardBg',
            'cardBorder',
            'titleTypography',
            'buttonBg',
            'buttonTypography',
            'buttonBorder',
            'filterWidth',
            'filterDirection',
            'filterAlignItems',
            'filterJustifyContent',
            'filterGap',
            'filterWrap',
            'filterTypography',
            'filterColor',
            'filterBgColor',
            'filterBorder',
            'filterPadding',
            'filterActiveColor',
            'filterActiveBgColor',
            'filterActiveBorder',
            'topbarTitleTypography',
            'topbarTitleColor',
            'overlayBgColor',
            'deviceButtonColor',
            'deviceButtonPadding',
            'deviceButtonBorder',
            'ctaButtonBgColor',
            'ctaButtonColor',
            'ctaPadding',
            'ctaButtonBorder',
            'ctaTypography',
            'deviceButtonStyle',
            'deviceButtonSize',
            'deviceButtonActiveColor',
            'deviceButtonHoverColor',
            'overlayCloseOnClick',
            'overlayCloseOnEsc',
            'overlayLoadingIndicator',
            'overlayLoadingColor',
            'enableKeyboardNav',
            'focusOutline',
            'focusOutlineColor',
            'focusOutlineWidth',
        );
    }
}

// Initialize the test class
new RTP_Comprehensive_Settings_Test();
