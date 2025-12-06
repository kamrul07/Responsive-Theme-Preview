<?php

if (! defined('ABSPATH')) {
    exit;
}

class RTP_Dashboard {

    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_dashboard_page'));
        add_action('admin_init', array($this, 'handle_actions'));
        add_action('admin_enqueue_scripts', array($this, 'dashboard_scripts'));

        // Register AJAX actions in constructor to ensure they're available
        add_action('wp_ajax_rtp_save_breakpoint', array($this, 'ajax_save_breakpoint'));
        add_action('wp_ajax_rtp_delete_breakpoint', array($this, 'ajax_delete_breakpoint'));
    }

    public function add_dashboard_page() {
        add_submenu_page(
            'rtp-previews',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'rtp-dashboard',
            array($this, 'create_dashboard_page')
        );
    }

    public function dashboard_scripts($hook) {
        // Check if we're on the dashboard page
        // Match any hook that contains 'rtp-dashboard' to be more flexible
        if (strpos($hook, 'rtp-dashboard') === false) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_media(); // Make sure media scripts are enqueued
        wp_enqueue_style('rtp-admin-styles', plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin-styles.css', array(), '1.0');
        wp_enqueue_style('rtp-dashboard', plugin_dir_url(dirname(__FILE__)) . 'assets/css/dashboard.css', array(), '1.0');
        wp_enqueue_script('rtp-dashboard', plugin_dir_url(dirname(__FILE__)) . 'assets/js/dashboard.js', array('jquery', 'wp-color-picker'), '1.0', true);

        wp_localize_script('rtp-dashboard', 'rtp_dashboard', array(
            'nonce' => wp_create_nonce('rtp_breakpoint_nonce'),
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
    }

    public function create_dashboard_page() {
        $this->options = get_option('rtp_settings', array());
        $stats = $this->get_preview_stats();
        $breakpoints = isset($this->options['default_breakpoints']) ? $this->options['default_breakpoints'] : RTP_Advanced_Settings::get_defaults()['default_breakpoints'];

        // Handle any success/error messages
        $message = '';
        if (isset($_GET['rtp-message'])) {
            $message = sanitize_text_field($_GET['rtp-message']);
            $type = isset($_GET['rtp-type']) ? sanitize_text_field($_GET['rtp-type']) : 'success';
        }
?>
<div class="wrap rtp-dashboard-wrap">
    <h1>Responsive Theme Preview Dashboard</h1>

    <?php if ($message): ?>
    <div class="notice notice-<?php echo esc_attr($type); ?> is-dismissible">
        <p><?php echo esc_html($message); ?></p>
    </div>
    <?php endif; ?>

    <!-- Stats Overview -->
    <div class="rtp-stats-grid">
        <div class="rtp-stat-card">
            <h3>Total Previews</h3>
            <div class="rtp-stat-number"><?php echo esc_html($stats['total']); ?></div>
            <a href="<?php echo admin_url('edit.php?post_type=rtp_preview'); ?>" class="rtp-stat-link">Manage Previews</a>
        </div>
        <div class="rtp-stat-card">
            <h3>Total Views</h3>
            <div class="rtp-stat-number"><?php echo esc_html(number_format($stats['total_views'])); ?></div>
            <span class="rtp-stat-desc">All time</span>
        </div>
        <div class="rtp-stat-card">
            <h3>Active Breakpoints</h3>
            <div class="rtp-stat-number"><?php echo esc_html(count($breakpoints)); ?></div>
            <a href="#breakpoints" class="rtp-stat-link">Manage Breakpoints</a>
        </div>
        <div class="rtp-stat-card">
            <h3>Settings Status</h3>
            <div class="rtp-stat-number"><?php echo empty($this->options) ? 'Default' : 'Custom'; ?></div>
            <a href="<?php echo admin_url('admin.php?page=rtp-settings'); ?>" class="rtp-stat-link">Configure</a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="rtp-quick-actions">
        <h2>Quick Actions</h2>
        <div class="rtp-action-buttons">
            <a href="<?php echo admin_url('post-new.php?post_type=rtp_preview'); ?>" class="button button-primary">
                <span class="dashicons dashicons-plus"></span> Add New Preview
            </a>
            <a href="<?php echo admin_url('admin.php?page=rtp-settings'); ?>" class="button">
                <span class="dashicons dashicons-admin-settings"></span> Settings
            </a>
            <button type="button" id="rtp-export-settings" class="button">
                <span class="dashicons dashicons-download"></span> Export Settings
            </button>
            <button type="button" id="rtp-import-settings" class="button">
                <span class="dashicons dashicons-upload"></span> Import Settings
            </button>
            <button type="button" id="rtp-reset-settings" class="button">
                <span class="dashicons dashicons-update"></span> Reset to Defaults
            </button>
        </div>
    </div>

    <!-- Import/Export Form (Hidden by default) -->
    <div id="rtp-import-export" style="display:none;">
        <div class="rtp-import-section">
            <h3>Import Settings</h3>
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('rtp_import_settings', 'rtp_import_nonce'); ?>
                <input type="file" name="rtp_settings_file" accept=".json" class="regular-text" />
                <p class="description">Upload a JSON file containing exported settings.</p>
                <input type="submit" name="rtp_import_submit" class="button button-secondary" value="Import Settings" />
            </form>
        </div>
    </div>

    <!-- Settings Overview -->
    <div class="rtp-settings-overview">
        <h2>Settings Overview</h2>
        <div class="rtp-settings-grid">
            <!-- General Settings -->
            <div class="rtp-setting-group">
                <h3><span class="dashicons dashicons-admin-generic"></span> General</h3>
                <div class="rtp-setting-item">
                    <label>Topbar Height:</label>
                    <span><?php echo esc_html(isset($this->options['topbar_height']) ? $this->options['topbar_height'] . 'px' : '52px'); ?></span>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div class="rtp-setting-group">
                <h3><span class="dashicons dashicons-admin-appearance"></span> Appearance</h3>
                <div class="rtp-setting-item">
                    <label>Topbar Background:</label>
                    <span class="rtp-color-preview" style="background-color:<?php echo esc_attr(isset($this->options['topbar_bg']) ? $this->options['topbar_bg'] : '#ffffff'); ?>;"></span>
                </div>
                <div class="rtp-setting-item">
                    <label>Device Button Style:</label>
                    <span><?php echo esc_html(isset($this->options['device_button_style']) ? ucfirst($this->options['device_button_style']) : 'Default'); ?></span>
                </div>
            </div>

            <!-- Overlay Settings -->
            <div class="rtp-setting-group">
                <h3><span class="dashicons dashicons-overlay"></span> Overlay</h3>
                <div class="rtp-setting-item">
                    <label>Close on Click:</label>
                    <span><?php echo isset($this->options['overlay_close_on_click']) && $this->options['overlay_close_on_click'] ? 'Enabled' : 'Disabled'; ?></span>
                </div>
                <div class="rtp-setting-item">
                    <label>Close on ESC:</label>
                    <span><?php echo isset($this->options['overlay_close_on_esc']) && $this->options['overlay_close_on_esc'] ? 'Enabled' : 'Disabled'; ?></span>
                </div>
            </div>

            <!-- Performance Settings -->
            <div class="rtp-setting-group">
                <h3><span class="dashicons dashicons-performance"></span> Performance</h3>
                <div class="rtp-setting-item">
                    <label>Lazy Load:</label>
                    <span><?php echo isset($this->options['lazy_load_preview']) && $this->options['lazy_load_preview'] ? 'Enabled' : 'Disabled'; ?></span>
                </div>
                <div class="rtp-setting-item">
                    <label>Cache Previews:</label>
                    <span><?php echo isset($this->options['cache_previews']) && $this->options['cache_previews'] ? 'Enabled' : 'Disabled'; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Breakpoints Management -->
    <div id="breakpoints" class="rtp-breakpoints-section">
        <h2>Device Breakpoints</h2>
        <div class="rtp-breakpoints-list">
            <?php foreach ($breakpoints as $index => $breakpoint): ?>
            <div class="rtp-breakpoint-item" data-icon="<?php echo esc_attr($breakpoint['icon']); ?>">
                <div class="rtp-breakpoint-info">
                    <h4><?php echo esc_html($breakpoint['title']); ?></h4>
                    <span class="rtp-breakpoint-width"><?php echo esc_html($breakpoint['width']); ?>px</span>
                </div>
                <div class="rtp-breakpoint-actions">
                    <button type="button" class="button rtp-edit-breakpoint" data-index="<?php echo esc_attr($index); ?>">Edit</button>
                    <button type="button" class="button rtp-delete-breakpoint" data-index="<?php echo esc_attr($index); ?>">Delete</button>
                    <button type="button" class="button rtp-delete-breakpoint-inline" data-index="<?php echo esc_attr($index); ?>" title="Delete this breakpoint">Delete</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="rtp-add-breakpoint" class="button button-primary">Add New Breakpoint</button>
    </div>

    <!-- Breakpoint Edit Modal (Hidden by default) -->
    <div id="rtp-breakpoint-modal" style="display:none;">
        <div class="rtp-modal-content">
            <h3>Edit Breakpoint</h3>
            <form id="rtp-breakpoint-form">
                <div class="rtp-form-row">
                    <label for="rtp-breakpoint-title">Title:</label>
                    <input type="text" id="rtp-breakpoint-title" name="title" class="regular-text" />
                </div>
                <div class="rtp-form-row">
                    <label for="rtp-breakpoint-width">Width (px):</label>
                    <input type="number" id="rtp-breakpoint-width" name="width" class="small-text" min="320" max="2560" />
                </div>
                <div class="rtp-form-row">
                    <label for="rtp-breakpoint-icon">Icon:</label>
                    <div class="rtp-icon-input-group">
                        <input type="text" id="rtp-breakpoint-icon" name="icon" class="regular-text" placeholder="data:image/svg+xml;base64,..." />
                        <button type="button" id="rtp-upload-icon" class="button">Upload Image</button>
                    </div>
                    <div id="rtp-icon-preview" class="rtp-icon-preview"></div>
                </div>
                <div class="rtp-form-row">
                    <button type="button" id="rtp-save-breakpoint" class="button button-primary">Save</button>
                    <button type="button" id="rtp-cancel-breakpoint" class="button">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
    }

    private function get_preview_stats() {
        $args = array(
            'post_type' => 'rtp_preview',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );

        $previews = get_posts($args);
        $total_views = 0;

        foreach ($previews as $preview) {
            $views = (int) get_post_meta($preview->ID, RTP_CPT::META_VIEWS, true);
            $total_views += $views;
        }

        return array(
            'total' => count($previews),
            'total_views' => $total_views,
        );
    }

    public function handle_actions() {
        // Handle import settings
        if (isset($_POST['rtp_import_submit']) && isset($_FILES['rtp_settings_file'])) {
            if (! wp_verify_nonce($_POST['rtp_import_nonce'], 'rtp_import_settings')) {
                wp_die('Security check failed');
            }

            if (! current_user_can('manage_options')) {
                wp_die('You do not have sufficient permissions');
            }

            $file = $_FILES['rtp_settings_file'];
            if ($file['error'] === UPLOAD_ERR_OK && $file['type'] === 'application/json') {
                $content = file_get_contents($file['tmp_name']);
                $settings = json_decode($content, true);

                if ($settings && is_array($settings)) {
                    $admin_settings = new RTP_Admin_Settings();
                    $sanitized = $admin_settings->sanitize_settings($settings);
                    update_option('rtp_settings', $sanitized);

                    wp_redirect(add_query_arg(array(
                        'rtp-message' => 'Settings imported successfully!',
                        'rtp-type' => 'success'
                    ), admin_url('admin.php?page=rtp-dashboard')));
                    exit;
                }
            }

            wp_redirect(add_query_arg(array(
                'rtp-message' => 'Failed to import settings. Please check the file format.',
                'rtp-type' => 'error'
            ), admin_url('admin.php?page=rtp-dashboard')));
            exit;
        }

        // Handle export settings
        if (isset($_GET['rtp_action']) && $_GET['rtp_action'] === 'export_settings') {
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

        // Handle reset settings
        if (isset($_GET['rtp_action']) && $_GET['rtp_action'] === 'reset_settings') {
            if (! current_user_can('manage_options')) {
                wp_die('You do not have sufficient permissions');
            }

            delete_option('rtp_settings');

            wp_redirect(add_query_arg(array(
                'rtp-message' => 'Settings reset to defaults!',
                'rtp-type' => 'success'
            ), admin_url('admin.php?page=rtp-dashboard')));
            exit;
        }
    }

    public function ajax_save_breakpoint() {
        check_ajax_referer('rtp_breakpoint_nonce', 'nonce');

        if (! current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions');
        }

        $index = isset($_POST['index']) ? (int) $_POST['index'] : -1;
        $title = sanitize_text_field($_POST['title']);
        $width = (int) $_POST['width'];
        $icon = sanitize_text_field($_POST['icon']);

        // Debug logging
        error_log('RTP Save Breakpoint - Index: ' . $index . ', Title: ' . $title . ', Width: ' . $width . ', Icon: ' . $icon);

        // Handle base64 image upload
        if (strpos($icon, 'data:image/') === 0) {
            // This is already a base64 encoded image, use as is
            $icon_data = $icon;
        } else {
            // This might be a regular URL, use as is
            $icon_data = $icon;
        }

        $settings = get_option('rtp_settings', array());
        $breakpoints = isset($settings['default_breakpoints']) ? $settings['default_breakpoints'] : RTP_Advanced_Settings::get_defaults()['default_breakpoints'];

        $new_breakpoint = array(
            'title' => $title,
            'width' => $width,
            'icon' => $icon_data,
        );

        if ($index >= 0 && $index < count($breakpoints)) {
            $breakpoints[$index] = $new_breakpoint;
        } else {
            $breakpoints[] = $new_breakpoint;
        }

        $settings['default_breakpoints'] = $breakpoints;

        // Debug logging
        error_log('RTP Before Update - Settings: ' . print_r($settings, true));

        update_option('rtp_settings', $settings);

        // Debug logging
        error_log('RTP After Update - Settings: ' . print_r(get_option('rtp_settings', array()), true));

        wp_send_json_success(array(
            'message' => 'Breakpoint saved successfully!',
            'breakpoints' => $breakpoints,
            'debug_info' => array(
                'before_update' => $settings,
                'after_update' => get_option('rtp_settings', array()),
                'new_breakpoint' => $new_breakpoint
            )
        ));
    }

    public function ajax_delete_breakpoint() {
        check_ajax_referer('rtp_breakpoint_nonce', 'nonce');

        if (! current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions');
        }

        $index = (int) $_POST['index'];

        $settings = get_option('rtp_settings', array());

        $breakpoints = isset($settings['default_breakpoints']) ? $settings['default_breakpoints'] : RTP_Advanced_Settings::get_defaults()['default_breakpoints'];
        var_dump($settings['default_breakpoints']);
        if ($index >= 0 && $index < count($breakpoints)) {
            array_splice($breakpoints, $index, 1);
            $settings['default_breakpoints'] = $breakpoints;
            update_option('rtp_settings', $settings);

            wp_send_json_success(array(
                'message' => 'Breakpoint deleted successfully!',
                'breakpoints' => $breakpoints
            ));
        }

        wp_send_json_error('Invalid breakpoint index');
    }
}