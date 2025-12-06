<?php

if (! defined('ABSPATH')) {
    exit;
}

class RTP_Admin_Settings {

    private $options;
    private $settings;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('admin_init', array($this, 'handle_ajax_requests'));
    }

    public function add_plugin_page() {
        add_menu_page(
            'Responsive Theme Preview',
            'Previews',
            'manage_options',
            'rtp-previews',
            '',
            'dashicons-tablet',
            30
        );

        add_submenu_page(
            'rtp-previews',
            'Responsive Theme Preview Settings',
            'Settings',
            'manage_options',
            'rtp-settings',
            array($this, 'create_admin_page')
        );
    }

    public function admin_scripts($hook) {
        if ('rtp-previews_page_rtp-settings' !== $hook) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('rtp-admin-styles', plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin-styles.css', array(), '1.0');
        wp_enqueue_script('rtp-admin-settings', plugin_dir_url(dirname(__FILE__)) . 'assets/js/admin-settings.js', array('jquery', 'wp-color-picker'), '1.0', true);

        wp_localize_script('rtp-admin-settings', 'rtp_admin', array(
            'nonce' => wp_create_nonce('rtp_admin_nonce'),
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
    }

    public function create_admin_page() {
        $this->options = get_option('rtp_settings');

        // Handle success/error messages
        $message = '';
        if (isset($_GET['rtp-message'])) {
            $message = sanitize_text_field($_GET['rtp-message']);
            $type = isset($_GET['rtp-type']) ? sanitize_text_field($_GET['rtp-type']) : 'success';
        }
?>
<div class="wrap rtp-settings-wrap">
    <h1>Responsive Theme Preview Settings</h1>

    <?php if ($message): ?>
    <div class="notice notice-<?php echo esc_attr($type); ?> is-dismissible">
        <p><?php echo esc_html($message); ?></p>
    </div>
    <?php endif; ?>

    <!-- WordPress-style Tab Navigation -->
    <nav class="nav-tab-wrapper rtp-tab-nav">
        <a href="#general" data-tab="general" class="nav-tab nav-tab-active">General</a>
        <a href="#breakpoints" data-tab="breakpoints" class="nav-tab">Breakpoints</a>
        <a href="#appearance" data-tab="appearance" class="nav-tab">Appearance</a>
        <a href="#overlay" data-tab="overlay" class="nav-tab">Overlay</a>
        <a href="#preview" data-tab="preview" class="nav-tab">Preview</a>
        <a href="#performance" data-tab="performance" class="nav-tab">Performance</a>
        <a href="#accessibility" data-tab="accessibility" class="nav-tab">Accessibility</a>
        <a href="#import-export" data-tab="import-export" class="nav-tab">Import/Export</a>
        <a href="#custom" data-tab="custom" class="nav-tab">Custom</a>
    </nav>

    <form method="post" action="options.php">
        <?php settings_fields('rtp_settings_group'); ?>
        <div class="rtp-tab-content-container">
            <!-- General Tab -->
            <div id="general" class="rtp-tab-content active">
                <div class="rtp-sections-wrapper">
                    <?php $this->render_settings_section('rtp_general_section'); ?>
                </div>
            </div>

            <!-- Breakpoints Tab -->
            <div id="breakpoints" class="rtp-tab-content">
                <div class="rtp-sections-wrapper">
                    <?php $this->render_settings_section('rtp_breakpoints_section'); ?>
                </div>
            </div>

            <!-- Appearance Tab -->
            <div id="appearance" class="rtp-tab-content">
                <div class="rtp-sections-wrapper">
                    <?php $this->render_settings_section('rtp_appearance_section'); ?>
                </div>
            </div>

            <!-- Overlay Tab -->
            <div id="overlay" class="rtp-tab-content">
                <div class="rtp-sections-wrapper">
                    <?php $this->render_settings_section('rtp_overlay_section'); ?>
                </div>
            </div>

            <!-- Preview Tab -->
            <div id="preview" class="rtp-tab-content">
                <div class="rtp-sections-wrapper">
                    <?php $this->render_settings_section('rtp_preview_section'); ?>
                </div>
            </div>

            <!-- Performance Tab -->
            <div id="performance" class="rtp-tab-content">
                <div class="rtp-sections-wrapper">
                    <?php $this->render_settings_section('rtp_performance_section'); ?>
                </div>
            </div>

            <!-- Accessibility Tab -->
            <div id="accessibility" class="rtp-tab-content">
                <div class="rtp-sections-wrapper">
                    <?php $this->render_settings_section('rtp_accessibility_section'); ?>
                </div>
            </div>

            <!-- Import/Export Tab -->
            <div id="import-export" class="rtp-tab-content">
                <div class="rtp-sections-wrapper">
                    <?php $this->render_settings_section('rtp_import_export_section'); ?>
                </div>
            </div>

            <!-- Custom Tab -->
            <div id="custom" class="rtp-tab-content">
                <div class="rtp-sections-wrapper">
                    <?php $this->render_settings_section('rtp_custom_section'); ?>
                </div>
            </div>
        </div>
        <?php submit_button('Save Settings', 'primary', 'submit', true, array('id' => 'rtp-submit')); ?>
    </form>
</div>
<?php
    }

    /**
     * Render a specific settings section
     */
    private function render_settings_section($section_id) {
        global $wp_settings_sections;
        global $wp_settings_fields;

        if (!isset($wp_settings_sections[$section_id])) {
            return;
        }

        $section = $wp_settings_sections[$section_id];

        // Add section title
        echo '<h2>' . $section['title'] . '</h2>';

        if (!empty($section['callback'])) {
            call_user_func($section['callback'], $section);
        }

        // Render fields for this section only
        if (isset($wp_settings_fields[$section_id])) {
            echo '<table class="form-table" role="presentation">';
            foreach ($wp_settings_fields[$section_id] as $field) {
                echo '<tr>';
                echo '<th scope="row">' . $field['title'] . '</th>';
                echo '<td>';
                call_user_func($field['callback'], $field['args']);
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }

    public function page_init() {
        register_setting(
            'rtp_settings_group',
            'rtp_settings',
            array($this, 'sanitize_settings')
        );

        // General Settings Tab
        add_settings_section(
            'rtp_general_section',
            'General Settings',
            array($this, 'print_section_info'),
            'rtp-settings'
        );

        add_settings_field(
            'topbar_height',
            'Topbar Height',
            array($this, 'topbar_height_callback'),
            'rtp-settings',
            'rtp_general_section'
        );

        // Appearance Settings Tab
        add_settings_section(
            'rtp_appearance_section',
            'Appearance Settings',
            array($this, 'print_appearance_info'),
            'rtp-settings'
        );

        add_settings_field(
            'device_button_style',
            'Device Button Style',
            array($this, 'device_button_style_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'device_button_size',
            'Device Button Size',
            array($this, 'device_button_size_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'device_button_active_color',
            'Device Button Active Color',
            array($this, 'device_button_active_color_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'device_button_hover_color',
            'Device Button Hover Color',
            array($this, 'device_button_hover_color_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        // Topbar Settings
        add_settings_field(
            'topbar_bg',
            'Topbar Background Color',
            array($this, 'topbar_bg_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'topbar_title_size',
            'Topbar Title Font Size',
            array($this, 'topbar_title_size_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'topbar_title_color',
            'Topbar Title Font Color',
            array($this, 'topbar_title_color_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'topbar_button_color',
            'Topbar Button Color',
            array($this, 'topbar_button_color_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'topbar_button_bg',
            'Topbar Button Background',
            array($this, 'topbar_button_bg_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'topbar_button_font_size',
            'Topbar Button Font Size',
            array($this, 'topbar_button_font_size_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'topbar_button_border_radius',
            'Topbar Button Border Radius',
            array($this, 'topbar_button_border_radius_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'topbar_button_padding',
            'Topbar Button Padding',
            array($this, 'topbar_button_padding_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'overlay_bg_color',
            'Overlay Background Color',
            array($this, 'overlay_bg_color_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'overlay_border_color',
            'Overlay Border Color',
            array($this, 'overlay_border_color_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'frame_border_color',
            'Preview Frame Border Color',
            array($this, 'frame_border_color_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'frame_shadow_color',
            'Preview Frame Shadow Color',
            array($this, 'frame_shadow_color_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'cta_button_color',
            'CTA Button Text Color',
            array($this, 'cta_button_color_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'cta_button_bg_color',
            'CTA Button Background Color',
            array($this, 'cta_button_bg_color_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'cta_button_hover_color',
            'CTA Button Hover Color',
            array($this, 'cta_button_hover_color_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'device_button_text_color',
            'Device Button Text Color',
            array($this, 'device_button_text_color_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        add_settings_field(
            'device_button_bg_color',
            'Device Button Background Color',
            array($this, 'device_button_bg_color_callback'),
            'rtp-settings',
            'rtp_appearance_section'
        );

        // Overlay Settings Tab
        add_settings_section(
            'rtp_overlay_section',
            'Overlay Settings',
            array($this, 'print_overlay_info'),
            'rtp-settings'
        );

        add_settings_field(
            'overlay_close_on_click',
            'Close on Click',
            array($this, 'overlay_close_on_click_callback'),
            'rtp-settings',
            'rtp_overlay_section'
        );

        add_settings_field(
            'overlay_close_on_esc',
            'Close on ESC',
            array($this, 'overlay_close_on_esc_callback'),
            'rtp-settings',
            'rtp_overlay_section'
        );

        add_settings_field(
            'overlay_loading_indicator',
            'Show Loading Indicator',
            array($this, 'overlay_loading_indicator_callback'),
            'rtp-settings',
            'rtp_overlay_section'
        );

        add_settings_field(
            'overlay_loading_color',
            'Loading Indicator Color',
            array($this, 'overlay_loading_color_callback'),
            'rtp-settings',
            'rtp_overlay_section'
        );

        // Preview Settings Tab
        add_settings_section(
            'rtp_preview_section',
            'Preview Settings',
            array($this, 'print_preview_info'),
            'rtp-settings'
        );

        add_settings_field(
            'preview_start_with_device',
            'Start with Device',
            array($this, 'preview_start_with_device_callback'),
            'rtp-settings',
            'rtp_preview_section'
        );

        add_settings_field(
            'preview_zoom_level',
            'Default Zoom Level',
            array($this, 'preview_zoom_level_callback'),
            'rtp-settings',
            'rtp_preview_section'
        );

        add_settings_field(
            'preview_allow_zoom',
            'Allow Zoom',
            array($this, 'preview_allow_zoom_callback'),
            'rtp-settings',
            'rtp_preview_section'
        );

        // Performance Settings Tab
        add_settings_section(
            'rtp_performance_section',
            'Performance Settings',
            array($this, 'print_performance_info'),
            'rtp-settings'
        );

        add_settings_field(
            'lazy_load_preview',
            'Lazy Load Preview',
            array($this, 'lazy_load_preview_callback'),
            'rtp-settings',
            'rtp_performance_section'
        );

        add_settings_field(
            'preload_previews',
            'Preload Previews',
            array($this, 'preload_previews_callback'),
            'rtp-settings',
            'rtp_performance_section'
        );

        add_settings_field(
            'cache_previews',
            'Cache Previews',
            array($this, 'cache_previews_callback'),
            'rtp-settings',
            'rtp_performance_section'
        );

        add_settings_field(
            'cache_duration',
            'Cache Duration (seconds)',
            array($this, 'cache_duration_callback'),
            'rtp-settings',
            'rtp_performance_section'
        );

        // Accessibility Settings Tab
        add_settings_section(
            'rtp_accessibility_section',
            'Accessibility Settings',
            array($this, 'print_accessibility_info'),
            'rtp-settings'
        );

        add_settings_field(
            'enable_keyboard_nav',
            'Enable Keyboard Navigation',
            array($this, 'enable_keyboard_nav_callback'),
            'rtp-settings',
            'rtp_accessibility_section'
        );

        add_settings_field(
            'enable_screen_reader',
            'Enable Screen Reader Support',
            array($this, 'enable_screen_reader_callback'),
            'rtp-settings',
            'rtp_accessibility_section'
        );

        add_settings_field(
            'focus_outline',
            'Show Focus Outline',
            array($this, 'focus_outline_callback'),
            'rtp-settings',
            'rtp_accessibility_section'
        );

        add_settings_field(
            'focus_outline_color',
            'Focus Outline Color',
            array($this, 'focus_outline_color_callback'),
            'rtp-settings',
            'rtp_accessibility_section'
        );

        add_settings_field(
            'focus_outline_width',
            'Focus Outline Width',
            array($this, 'focus_outline_width_callback'),
            'rtp-settings',
            'rtp_accessibility_section'
        );

        // Custom CSS/JS Tab
        add_settings_section(
            'rtp_custom_section',
            'Custom CSS/JS',
            array($this, 'print_custom_info'),
            'rtp-settings'
        );

        add_settings_field(
            'custom_css',
            'Custom CSS',
            array($this, 'custom_css_callback'),
            'rtp-settings',
            'rtp_custom_section'
        );

        add_settings_field(
            'custom_js',
            'Custom JavaScript',
            array($this, 'custom_js_callback'),
            'rtp-settings',
            'rtp_custom_section'
        );
    }

    // Section info callbacks
    public function print_section_info() {
        print 'Configure general settings for responsive theme preview:';
    }

    public function print_appearance_info() {
        print 'Configure appearance settings for responsive theme preview:';
    }

    public function print_overlay_info() {
        print 'Configure overlay behavior and appearance:';
    }

    public function print_preview_info() {
        print 'Configure preview functionality:';
    }

    public function print_performance_info() {
        print 'Configure performance-related settings:';
    }

    public function print_accessibility_info() {
        print 'Configure accessibility features:';
    }

    public function print_custom_info() {
        print 'Add custom CSS and JavaScript:';
    }

    // Field callbacks for General Settings
    public function topbar_height_callback() {
        $value = isset($this->options['topbar_height']) ? $this->options['topbar_height'] : '52';
        echo "<input type='number' id='topbar_height' name='rtp_settings[topbar_height]' value='" . esc_attr($value) . "' min='30' max='100' />";
        echo "<p class='description'>Enter the height of the topbar in pixels (default: 52)</p>";
    }

    // Field callbacks for Appearance Settings
    public function device_button_style_callback() {
        $value = isset($this->options['device_button_style']) ? $this->options['device_button_style'] : 'default';
        $options = array(
            'default' => 'Default',
            'rounded' => 'Rounded',
            'square' => 'Square'
        );

        echo "<select id='device_button_style' name='rtp_settings[device_button_style]'>";
        foreach ($options as $key => $label) {
            echo "<option value='" . esc_attr($key) . "' " . selected($value, $key, false) . ">" . esc_html($label) . "</option>";
        }
        echo "</select>";
        echo "<p class='description'>Choose the style for device buttons</p>";
    }

    public function device_button_size_callback() {
        $value = isset($this->options['device_button_size']) ? $this->options['device_button_size'] : 'medium';
        $options = array(
            'small' => 'Small',
            'medium' => 'Medium',
            'large' => 'Large'
        );

        echo "<select id='device_button_size' name='rtp_settings[device_button_size]'>";
        foreach ($options as $key => $label) {
            echo "<option value='" . esc_attr($key) . "' " . selected($value, $key, false) . ">" . esc_html($label) . "</option>";
        }
        echo "</select>";
        echo "<p class='description'>Choose the size for device buttons</p>";
    }

    public function device_button_active_color_callback() {
        $value = isset($this->options['device_button_active_color']) ? $this->options['device_button_active_color'] : '#2563eb';
        echo "<input type='text' id='device_button_active_color' name='rtp_settings[device_button_active_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the active color for device buttons</p>";
    }

    public function device_button_hover_color_callback() {
        $value = isset($this->options['device_button_hover_color']) ? $this->options['device_button_hover_color'] : '#1d4ed8';
        echo "<input type='text' id='device_button_hover_color' name='rtp_settings[device_button_hover_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the hover color for device buttons</p>";
    }

    public function topbar_bg_callback() {
        $value = isset($this->options['topbar_bg']) ? $this->options['topbar_bg'] : '#ffffff';
        echo "<input type='text' id='topbar_bg' name='rtp_settings[topbar_bg]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the background color for the topbar</p>";
    }

    public function topbar_title_size_callback() {
        $value = isset($this->options['topbar_title_size']) ? $this->options['topbar_title_size'] : '16';
        echo "<input type='number' id='topbar_title_size' name='rtp_settings[topbar_title_size]' value='" . esc_attr($value) . "' min='10' max='30' />";
        echo "<p class='description'>Enter the font size for the topbar title in pixels (default: 16)</p>";
    }

    public function topbar_title_color_callback() {
        $value = isset($this->options['topbar_title_color']) ? $this->options['topbar_title_color'] : '#333333';
        echo "<input type='text' id='topbar_title_color' name='rtp_settings[topbar_title_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the font color for the topbar title</p>";
    }

    public function topbar_button_color_callback() {
        $value = isset($this->options['topbar_button_color']) ? $this->options['topbar_button_color'] : '#2563eb';
        echo "<input type='text' id='topbar_button_color' name='rtp_settings[topbar_button_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the font color for the topbar button</p>";
    }

    public function topbar_button_bg_callback() {
        $value = isset($this->options['topbar_button_bg']) ? $this->options['topbar_button_bg'] : '#ffffff';
        echo "<input type='text' id='topbar_button_bg' name='rtp_settings[topbar_button_bg]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the background color for the topbar button</p>";
    }

    public function topbar_button_font_size_callback() {
        $value = isset($this->options['topbar_button_font_size']) ? $this->options['topbar_button_font_size'] : '14';
        echo "<input type='number' id='topbar_button_font_size' name='rtp_settings[topbar_button_font_size]' value='" . esc_attr($value) . "' min='10' max='24' />";
        echo "<p class='description'>Enter the font size for the topbar button in pixels (default: 14)</p>";
    }

    public function topbar_button_border_radius_callback() {
        $value = isset($this->options['topbar_button_border_radius']) ? $this->options['topbar_button_border_radius'] : '4';
        echo "<input type='number' id='topbar_button_border_radius' name='rtp_settings[topbar_button_border_radius]' value='" . esc_attr($value) . "' min='0' max='50' />";
        echo "<p class='description'>Enter the border radius for the topbar button in pixels (default: 4)</p>";
    }

    public function topbar_button_padding_callback() {
        $top = isset($this->options['topbar_button_padding_top']) ? $this->options['topbar_button_padding_top'] : '8';
        $right = isset($this->options['topbar_button_padding_right']) ? $this->options['topbar_button_padding_right'] : '16';
        $bottom = isset($this->options['topbar_button_padding_bottom']) ? $this->options['topbar_button_padding_bottom'] : '8';
        $left = isset($this->options['topbar_button_padding_left']) ? $this->options['topbar_button_padding_left'] : '16';

        echo "<div style='display: flex; gap: 10px; align-items: center;'>";
        echo "<label>Top: <input type='number' name='rtp_settings[topbar_button_padding_top]' value='" . esc_attr($top) . "' min='0' max='50' style='width: 60px;' /></label>";
        echo "<label>Right: <input type='number' name='rtp_settings[topbar_button_padding_right]' value='" . esc_attr($right) . "' min='0' max='50' style='width: 60px;' /></label>";
        echo "<label>Bottom: <input type='number' name='rtp_settings[topbar_button_padding_bottom]' value='" . esc_attr($bottom) . "' min='0' max='50' style='width: 60px;' /></label>";
        echo "<label>Left: <input type='number' name='rtp_settings[topbar_button_padding_left]' value='" . esc_attr($left) . "' min='0' max='50' style='width: 60px;' /></label>";
        echo "</div>";
        echo "<p class='description'>Enter the padding for the topbar button in pixels (default: 8 16 8 16)</p>";
    }

    public function overlay_bg_color_callback() {
        $value = isset($this->options['overlay_bg_color']) ? $this->options['overlay_bg_color'] : '#000000';
        echo "<input type='text' id='overlay_bg_color' name='rtp_settings[overlay_bg_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the background color for the overlay</p>";
    }

    public function overlay_border_color_callback() {
        $value = isset($this->options['overlay_border_color']) ? $this->options['overlay_border_color'] : '#cccccc';
        echo "<input type='text' id='overlay_border_color' name='rtp_settings[overlay_border_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the border color for the overlay</p>";
    }

    public function frame_border_color_callback() {
        $value = isset($this->options['frame_border_color']) ? $this->options['frame_border_color'] : '#dddddd';
        echo "<input type='text' id='frame_border_color' name='rtp_settings[frame_border_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the border color for the preview frame</p>";
    }

    public function frame_shadow_color_callback() {
        $value = isset($this->options['frame_shadow_color']) ? $this->options['frame_shadow_color'] : 'rgba(0, 0, 0, 0.2)';
        echo "<input type='text' id='frame_shadow_color' name='rtp_settings[frame_shadow_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the shadow color for the preview frame</p>";
    }

    public function cta_button_color_callback() {
        $value = isset($this->options['cta_button_color']) ? $this->options['cta_button_color'] : '#ffffff';
        echo "<input type='text' id='cta_button_color' name='rtp_settings[cta_button_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the text color for the CTA button</p>";
    }

    public function cta_button_bg_color_callback() {
        $value = isset($this->options['cta_button_bg_color']) ? $this->options['cta_button_bg_color'] : '#2563eb';
        echo "<input type='text' id='cta_button_bg_color' name='rtp_settings[cta_button_bg_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the background color for the CTA button</p>";
    }

    public function cta_button_hover_color_callback() {
        $value = isset($this->options['cta_button_hover_color']) ? $this->options['cta_button_hover_color'] : '#1d4ed8';
        echo "<input type='text' id='cta_button_hover_color' name='rtp_settings[cta_button_hover_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the hover color for the CTA button</p>";
    }

    public function device_button_text_color_callback() {
        $value = isset($this->options['device_button_text_color']) ? $this->options['device_button_text_color'] : '#333333';
        echo "<input type='text' id='device_button_text_color' name='rtp_settings[device_button_text_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the text color for device buttons</p>";
    }

    public function device_button_bg_color_callback() {
        $value = isset($this->options['device_button_bg_color']) ? $this->options['device_button_bg_color'] : '#f5f5f5';
        echo "<input type='text' id='device_button_bg_color' name='rtp_settings[device_button_bg_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the background color for device buttons</p>";
    }

    // Field callbacks for Overlay Settings
    public function overlay_close_on_click_callback() {
        $checked = isset($this->options['overlay_close_on_click']) && $this->options['overlay_close_on_click'] ? 'checked' : '';
        echo "<input type='checkbox' id='overlay_close_on_click' name='rtp_settings[overlay_close_on_click]' value='1' " . $checked . " />";
        echo "<p class='description'>Close overlay when clicking outside of preview</p>";
    }

    public function overlay_close_on_esc_callback() {
        $checked = isset($this->options['overlay_close_on_esc']) && $this->options['overlay_close_on_esc'] ? 'checked' : '';
        echo "<input type='checkbox' id='overlay_close_on_esc' name='rtp_settings[overlay_close_on_esc]' value='1' " . $checked . " />";
        echo "<p class='description'>Close overlay when pressing ESC key</p>";
    }

    public function overlay_loading_indicator_callback() {
        $checked = isset($this->options['overlay_loading_indicator']) && $this->options['overlay_loading_indicator'] ? 'checked' : '';
        echo "<input type='checkbox' id='overlay_loading_indicator' name='rtp_settings[overlay_loading_indicator]' value='1' " . $checked . " />";
        echo "<p class='description'>Show loading indicator while preview loads</p>";
    }

    public function overlay_loading_color_callback() {
        $value = isset($this->options['overlay_loading_color']) ? $this->options['overlay_loading_color'] : '#2563eb';
        echo "<input type='text' id='overlay_loading_color' name='rtp_settings[overlay_loading_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the color for the loading indicator</p>";
    }

    // Field callbacks for Preview Settings
    public function preview_start_with_device_callback() {
        $value = isset($this->options['preview_start_with_device']) ? $this->options['preview_start_with_device'] : 'desktop';
        $options = array(
            'desktop' => 'Desktop',
            'tablet' => 'Tablet',
            'mobile' => 'Mobile'
        );

        echo "<select id='preview_start_with_device' name='rtp_settings[preview_start_with_device]'>";
        foreach ($options as $key => $label) {
            echo "<option value='" . esc_attr($key) . "' " . selected($value, $key, false) . ">" . esc_html($label) . "</option>";
        }
        echo "</select>";
        echo "<p class='description'>Choose the default device to start with</p>";
    }

    public function preview_zoom_level_callback() {
        $value = isset($this->options['preview_zoom_level']) ? $this->options['preview_zoom_level'] : '1.0';
        echo "<input type='number' id='preview_zoom_level' name='rtp_settings[preview_zoom_level]' value='" . esc_attr($value) . "' min='0.5' max='2.0' step='0.1' />";
        echo "<p class='description'>Enter the default zoom level (0.5 - 2.0, default: 1.0)</p>";
    }

    public function preview_allow_zoom_callback() {
        $checked = isset($this->options['preview_allow_zoom']) && $this->options['preview_allow_zoom'] ? 'checked' : '';
        echo "<input type='checkbox' id='preview_allow_zoom' name='rtp_settings[preview_allow_zoom]' value='1' " . $checked . " />";
        echo "<p class='description'>Allow users to zoom in/out of the preview</p>";
    }

    // Field callbacks for Performance Settings
    public function lazy_load_preview_callback() {
        $checked = isset($this->options['lazy_load_preview']) && $this->options['lazy_load_preview'] ? 'checked' : '';
        echo "<input type='checkbox' id='lazy_load_preview' name='rtp_settings[lazy_load_preview]' value='1' " . $checked . " />";
        echo "<p class='description'>Load preview content only when needed</p>";
    }

    public function preload_previews_callback() {
        $checked = isset($this->options['preload_previews']) && $this->options['preload_previews'] ? 'checked' : '';
        echo "<input type='checkbox' id='preload_previews' name='rtp_settings[preload_previews]' value='1' " . $checked . " />";
        echo "<p class='description'>Preload preview content for faster access</p>";
    }

    public function cache_previews_callback() {
        $checked = isset($this->options['cache_previews']) && $this->options['cache_previews'] ? 'checked' : '';
        echo "<input type='checkbox' id='cache_previews' name='rtp_settings[cache_previews]' value='1' " . $checked . " />";
        echo "<p class='description'>Cache preview content for better performance</p>";
    }

    public function cache_duration_callback() {
        $value = isset($this->options['cache_duration']) ? $this->options['cache_duration'] : '3600';
        echo "<input type='number' id='cache_duration' name='rtp_settings[cache_duration]' value='" . esc_attr($value) . "' min='60' max='86400' />";
        echo "<p class='description'>Cache duration in seconds (default: 3600 = 1 hour)</p>";
    }

    // Field callbacks for Accessibility Settings
    public function enable_keyboard_nav_callback() {
        $checked = isset($this->options['enable_keyboard_nav']) && $this->options['enable_keyboard_nav'] ? 'checked' : '';
        echo "<input type='checkbox' id='enable_keyboard_nav' name='rtp_settings[enable_keyboard_nav]' value='1' " . $checked . " />";
        echo "<p class='description'>Enable keyboard navigation for the preview</p>";
    }

    public function enable_screen_reader_callback() {
        $checked = isset($this->options['enable_screen_reader']) && $this->options['enable_screen_reader'] ? 'checked' : '';
        echo "<input type='checkbox' id='enable_screen_reader' name='rtp_settings[enable_screen_reader]' value='1' " . $checked . " />";
        echo "<p class='description'>Enable screen reader support</p>";
    }

    public function focus_outline_callback() {
        $checked = isset($this->options['focus_outline']) && $this->options['focus_outline'] ? 'checked' : '';
        echo "<input type='checkbox' id='focus_outline' name='rtp_settings[focus_outline]' value='1' " . $checked . " />";
        echo "<p class='description'>Show focus outline for better accessibility</p>";
    }

    public function focus_outline_color_callback() {
        $value = isset($this->options['focus_outline_color']) ? $this->options['focus_outline_color'] : '#2563eb';
        echo "<input type='text' id='focus_outline_color' name='rtp_settings[focus_outline_color]' value='" . esc_attr($value) . "' class='rtp-color-picker' />";
        echo "<p class='description'>Choose the color for focus outlines</p>";
    }

    public function focus_outline_width_callback() {
        $value = isset($this->options['focus_outline_width']) ? $this->options['focus_outline_width'] : '2';
        echo "<input type='number' id='focus_outline_width' name='rtp_settings[focus_outline_width]' value='" . esc_attr($value) . "' min='1' max='10' />";
        echo "<p class='description'>Enter the width for focus outlines in pixels (default: 2)</p>";
    }

    // Field callbacks for Custom CSS/JS
    public function custom_css_callback() {
        $value = isset($this->options['custom_css']) ? $this->options['custom_css'] : '';
        echo "<textarea id='custom_css' name='rtp_settings[custom_css]' rows='10' style='width: 100%;'>" . esc_textarea($value) . "</textarea>";
        echo "<p class='description'>Add custom CSS to override default styles</p>";
    }

    public function custom_js_callback() {
        $value = isset($this->options['custom_js']) ? $this->options['custom_js'] : '';
        echo "<textarea id='custom_js' name='rtp_settings[custom_js]' rows='10' style='width: 100%;'>" . esc_textarea($value) . "</textarea>";
        echo "<p class='description'>Add custom JavaScript for additional functionality</p>";
    }

    public function sanitize_settings($input) {
        $sanitized = array();

        // Topbar height
        if (isset($input['topbar_height'])) {
            $sanitized['topbar_height'] = absint($input['topbar_height']);
        }

        // Device button settings
        if (isset($input['device_button_style'])) {
            $sanitized['device_button_style'] = in_array($input['device_button_style'], array('default', 'rounded', 'square')) ? $input['device_button_style'] : 'default';
        }

        if (isset($input['device_button_size'])) {
            $sanitized['device_button_size'] = in_array($input['device_button_size'], array('small', 'medium', 'large')) ? $input['device_button_size'] : 'medium';
        }

        if (isset($input['device_button_active_color'])) {
            $sanitized['device_button_active_color'] = sanitize_hex_color($input['device_button_active_color']);
        }

        if (isset($input['device_button_hover_color'])) {
            $sanitized['device_button_hover_color'] = sanitize_hex_color($input['device_button_hover_color']);
        }

        // Topbar settings
        if (isset($input['topbar_bg'])) {
            $sanitized['topbar_bg'] = sanitize_hex_color($input['topbar_bg']);
        }

        if (isset($input['topbar_title_size'])) {
            $sanitized['topbar_title_size'] = absint($input['topbar_title_size']);
        }

        if (isset($input['topbar_title_color'])) {
            $sanitized['topbar_title_color'] = sanitize_hex_color($input['topbar_title_color']);
        }

        if (isset($input['topbar_button_color'])) {
            $sanitized['topbar_button_color'] = sanitize_hex_color($input['topbar_button_color']);
        }

        if (isset($input['topbar_button_bg'])) {
            $sanitized['topbar_button_bg'] = sanitize_hex_color($input['topbar_button_bg']);
        }

        if (isset($input['topbar_button_font_size'])) {
            $sanitized['topbar_button_font_size'] = absint($input['topbar_button_font_size']);
        }

        if (isset($input['topbar_button_border_radius'])) {
            $sanitized['topbar_button_border_radius'] = absint($input['topbar_button_border_radius']);
        }

        // Topbar button padding
        $padding_fields = array('topbar_button_padding_top', 'topbar_button_padding_right', 'topbar_button_padding_bottom', 'topbar_button_padding_left');
        foreach ($padding_fields as $field) {
            if (isset($input[$field])) {
                $sanitized[$field] = absint($input[$field]);
            }
        }

        // Additional color settings
        if (isset($input['overlay_bg_color'])) {
            $sanitized['overlay_bg_color'] = sanitize_hex_color($input['overlay_bg_color']);
        }

        if (isset($input['overlay_border_color'])) {
            $sanitized['overlay_border_color'] = sanitize_hex_color($input['overlay_border_color']);
        }

        if (isset($input['frame_border_color'])) {
            $sanitized['frame_border_color'] = sanitize_hex_color($input['frame_border_color']);
        }

        if (isset($input['frame_shadow_color'])) {
            // Allow rgba values for shadow color
            $sanitized['frame_shadow_color'] = preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $input['frame_shadow_color']) ? $input['frame_shadow_color'] : sanitize_text_field($input['frame_shadow_color']);
        }

        if (isset($input['cta_button_color'])) {
            $sanitized['cta_button_color'] = sanitize_hex_color($input['cta_button_color']);
        }

        if (isset($input['cta_button_bg_color'])) {
            $sanitized['cta_button_bg_color'] = sanitize_hex_color($input['cta_button_bg_color']);
        }

        if (isset($input['cta_button_hover_color'])) {
            $sanitized['cta_button_hover_color'] = sanitize_hex_color($input['cta_button_hover_color']);
        }

        if (isset($input['device_button_text_color'])) {
            $sanitized['device_button_text_color'] = sanitize_hex_color($input['device_button_text_color']);
        }

        if (isset($input['device_button_bg_color'])) {
            $sanitized['device_button_bg_color'] = sanitize_hex_color($input['device_button_bg_color']);
        }

        // Overlay settings
        $checkbox_fields = array('overlay_close_on_click', 'overlay_close_on_esc', 'overlay_loading_indicator', 'preview_allow_zoom', 'lazy_load_preview', 'preload_previews', 'cache_previews', 'enable_keyboard_nav', 'enable_screen_reader', 'focus_outline');
        foreach ($checkbox_fields as $field) {
            $sanitized[$field] = isset($input[$field]) ? (bool) $input[$field] : false;
        }

        if (isset($input['overlay_loading_color'])) {
            $sanitized['overlay_loading_color'] = sanitize_hex_color($input['overlay_loading_color']);
        }

        // Preview settings
        if (isset($input['preview_start_with_device'])) {
            $sanitized['preview_start_with_device'] = in_array($input['preview_start_with_device'], array('desktop', 'tablet', 'mobile')) ? $input['preview_start_with_device'] : 'desktop';
        }

        if (isset($input['preview_zoom_level'])) {
            $sanitized['preview_zoom_level'] = floatval($input['preview_zoom_level']);
        }

        // Performance settings
        if (isset($input['cache_duration'])) {
            $sanitized['cache_duration'] = absint($input['cache_duration']);
        }

        // Accessibility settings
        if (isset($input['focus_outline_color'])) {
            $sanitized['focus_outline_color'] = sanitize_hex_color($input['focus_outline_color']);
        }

        if (isset($input['focus_outline_width'])) {
            $sanitized['focus_outline_width'] = absint($input['focus_outline_width']);
        }

        // Custom CSS/JS
        if (isset($input['custom_css'])) {
            $sanitized['custom_css'] = wp_kses_post($input['custom_css']);
        }

        if (isset($input['custom_js'])) {
            $sanitized['custom_js'] = wp_kses_post($input['custom_js']);
        }

        return $sanitized;
    }

    /**
     * Get plugin settings from WordPress options
     *
     * @return array Plugin settings
     */
    public static function get_settings() {
        $defaults = RTP_Advanced_Settings::get_defaults();
        $saved = get_option('rtp_settings', array());
        return wp_parse_args($saved, $defaults);
    }

    // Handle AJAX requests for import/export/reset
    public function handle_ajax_requests() {
        // Import settings
        if (isset($_POST['action']) && $_POST['action'] === 'rtp_import_settings') {
            check_ajax_referer('rtp_admin_nonce', 'rtp_import_nonce');

            if (! current_user_can('manage_options')) {
                wp_die('You do not have sufficient permissions');
            }

            if (isset($_POST['rtp_settings'])) {
                $settings = json_decode(stripslashes($_POST['rtp_settings']), true);

                if ($settings && is_array($settings)) {
                    $sanitized = RTP_Advanced_Settings::sanitize_settings($settings);
                    update_option('rtp_settings', $sanitized);

                    wp_redirect(add_query_arg(array(
                        'rtp-message' => 'Settings imported successfully!',
                        'rtp-type' => 'success'
                    ), admin_url('admin.php?page=rtp-settings')));
                    exit;
                }
            }

            wp_redirect(add_query_arg(array(
                'rtp-message' => 'Failed to import settings. Please check the file format.',
                'rtp-type' => 'error'
            ), admin_url('admin.php?page=rtp-settings')));
            exit;
        }

        // Export settings
        if (isset($_GET['action']) && $_GET['action'] === 'rtp_export_settings') {
            if (! current_user_can('manage_options')) {
                wp_die('You do not have sufficient permissions');
            }

            $settings = get_option('rtp_settings', array());
            $defaults = RTP_Advanced_Settings::get_defaults();
            $export_data = wp_parse_args($settings, $defaults);

            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="rtp-settings-' . date('Y-m-d') . '.json"');
            echo json_encode($export_data, JSON_PRETTY_PRINT);
            exit;
        }

        // Reset settings
        if (isset($_GET['action']) && $_GET['action'] === 'rtp_reset_settings') {
            if (! current_user_can('manage_options')) {
                wp_die('You do not have sufficient permissions');
            }

            delete_option('rtp_settings');

            wp_redirect(add_query_arg(array(
                'rtp-message' => 'Settings reset to defaults!',
                'rtp-type' => 'success'
            ), admin_url('admin.php?page=rtp-settings')));
            exit;
        }
    }
}