<?php
if (! defined('ABSPATH')) {
    exit;
}

class RTP_Admin_Settings {

    /**
     * Settings page slug
     */
    const PAGE_SLUG = 'rtp-settings';

    /**
     * Settings option name
     */
    const OPTION_NAME = 'rtp_settings';

    /**
     * Initialize the admin settings
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_menu_page'));
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
    }

    /**
     * Add menu page
     */
    public static function add_menu_page() {
        add_submenu_page(
            'edit.php?post_type=' . RTP_CPT::POST_TYPE,
            __('Settings', 'responsive-theme-preview'),
            __('Settings', 'responsive-theme-preview'),
            'manage_options',
            self::PAGE_SLUG,
            array(__CLASS__, 'render_settings_page')
        );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public static function enqueue_scripts($hook) {
        if (strpos($hook, self::PAGE_SLUG) === false) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('rtp-admin-settings', RTP_URL . 'assets/js/admin-settings.js', array('jquery'), RTP_VER, true);

        // Enqueue Themify Icons for admin settings
        wp_enqueue_style('rtp-themify-icons', 'https://cdn.jsdelivr.net/npm/themify-icons@0.1.2/themify-icons.css', array(), '0.1.2');

        // Pass option name and nonce to JavaScript
        wp_localize_script('rtp-admin-settings', 'rtpSettings', array(
            'optionName' => self::OPTION_NAME,
            'nonce' => wp_create_nonce('rtp_save_settings'),
        ));

        // Add custom admin styles
        wp_add_inline_style('wp-color-picker', '
   .rtp-settings-section {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    margin: 20px 0;
    padding: 20px;
   }
   .rtp-settings-section h3 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
   }
   .rtp-settings-field {
    margin-bottom: 15px;
   }
   .rtp-settings-field label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
   }
   .rtp-settings-field .description {
    font-size: 12px;
    color: #666;
    font-style: italic;
    margin-top: 5px;
   }
   .rtp-settings-checkbox {
    margin-right: 8px;
   }
   .rtp-settings-tabs {
    margin-bottom: 20px;
   }
   .rtp-settings-tabs .nav-tab {
    margin-right: 5px;
   }
   .rtp-tab-content {
    display: none;
   }
   .rtp-tab-content.active {
    display: block;
   }
   .rtp-color-picker {
    width: 80px;
   }
   .rtp-number-field {
    width: 80px;
   }
   .rtp-breakpoint-item {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 15px;
    background: #f9f9f9;
   }
   .rtp-breakpoint-fields {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
   }
   .rtp-field-row {
    flex: 1;
    min-width: 200px;
   }
   .rtp-field-row label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
   }
   .rtp-field-row input {
    width: 100%;
   }
   .rtp-breakpoint-actions {
   	margin-top: 10px;
   	text-align: right;
   }
   .rtp-media-uploader {
   	position: relative;
   	display: flex;
   	align-items: center;
   	gap: 10px;
   }
   .rtp-media-input {
   	flex: 1;
   }
   .rtp-media-upload-button {
   	flex-shrink: 0;
   }
   .rtp-media-preview {
   	flex-shrink: 0;
   }
   .rtp-icon-picker-modal {
   	display: none;
   	position: fixed;
   	top: 0;
   	left: 0;
   	width: 100%;
   	height: 100%;
   	background: rgba(0, 0, 0, 0.5);
   	z-index: 100000;
   }
   .rtp-icon-picker-content {
   	position: absolute;
   	top: 50%;
   	left: 50%;
   	transform: translate(-50%, -50%);
   	background: #fff;
   	padding: 20px;
   	border-radius: 5px;
   	width: 80%;
   	max-width: 600px;
   	max-height: 80%;
   	overflow-y: auto;
   }
   .rtp-icon-picker-header {
   	display: flex;
   	justify-content: space-between;
   	align-items: center;
   	margin-bottom: 20px;
   	padding-bottom: 10px;
   	border-bottom: 1px solid #eee;
   }
   .rtp-icon-picker-search {
   	width: 100%;
   	padding: 8px;
   	margin-bottom: 20px;
   	border: 1px solid #ddd;
   	border-radius: 4px;
   }
   .rtp-icon-grid {
   	display: grid;
   	grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
   	gap: 10px;
   }
   .rtp-icon-item {
   	display: flex;
   	flex-direction: column;
   	align-items: center;
   	justify-content: center;
   	padding: 10px;
   	border: 1px solid #ddd;
   	border-radius: 4px;
   	cursor: pointer;
   	height: 60px;
   	font-size: 10px;
   }
   .rtp-icon-item i {
   	font-size: 20px;
   	margin-bottom: 5px;
   }
   .rtp-icon-item span {
   	text-align: center;
   	word-break: break-all;
   	line-height: 1;
   }
   .rtp-icon-item:hover {
   	background: #f0f0f0;
   	border-color: #0073aa;
   }
   .rtp-icon-item.selected {
   	background: #0073aa;
   	color: white;
   	border-color: #0073aa;
   }
  ');

