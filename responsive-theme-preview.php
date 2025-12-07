<?php
/*
Plugin Name: Responsive Theme Preview
Description: Preview templates in responsive frames (popup or separate page). Works with Elementor, Gutenberg, Bricks, and shortcode. Dynamic CPT source, view tracking, and customizable breakpoints.
Author: Kamrul Hasan
Text Domain: responsive-theme-preview
Domain Path: /languages
Version:         1.0.0
Tested up to:    6.8
Author URI:      mailto:kamrulhasanshuvo04@gmail.com
WC tested up to: 10.0
Requires PHP:    7.4
Elementor tested up to: 3.31
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (! defined('ABSPATH')) {
	exit;
}

define('RTP_VER', '3.11.1');
define('RTP_PATH', plugin_dir_path(__FILE__));
define('RTP_URL', plugin_dir_url(__FILE__));

add_action('plugins_loaded', function () {
	load_plugin_textdomain('responsive-theme-preview', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

add_action('wp_enqueue_scripts', function () {
	wp_register_style('rtp-front', RTP_URL . 'assets/css/front.css', array(), RTP_VER);
	wp_register_script('rtp-front', RTP_URL . 'assets/js/front.js', array(), RTP_VER, true);

	wp_enqueue_style('rtp-front');
	wp_enqueue_script('rtp-front');
}, 20);

require_once __DIR__ . '/includes/class-cpt.php';
require_once __DIR__ . '/includes/class-tracker.php';
require_once __DIR__ . '/includes/class-render.php';
require_once __DIR__ . '/includes/class-shortcode.php';
require_once __DIR__ . '/includes/class-block.php';
require_once __DIR__ . '/includes/class-advanced-settings.php';
require_once __DIR__ . '/includes/class-admin-settings.php';

add_action('elementor/widgets/register', function ($widgets_manager) {
	if (class_exists('\Elementor\Widget_Base')) {
		require_once __DIR__ . '/includes/class-elementor.php';
		if (class_exists('RTP_Elementor_Widget')) {
			$widgets_manager->register(new \RTP_Elementor_Widget());
		}
	}
}, 20);

add_action('init', function () {
	if (class_exists('\Bricks\Element') && class_exists('\Bricks\Elements')) {
		$element_files = [
			__DIR__ . '/includes/class-bricks.php',
		];

		foreach ($element_files as $file) {
			\Bricks\Elements::register_element($file);
		}
	}
}, 30);




add_action('enqueue_block_editor_assets', function () {
	wp_enqueue_script(
		'rtp-block-editor',
		RTP_URL . 'blocks/responsive-preview/editor.js',
		array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-block-editor', 'wp-i18n', 'wp-media-utils'),
		RTP_VER,
		true
	);
});

// Render a public single template for the Preview CPT permalink
add_action('template_redirect', function () {
	if (! is_singular(RTP_CPT::POST_TYPE)) {
		return;
	}
	$post_id = (int) get_the_ID();
	$title   = get_the_title($post_id);
	$url     = get_post_meta($post_id, RTP_CPT::META_URL, true);
	$url     = esc_url($url);

	// Get global settings for breakpoints
	$global_settings = array();
	if (class_exists('RTP_Admin_Settings')) {
		$global_settings = RTP_Admin_Settings::get_settings();
	} else {
		// Fallback to default if admin settings not available
		$global_settings = array(
			'default_breakpoints' => array(
				array('title' => 'Desktop', 'width' => 1280, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE4IDJINmMtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMmMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE2SDZWNmgxMnYxMnpNOSA4aDZ2Mkg5Vjh6bTAgNGg2djJIOXYtMnptMCA0aDZ2Mkg5di0yeiIvPjwvc3ZnPg=='),
				array('title' => 'Tablet',  'width' => 768, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE5IDFINVYyM2gxNFYxem0wIDIySDVWM2gxNHYyMHpNOSAxOWg2djJIOXYtMnptMC0xNGg2djEwSDlWNXoiLz48L3N2Zz4='),
				array('title' => 'Mobile', 'width' => 375, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE3IDJIN2MtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMGMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE4SDdWNmgxMHYxNHptLTUtMTJoMnY4aC0yVjh6Ii8+PC9zdmc+'),
			),
		);
	}

	// Use breakpoints from global settings if available, otherwise use defaults
	$bps = isset($global_settings['default_breakpoints']) && !empty($global_settings['default_breakpoints']) ? $global_settings['default_breakpoints'] : array(
		array('title' => 'Desktop', 'width' => 1280, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE4IDJINmMtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMmMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE2SDZWNmgxMnYxMnpNOSA4aDZ2Mkg5Vjh6bTAgNGg2djJIOXYtMnptMCA0aDZ2Mkg5di0yeiIvPjwvc3ZnPg=='),
		array('title' => 'Tablet',  'width' => 768, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE5IDFINVYyM2gxNFYxem0wIDIySDVWM2gxNHYyMHpNOSAxOWg2djJIOXYtMnptMC0xNGg2djEwSDlWNXoiLz48L3N2Zz4='),
		array('title' => 'Mobile', 'width' => 375, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE3IDJIN2MtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMGMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE4SDdWNmgxMHYxNHptLTUtMTJoMnY4aC0yVjh6Ii8+PC9zdmc+'),
	);

	$views = (int) get_post_meta($post_id, RTP_CPT::META_VIEWS, true);
	$views++;
	update_post_meta($post_id, RTP_CPT::META_VIEWS, $views);

	// Get global settings for single preview page
	$global_settings = array();
	if (class_exists('RTP_Admin_Settings')) {
		$global_settings = RTP_Admin_Settings::get_settings();
	} else {
		// Debug: Admin settings class not found, using defaults
		$global_settings = array(
			'topbar_height' => 52,
			'device_button_active_color' => '#2563eb',
			'device_button_hover_color' => '#1d4ed8',
			'overlay_close_on_click' => true,
			'overlay_close_on_esc' => true,
			'overlay_loading_indicator' => true,
			'overlay_loading_color' => '#2563eb',
			'enable_keyboard_nav' => true,
			'focus_outline' => true,
			'focus_outline_color' => '#2563eb',
			'focus_outline_width' => 2,
		);
	}

	// Debug output
	error_log('RTP Global Settings: ' . print_r($global_settings, true));

	status_header(200);
	nocache_headers();

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo esc_html($title ? $title : __('Preview', 'responsive-theme-preview')); ?></title>
    <link rel="stylesheet" href="<?php echo esc_url(RTP_URL . 'assets/css/front.css'); ?>?ver=<?php echo esc_attr(RTP_VER); ?>" />
    <style>
    .rtp-overlay {
        display: block;
    }

    .rtp-topbar .rtp-devices button {
        background: <?php echo $global_settings['device_button_hover_color'];
        ?> !important;
        color: <?php echo $global_settings['device_button_active_color'];
        ?> !important;
    }

    .rtp-topbar .rtp-devices button.active {
        background: <?php echo $global_settings['device_button_active_color'];
        ?> !important;
    }

    .rtp-topbar .rtp-topbar-title {
        font-size: <?php echo $global_settings['topbar_title_size'];
        ?>px !important;
    }

    .rtp-topbar .rtp-cta {
        background: <?php echo $global_settings['cta_button_bg_color'];
        ?> !important;
        color: <?php echo $global_settings['cta_button_color'];
        ?> !important;
    }
    </style>
    <?php wp_head(); ?>
</head>

<body <?php body_class('rtp-preview-page'); ?>>
    <div class="rtp-wrapper">
        <div class="rtp-topbar" style="background:<?php echo $global_settings['topbar_bg']; ?>;height: <?php echo (int) $global_settings['topbar_height']; ?>px; ">

            <div class="rtp-topbar-title"><?php echo esc_html($title); ?></div>
            <div class="rtp-devices">
                <?php foreach ($bps as $bp) :
						$w = isset($bp['width']) ? (int) $bp['width'] : 1280;
						$t = isset($bp['title']) ? sanitize_text_field($bp['title']) : '';
						$iconVal = isset($bp['icon']) ? $bp['icon'] : '';
						$iconUrl = '';
						$iconClass = '';

						// Handle different icon formats - now primarily image URLs
						if (is_array($iconVal)) {
							if (!empty($iconVal['url'])) {
								// Image icon format: {url: 'https://...'}
								$iconUrl = esc_url($iconVal['url']);
							} elseif (!empty($iconVal['src'])) {
								// Alternative image format: {src: 'https://...'}
								$iconUrl = esc_url($iconVal['src']);
							} elseif (!empty($iconVal['icon'])) {
								// Nested icon format: {icon: 'https://...'}
								if (preg_match('/^https?:\/\//', $iconVal['icon'])) {
									$iconUrl = esc_url($iconVal['icon']);
								} else {
									$iconClass = esc_attr($iconVal['icon']);
								}
							}
						} elseif (is_string($iconVal) && preg_match('/^https?:\/\//', $iconVal)) {
							// Image URL format
							$iconUrl = esc_url($iconVal);
						} elseif (is_string($iconVal) && !empty($iconVal)) {
							// For backward compatibility - check if it's an icon class or image URL
							if (preg_match('/^https?:\/\//', $iconVal)) {
								$iconUrl = esc_url($iconVal);
							} else {
								// Fallback to icon class for backward compatibility
								$iconClass = esc_attr($iconVal);
							}
						}
					?>
                <button data-w="<?php echo (int) $w; ?>px" title="<?php echo esc_attr($t); ?>">
                    <?php if ($iconUrl) : ?>
                    <img class="rtp-ic" src="<?php echo $iconUrl; ?>" alt="<?php echo esc_attr($t); ?>" />
                    <?php elseif ($iconClass) : ?>
                    <i class="rtp-ic <?php echo $iconClass; ?>"></i>
                    <?php else : ?>
                    <span class="rtp-ic"><?php echo esc_html($t ? substr($t, 0, 1) : '•'); ?></span>
                    <?php endif; ?>
                </button>
                <?php endforeach; ?>
            </div>
            <a class="rtp-cta" href="<?php echo $url ? $url : '#'; ?>" target="_blank" rel="noopener"><?php esc_html_e('Open Live', 'responsive-theme-preview'); ?></a>
        </div>
        <div class="rtp-framewrap" style="width:100%;top: <?php echo (int) $global_settings['topbar_height']; ?>px">
            <iframe id="rtp-frame" src="<?php echo $url; ?>" style="width:100%;height:calc(100vh - <?php echo (int) $global_settings['topbar_height']; ?>px)"></iframe>
        </div>
    </div>

    <script>
    document.addEventListener('click', function(e) {
        var b = e.target.closest('.rtp-devices button');
        if (!b) return;
        var w = b.getAttribute('data-w') || '100%';
        var f = document.getElementById('rtp-frame');
        if (f) {
            f.style.width = w;
        }
    });
    </script>
    <?php wp_footer(); ?>
</body>

</html>
<?php
	exit;
});