        // Add custom admin script
        wp_add_inline_script('wp-color-picker', '
            jQuery(document).ready(function($) {
                // Initialize color pickers
                $(".rtp-color-picker").wpColorPicker();
                
                // Tab functionality
                $(".rtp-settings-tabs .nav-tab").click(function(e) {
                    e.preventDefault();
                    var tab = $(this).data("tab");
                    
                    $(".rtp-settings-tabs .nav-tab").removeClass("nav-tab-active");
                    $(this).addClass("nav-tab-active");
                    
                    $(".rtp-tab-content").removeClass("active");
                    $("#" + tab).addClass("active");
                });
                
                // Activate first tab by default
                $(".rtp-settings-tabs .nav-tab:first").click();
            });
        ');
    }


    /**
     * Register settings
     */
    public static function register_settings() {
        register_setting(self::OPTION_NAME, self::OPTION_NAME, array(__CLASS__, 'sanitize_settings'));
    }

    /**
     * Sanitize settings
     */
    public static function sanitize_settings($settings) {
        if (! is_array($settings)) {
            return array();
        }

        $defaults = RTP_Advanced_Settings::get_defaults();
        $sanitized = array();

        foreach ($defaults as $key => $default) {
            $value = isset($settings[$key]) ? $settings[$key] : $default;

            switch ($key) {
                // Boolean values
                case 'enable_animation':
                case 'frame_border':
                case 'frame_shadow':
                case 'topbar_sticky':
                case 'topbar_hide_on_scroll':
                case 'overlay_close_on_click':
                case 'overlay_close_on_esc':
                case 'overlay_loading_indicator':
                case 'preview_allow_zoom':
                case 'lazy_load_preview':
                case 'preload_previews':
                case 'cache_previews':
                case 'enable_keyboard_nav':
                case 'enable_screen_reader':
                case 'focus_outline':
                case 'debug_mode':
                case 'log_events':
                    $sanitized[$key] = (bool) $value;
                    break;

                // Integer values
                case 'animation_duration':
                case 'frame_border_width':
                case 'frame_border_radius':
                case 'frame_shadow_blur':
                case 'frame_shadow_offset':
                case 'topbar_height':
                case 'topbar_title_size':
                case 'cache_duration':
                case 'focus_outline_width':
                    $sanitized[$key] = (int) $value;
                    break;

                // Float values
                case 'preview_zoom_level':
                    $sanitized[$key] = (float) $value;
                    break;

                // Color values
                case 'frame_border_color':
                case 'frame_shadow_color':
                case 'device_button_active_color':
                case 'device_button_hover_color':
                case 'overlay_loading_color':
                case 'focus_outline_color':
                case 'cta_button_bg_color':
                case 'cta_button_color':
                    $sanitized[$key] = sanitize_hex_color($value);
                    break;

                // Text values with specific options
                case 'animation_easing':
                    $sanitized[$key] = in_array($value, array('ease', 'ease-in', 'ease-out', 'ease-in-out', 'linear')) ? $value : $default;
                    break;

                case 'device_button_style':
                    $sanitized[$key] = in_array($value, array('default', 'rounded', 'square')) ? $value : $default;
                    break;

                case 'device_button_size':
                    $sanitized[$key] = in_array($value, array('small', 'medium', 'large')) ? $value : $default;
                    break;

                case 'preview_start_with_device':
                    $sanitized[$key] = in_array($value, array('desktop', 'tablet', 'mobile')) ? $value : $default;
                    break;

                case 'topbar_title_weight':
                    $sanitized[$key] = in_array($value, array('100', '200', '300', '400', '500', '600', '700', '800', '900', 'normal', 'bold')) ? $value : $default;
                    break;

                case 'custom_css':
                case 'custom_js':
                    $sanitized[$key] = wp_kses_post($value);
                    break;

                case 'default_breakpoints':
                    if (is_array($value)) {
                        $sanitized[$key] = array();
                        foreach ($value as $index => $breakpoint) {
                            if (is_array($breakpoint)) {
                                $sanitized[$key][$index] = array(
                                    'title' => isset($breakpoint['title']) ? sanitize_text_field($breakpoint['title']) : '',
                                    'width' => isset($breakpoint['width']) ? (int) $breakpoint['width'] : 1280,
                                    'icon' => isset($breakpoint['icon']) ? sanitize_text_field($breakpoint['icon']) : '',
                                );
                            } else {
                                $sanitized[$key][$index] = $default;
                            }
                        }
                    } else {
                        $sanitized[$key] = $default;
                    }
                    break;

                default:
                    $sanitized[$key] = sanitize_text_field($value);
                    break;
            }
        }

        return $sanitized;
    }

    /**
     * Get current settings
     */
    public static function get_settings() {
        $settings = get_option(self::OPTION_NAME, array());
        return wp_parse_args($settings, RTP_Advanced_Settings::get_defaults());
    }

    /**
     * Render settings page
     */
    public static function render_settings_page() {
        $settings = self::get_settings();
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
?>
<div class="wrap">
    <h1><?php esc_html_e('Responsive Theme Preview Settings', 'responsive-theme-preview'); ?></h1>

    <?php settings_errors(); ?>

    <nav class="nav-tab-wrapper rtp-settings-tabs">
        <a href="#" class="nav-tab" data-tab="general-tab"><?php esc_html_e('General', 'responsive-theme-preview'); ?></a>
        <a href="#" class="nav-tab" data-tab="appearance-tab"><?php esc_html_e('Appearance', 'responsive-theme-preview'); ?></a>
        <a href="#" class="nav-tab" data-tab="performance-tab"><?php esc_html_e('Performance', 'responsive-theme-preview'); ?></a>
        <a href="#" class="nav-tab" data-tab="accessibility-tab"><?php esc_html_e('Accessibility', 'responsive-theme-preview'); ?></a>
        <a href="#" class="nav-tab" data-tab="filtering-tab"><?php esc_html_e('Filtering', 'responsive-theme-preview'); ?></a>
        <a href="#" class="nav-tab" data-tab="advanced-tab"><?php esc_html_e('Advanced', 'responsive-theme-preview'); ?></a>
    </nav>

    <form method="post" action="options.php" id="rtp-settings-form">
        <?php settings_fields(self::OPTION_NAME); ?>

        <!-- General Settings Tab -->
        <div id="general-tab" class="rtp-tab-content">
            <div class="rtp-settings-section">
                <h3><?php esc_html_e('Preview Settings', 'responsive-theme-preview'); ?></h3>

                <div class="rtp-settings-field">
                    <label for="preview_start_with_device"><?php esc_html_e('Start With Device', 'responsive-theme-preview'); ?></label>
                    <select id="preview_start_with_device" name="<?php echo esc_attr(self::OPTION_NAME); ?>[preview_start_with_device]">
                        <option value="desktop" <?php selected($settings['preview_start_with_device'], 'desktop'); ?>><?php esc_html_e('Desktop', 'responsive-theme-preview'); ?></option>
                        <option value="tablet" <?php selected($settings['preview_start_with_device'], 'tablet'); ?>><?php esc_html_e('Tablet', 'responsive-theme-preview'); ?></option>
                        <option value="mobile" <?php selected($settings['preview_start_with_device'], 'mobile'); ?>><?php esc_html_e('Mobile', 'responsive-theme-preview'); ?></option>
                    </select>
                </div>

                <div class="rtp-settings-field">
                    <label for="preview_zoom_level"><?php esc_html_e('Default Zoom Level', 'responsive-theme-preview'); ?></label>
                    <input type="number" id="preview_zoom_level" name="<?php echo esc_attr(self::OPTION_NAME); ?>[preview_zoom_level]" value="<?php echo esc_attr($settings['preview_zoom_level']); ?>" class="rtp-number-field" min="0.5" max="2.0" step="0.1" />
                    <p class="description"><?php esc_html_e('Default zoom level for previews (1.0 = 100%).', 'responsive-theme-preview'); ?></p>
                </div>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[preview_allow_zoom]" value="1" <?php checked($settings['preview_allow_zoom']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Allow Zoom Controls', 'responsive-theme-preview'); ?>
                    </label>
                </div>

                <div class="rtp-settings-section">
                    <h3><?php esc_html_e('Default Breakpoints', 'responsive-theme-preview'); ?></h3>

                    <div class="rtp-settings-field">
                        <p><?php esc_html_e('Configure the default device breakpoints for preview.', 'responsive-theme-preview'); ?></p>
                        <div id="rtp-breakpoints-container">
                            <?php
                                    $breakpoints = $settings['default_breakpoints'];
                                    if (empty($breakpoints)) {
                                        $breakpoints = array(
                                            array('title' => 'Desktop', 'width' => 1280, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE4IDJINmMtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMmMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE2SDZWNmgxMnYxMnpNOSA4aDZ2Mkg5Vjh6bTAgNGg2djJIOXYtMnptMCA0aDZ2Mkg5di0yeiIvPjwvc3ZnPg=='),
                                            array('title' => 'Tablet', 'width' => 768, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE5IDFINVYyM2gxNFYxem0wIDIySDVWM2gxNHYyMHpNOSAxOWg2djJIOXYtMnptMC0xNGg2djEwSDlWNXoiLz48L3N2Zz4='),
                                            array('title' => 'Mobile', 'width' => 375, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE3IDJIN2MtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMGMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE4SDdWNmgxMHYxNHptLTUtMTJoMnY4aC0yVjh6Ii8+PC9zdmc+'),
                                        );
                                    }

                                    foreach ($breakpoints as $index => $breakpoint) :
                                    ?>
                            <div class="rtp-breakpoint-item" data-index="<?php echo esc_attr($index); ?>">
                                <div class="rtp-breakpoint-fields">
                                    <div class="rtp-field-row">
                                        <label><?php esc_html_e('Title', 'responsive-theme-preview'); ?></label>
                                        <input type="text" name="<?php echo esc_attr(self::OPTION_NAME); ?>[default_breakpoints][<?php echo esc_attr($index); ?>][title]" value="<?php echo esc_attr($breakpoint['title']); ?>" placeholder="<?php esc_attr_e('Desktop', 'responsive-theme-preview'); ?>" />
                                    </div>
                                    <div class="rtp-field-row">
                                        <label><?php esc_html_e('Width (px)', 'responsive-theme-preview'); ?></label>
                                        <input type="number" name="<?php echo esc_attr(self::OPTION_NAME); ?>[default_breakpoints][<?php echo esc_attr($index); ?>][width]" value="<?php echo esc_attr($breakpoint['width']); ?>" min="320" max="2560" />
                                    </div>
                                    <div class="rtp-field-row">
                                        <label><?php esc_html_e('Icon Image', 'responsive-theme-preview'); ?></label>
                                        <div class="rtp-media-uploader">
                                            <input type="text" class="rtp-media-input" name="<?php echo esc_attr(self::OPTION_NAME); ?>[default_breakpoints][<?php echo esc_attr($index); ?>][icon]" value="<?php echo esc_attr($breakpoint['icon']); ?>" placeholder="<?php esc_attr_e('Enter image URL or click to upload', 'responsive-theme-preview'); ?>" />
                                            <button type="button" class="button rtp-media-upload-button" data-target="<?php echo esc_attr(self::OPTION_NAME); ?>[default_breakpoints][<?php echo (int) $index; ?>][icon]"><?php esc_html_e('Upload', 'responsive-theme-preview'); ?></button>
                                            <?php if (!empty($breakpoint['icon'])) : ?>
                                            <div class="rtp-media-preview">
                                                <img src="<?php echo esc_url($breakpoint['icon']); ?>" alt="<?php esc_attr_e('Icon Preview', 'responsive-theme-preview'); ?>" style="max-width: 30px; max-height: 30px; vertical-align: middle;" />
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="rtp-breakpoint-actions">
                                    <button type="button" class="button rtp-remove-breakpoint"><?php esc_html_e('Remove', 'responsive-theme-preview'); ?></button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" id="rtp-add-breakpoint" class="button"><?php esc_html_e('Add Breakpoint', 'responsive-theme-preview'); ?></button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Appearance Settings Tab -->
        <div id="appearance-tab" class="rtp-tab-content">

            <div class="rtp-settings-section">
                <h3><?php esc_html_e('Device Button Settings', 'responsive-theme-preview'); ?></h3>

                <div class="rtp-settings-field">
                    <label for="device_button_style"><?php esc_html_e('Button Style', 'responsive-theme-preview'); ?></label>
                    <select id="device_button_style" name="<?php echo esc_attr(self::OPTION_NAME); ?>[device_button_style]">
                        <option value="default" <?php selected($settings['device_button_style'], 'default'); ?>><?php esc_html_e('Default', 'responsive-theme-preview'); ?></option>
                        <option value="rounded" <?php selected($settings['device_button_style'], 'rounded'); ?>><?php esc_html_e('Rounded', 'responsive-theme-preview'); ?></option>
                        <option value="square" <?php selected($settings['device_button_style'], 'square'); ?>><?php esc_html_e('Square', 'responsive-theme-preview'); ?></option>
                    </select>
                </div>

                <div class="rtp-settings-field">
                    <label for="device_button_size"><?php esc_html_e('Button Size', 'responsive-theme-preview'); ?></label>
                    <select id="device_button_size" name="<?php echo esc_attr(self::OPTION_NAME); ?>[device_button_size]">
                        <option value="small" <?php selected($settings['device_button_size'], 'small'); ?>><?php esc_html_e('Small', 'responsive-theme-preview'); ?></option>
                        <option value="medium" <?php selected($settings['device_button_size'], 'medium'); ?>><?php esc_html_e('Medium', 'responsive-theme-preview'); ?></option>
                        <option value="large" <?php selected($settings['device_button_size'], 'large'); ?>><?php esc_html_e('Large', 'responsive-theme-preview'); ?></option>
                    </select>
                </div>

                <div class="rtp-settings-field">
                    <label for="device_button_active_color"><?php esc_html_e('Button background', 'responsive-theme-preview'); ?></label>
                    <input type="text" id="device_button_active_color" name="<?php echo esc_attr(self::OPTION_NAME); ?>[device_button_active_color]" value="<?php echo esc_attr($settings['device_button_active_color']); ?>" class="rtp-color-picker" />
                </div>

                <div class="rtp-settings-field">
                    <label for="device_button_hover_color"><?php esc_html_e('Button Color', 'responsive-theme-preview'); ?></label>
                    <input type="text" id="device_button_hover_color" name="<?php echo esc_attr(self::OPTION_NAME); ?>[device_button_hover_color]" value="<?php echo esc_attr($settings['device_button_hover_color']); ?>" class="rtp-color-picker" />
                </div>
            </div>

            <div class="rtp-settings-section">
                <h3><?php esc_html_e('Topbar Settings', 'responsive-theme-preview'); ?></h3>

                <div class="rtp-settings-field">
                    <label for="topbar_height"><?php esc_html_e('Topbar Height (px)', 'responsive-theme-preview'); ?></label>
                    <input type="number" id="topbar_height" name="<?php echo esc_attr(self::OPTION_NAME); ?>[topbar_height]" value="<?php echo esc_attr($settings['topbar_height']); ?>" class="rtp-number-field" min="40" max="100" />
                </div>

                <div class="rtp-settings-field">
                    <label for="topbar_title_size"><?php esc_html_e('Title Font Size (px)', 'responsive-theme-preview'); ?></label>
                    <input type="number" id="topbar_title_size" name="<?php echo esc_attr(self::OPTION_NAME); ?>[topbar_title_size]" value="<?php echo esc_attr($settings['topbar_title_size']); ?>" class="rtp-number-field" min="10" max="24" />
                </div>

                <div class="rtp-settings-field">
                    <label for="topbar_title_weight"><?php esc_html_e('Title Font Weight', 'responsive-theme-preview'); ?></label>
                    <select id="topbar_title_weight" name="<?php echo esc_attr(self::OPTION_NAME); ?>[topbar_title_weight]">
                        <option value="100" <?php selected($settings['topbar_title_weight'], '100'); ?>><?php esc_html_e('Thin (100)', 'responsive-theme-preview'); ?></option>
                        <option value="200" <?php selected($settings['topbar_title_weight'], '200'); ?>><?php esc_html_e('Extra Light (200)', 'responsive-theme-preview'); ?></option>
                        <option value="300" <?php selected($settings['topbar_title_weight'], '300'); ?>><?php esc_html_e('Light (300)', 'responsive-theme-preview'); ?></option>
                        <option value="400" <?php selected($settings['topbar_title_weight'], '400'); ?>><?php esc_html_e('Normal (400)', 'responsive-theme-preview'); ?></option>
                        <option value="500" <?php selected($settings['topbar_title_weight'], '500'); ?>><?php esc_html_e('Medium (500)', 'responsive-theme-preview'); ?></option>
                        <option value="600" <?php selected($settings['topbar_title_weight'], '600'); ?>><?php esc_html_e('Semi Bold (600)', 'responsive-theme-preview'); ?></option>
                        <option value="700" <?php selected($settings['topbar_title_weight'], '700'); ?>><?php esc_html_e('Bold (700)', 'responsive-theme-preview'); ?></option>
                        <option value="800" <?php selected($settings['topbar_title_weight'], '800'); ?>><?php esc_html_e('Extra Bold (800)', 'responsive-theme-preview'); ?></option>
                        <option value="900" <?php selected($settings['topbar_title_weight'], '900'); ?>><?php esc_html_e('Black (900)', 'responsive-theme-preview'); ?></option>
                        <option value="normal" <?php selected($settings['topbar_title_weight'], 'normal'); ?>><?php esc_html_e('Normal', 'responsive-theme-preview'); ?></option>
                        <option value="bold" <?php selected($settings['topbar_title_weight'], 'bold'); ?>><?php esc_html_e('Bold', 'responsive-theme-preview'); ?></option>
                    </select>
                </div>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[topbar_sticky]" value="1" <?php checked($settings['topbar_sticky']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Sticky Topbar', 'responsive-theme-preview'); ?>
                    </label>
                </div>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[topbar_hide_on_scroll]" value="1" <?php checked($settings['topbar_hide_on_scroll']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Hide on Scroll', 'responsive-theme-preview'); ?>
                    </label>
                </div>

                <div class="rtp-settings-field">
                    <label for="topbar_bg"><?php esc_html_e('Topbar Background Color', 'responsive-theme-preview'); ?></label>
                    <input type="text" id="topbar_bg" name="<?php echo esc_attr(self::OPTION_NAME); ?>[topbar_bg]" value="<?php echo esc_attr($settings['topbar_bg']); ?>" class="rtp-color-picker" />
                </div>
            </div>

            <div class="rtp-settings-section">
                <h3><?php esc_html_e('Overlay Settings', 'responsive-theme-preview'); ?></h3>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[overlay_close_on_click]" value="1" <?php checked($settings['overlay_close_on_click']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Close on Click Outside', 'responsive-theme-preview'); ?>
                    </label>
                </div>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[overlay_close_on_esc]" value="1" <?php checked($settings['overlay_close_on_esc']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Close on ESC Key', 'responsive-theme-preview'); ?>
                    </label>
                </div>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[overlay_loading_indicator]" value="1" <?php checked($settings['overlay_loading_indicator']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Show Loading Indicator', 'responsive-theme-preview'); ?>
                    </label>
                </div>

                <div class="rtp-settings-field">
                    <label for="overlay_loading_color"><?php esc_html_e('Loading Indicator Color', 'responsive-theme-preview'); ?></label>
                    <input type="text" id="overlay_loading_color" name="<?php echo esc_attr(self::OPTION_NAME); ?>[overlay_loading_color]" value="<?php echo esc_attr($settings['overlay_loading_color']); ?>" class="rtp-color-picker" />
                </div>
            </div>

            <div class="rtp-settings-section">
                <h3><?php esc_html_e('CTA Button Settings', 'responsive-theme-preview'); ?></h3>

                <div class="rtp-settings-field">
                    <label for="cta_button_bg_color"><?php esc_html_e('CTA Button Background Color', 'responsive-theme-preview'); ?></label>
                    <input type="text" id="cta_button_bg_color" name="<?php echo esc_attr(self::OPTION_NAME); ?>[cta_button_bg_color]" value="<?php echo esc_attr($settings['cta_button_bg_color']); ?>" class="rtp-color-picker" />
                </div>

                <div class="rtp-settings-field">
                    <label for="cta_button_color"><?php esc_html_e('CTA Button Text Color', 'responsive-theme-preview'); ?></label>
                    <input type="text" id="cta_button_color" name="<?php echo esc_attr(self::OPTION_NAME); ?>[cta_button_color]" value="<?php echo esc_attr($settings['cta_button_color']); ?>" class="rtp-color-picker" />
                </div>
            </div>
        </div>

        <!-- Performance Settings Tab -->
        <div id="performance-tab" class="rtp-tab-content">
            <div class="rtp-settings-section">
                <h3><?php esc_html_e('Performance Settings', 'responsive-theme-preview'); ?></h3>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[lazy_load_preview]" value="1" <?php checked($settings['lazy_load_preview']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Lazy Load Previews', 'responsive-theme-preview'); ?>
                    </label>
                    <p class="description"><?php esc_html_e('Load preview content only when needed.', 'responsive-theme-preview'); ?></p>
                </div>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[preload_previews]" value="1" <?php checked($settings['preload_previews']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Preload Previews', 'responsive-theme-preview'); ?>
                    </label>
                    <p class="description"><?php esc_html_e('Preload preview content for faster access.', 'responsive-theme-preview'); ?></p>
                </div>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[cache_previews]" value="1" <?php checked($settings['cache_previews']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Cache Previews', 'responsive-theme-preview'); ?>
                    </label>
                    <p class="description"><?php esc_html_e('Cache preview content to improve performance.', 'responsive-theme-preview'); ?></p>
                </div>

                <div class="rtp-settings-field">
                    <label for="cache_duration"><?php esc_html_e('Cache Duration (seconds)', 'responsive-theme-preview'); ?></label>
                    <input type="number" id="cache_duration" name="<?php echo esc_attr(self::OPTION_NAME); ?>[cache_duration]" value="<?php echo esc_attr($settings['cache_duration']); ?>" class="rtp-number-field" min="300" max="86400" step="300" />
                    <p class="description"><?php esc_html_e('How long to cache preview content in seconds.', 'responsive-theme-preview'); ?></p>
                </div>
            </div>
        </div>

        <!-- Accessibility Settings Tab -->
        <div id="accessibility-tab" class="rtp-tab-content">
            <div class="rtp-settings-section">
                <h3><?php esc_html_e('Accessibility Settings', 'responsive-theme-preview'); ?></h3>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[enable_keyboard_nav]" value="1" <?php checked($settings['enable_keyboard_nav']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Enable Keyboard Navigation', 'responsive-theme-preview'); ?>
                    </label>
                    <p class="description"><?php esc_html_e('Allow keyboard navigation through preview controls.', 'responsive-theme-preview'); ?></p>
                </div>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[enable_screen_reader]" value="1" <?php checked($settings['enable_screen_reader']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Enable Screen Reader Support', 'responsive-theme-preview'); ?>
                    </label>
                    <p class="description"><?php esc_html_e('Add ARIA labels for screen readers.', 'responsive-theme-preview'); ?></p>
                </div>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[focus_outline]" value="1" <?php checked($settings['focus_outline']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Show Focus Outline', 'responsive-theme-preview'); ?>
                    </label>
                </div>

                <div class="rtp-settings-field">
                    <label for="focus_outline_color"><?php esc_html_e('Focus Outline Color', 'responsive-theme-preview'); ?></label>
                    <input type="text" id="focus_outline_color" name="<?php echo esc_attr(self::OPTION_NAME); ?>[focus_outline_color]" value="<?php echo esc_attr($settings['focus_outline_color']); ?>" class="rtp-color-picker" />
                </div>

                <div class="rtp-settings-field">
                    <label for="focus_outline_width"><?php esc_html_e('Focus Outline Width (px)', 'responsive-theme-preview'); ?></label>
                    <input type="number" id="focus_outline_width" name="<?php echo esc_attr(self::OPTION_NAME); ?>[focus_outline_width]" value="<?php echo esc_attr($settings['focus_outline_width']); ?>" class="rtp-number-field" min="1" max="5" />
                </div>
            </div>
        </div>

        <!-- Advanced Settings Tab -->
        <div id="advanced-tab" class="rtp-tab-content">
            <div class="rtp-settings-section">
                <h3><?php esc_html_e('Developer Settings', 'responsive-theme-preview'); ?></h3>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[debug_mode]" value="1" <?php checked($settings['debug_mode']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Debug Mode', 'responsive-theme-preview'); ?>
                    </label>
                    <p class="description"><?php esc_html_e('Enable debug mode for development.', 'responsive-theme-preview'); ?></p>
                </div>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[log_events]" value="1" <?php checked($settings['log_events']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Log Events', 'responsive-theme-preview'); ?>
                    </label>
                    <p class="description"><?php esc_html_e('Log plugin events to the console.', 'responsive-theme-preview'); ?></p>
                </div>
            </div>

            <div class="rtp-settings-section">
                <h3><?php esc_html_e('Custom Code', 'responsive-theme-preview'); ?></h3>

                <div class="rtp-settings-field">
                    <label for="custom_css"><?php esc_html_e('Custom CSS', 'responsive-theme-preview'); ?></label>
                    <textarea id="custom_css" name="<?php echo esc_attr(self::OPTION_NAME); ?>[custom_css]" rows="10" class="large-text"><?php echo esc_textarea($settings['custom_css']); ?></textarea>
                    <p class="description"><?php esc_html_e('Add custom CSS to override default styles.', 'responsive-theme-preview'); ?></p>
                </div>

                <div class="rtp-settings-field">
                    <label for="custom_js"><?php esc_html_e('Custom JavaScript', 'responsive-theme-preview'); ?></label>
                    <textarea id="custom_js" name="<?php echo esc_attr(self::OPTION_NAME); ?>[custom_js]" rows="10" class="large-text"><?php echo esc_textarea($settings['custom_js']); ?></textarea>
                    <p class="description"><?php esc_html_e('Add custom JavaScript for additional functionality.', 'responsive-theme-preview'); ?></p>
                </div>
            </div>

            <div class="rtp-settings-section">
                <h3><?php esc_html_e('Reset Settings', 'responsive-theme-preview'); ?></h3>

                <div class="rtp-settings-field">
                    <p><?php esc_html_e('If you want to reset all settings to their default values, click the button below.', 'responsive-theme-preview'); ?></p>
                    <button type="button" id="rtp-reset-settings" class="button button-secondary"><?php esc_html_e('Reset All Settings', 'responsive-theme-preview'); ?></button>
                </div>
            </div>
        </div>

        <!-- Filtering Settings Tab -->
        <div id="filtering-tab" class="rtp-tab-content">
            <div class="rtp-settings-section">
                <h3><?php esc_html_e('Preview Filtering', 'responsive-theme-preview'); ?></h3>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[enable_preview_filtering]" value="1" <?php checked($settings['enable_preview_filtering']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Enable Preview Filtering', 'responsive-theme-preview'); ?>
                    </label>
                    <p class="description"><?php esc_html_e('Enable filtering options for preview displays.', 'responsive-theme-preview'); ?></p>
                </div>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[filter_by_category]" value="1" <?php checked($settings['filter_by_category']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Filter by Category', 'responsive-theme-preview'); ?>
                    </label>
                    <p class="description"><?php esc_html_e('Allow filtering previews by category.', 'responsive-theme-preview'); ?></p>
                </div>

                <div class="rtp-settings-field">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_NAME); ?>[show_filter_count]" value="1" <?php checked($settings['show_filter_count']); ?> class="rtp-settings-checkbox" />
                        <?php esc_html_e('Show Filter Count', 'responsive-theme-preview'); ?>
                    </label>
                    <p class="description"><?php esc_html_e('Display the number of items in each filter category.', 'responsive-theme-preview'); ?></p>
                </div>
            </div>
        </div>

        <p class="submit">
            <button type="submit" id="rtp-save-settings" class="button button-primary"><?php esc_html_e('Save Changes', 'responsive-theme-preview'); ?></button>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('#rtp-reset-settings').on('click', function(e) {
        e.preventDefault();

        if (confirm('<?php esc_html_e('Are you sure you want to reset all settings to their default values? This action cannot be undone.', 'responsive-theme-preview'); ?>')) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'rtp_reset_settings',
                    nonce: '<?php echo esc_js(wp_create_nonce('rtp_reset_settings')); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('<?php esc_html_e('Error resetting settings. Please try again.', 'responsive-theme-preview'); ?>');
                    }
                },
                error: function() {
                    alert('<?php esc_html_e('Error resetting settings. Please try again.', 'responsive-theme-preview'); ?>');
                }
            });
        }
    });
});
</script>
<?php
    }
}

// Initialize the admin settings
RTP_Admin_Settings::init();

// Add AJAX handler for resetting settings
add_action('wp_ajax_rtp_reset_settings', function () {
    check_ajax_referer('rtp_reset_settings', 'nonce');

    if (! current_user_can('manage_options')) {
        wp_send_json_error();
    }

    delete_option(RTP_Admin_Settings::OPTION_NAME);
    wp_send_json_success();
});

// Add AJAX handler for saving settings
add_action('wp_ajax_rtp_save_settings', function () {
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Check nonce
    if (! check_ajax_referer('rtp_save_settings', 'nonce', false)) {
        wp_send_json_error(__('Security check failed.', 'responsive-theme-preview'));
    }

    // Check user capabilities
    if (! current_user_can('manage_options')) {
        wp_send_json_error(__('You do not have sufficient permissions to perform this action.', 'responsive-theme-preview'));
    }

    // Check if settings data is provided
    if (! isset($_POST['settings'])) {
        wp_send_json_error(__('No settings data provided.', 'responsive-theme-preview'));
    }

    $settings = $_POST['settings'];

    // Log the received settings for debugging
    error_log('RTP Settings received: ' . print_r($settings, true));

    // Sanitize settings using the existing sanitize method
    $sanitized_settings = RTP_Admin_Settings::sanitize_settings($settings);

    // Ensure default_breakpoints is properly formatted as array
    if (isset($sanitized_settings['default_breakpoints']) && is_array($sanitized_settings['default_breakpoints'])) {
        // Re-index the array to ensure sequential keys
        $sanitized_settings['default_breakpoints'] = array_values($sanitized_settings['default_breakpoints']);
    }

    // Log the sanitized settings for debugging
    error_log('RTP Settings sanitized: ' . print_r($sanitized_settings, true));

    // Update the option
    $updated = update_option(RTP_Admin_Settings::OPTION_NAME, $sanitized_settings);

    // Log the update result
    error_log('RTP Settings update result: ' . ($updated ? 'success' : 'failed'));

    if ($updated) {
        wp_send_json_success(array(
            'message' => __('Settings saved successfully!', 'responsive-theme-preview'),
            'settings' => $sanitized_settings
        ));
    } else {
        // Get the current settings to see if they're the same (no actual change)
        $current_settings = get_option(RTP_Admin_Settings::OPTION_NAME, array());
        if ($current_settings === $sanitized_settings) {
            wp_send_json_success(array(
                'message' => __('Settings saved successfully!', 'responsive-theme-preview'),
                'settings' => $sanitized_settings
            ));
        } else {
            wp_send_json_error(__('Failed to save settings. Please try again.', 'responsive-theme-preview'));
        }
    }
});