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
	$topbg   = '#0f172a';

	$bps = array(
		array('title' => 'Desktop', 'width' => 1280, 'icon' => ''),
		array('title' => 'Tablet',  'width' => 768,  'icon' => ''),
		array('title' => 'Mobile',  'width' => 375,  'icon' => ''),
	);

	$views = (int) get_post_meta($post_id, RTP_CPT::META_VIEWS, true);
	$views++;
	update_post_meta($post_id, RTP_CPT::META_VIEWS, $views);

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
				display: block
			}

			.rtp-framewrap {
				top: 52px
			}
		</style>
		<?php wp_head(); ?>
	</head>

	<body <?php body_class('rtp-preview-page'); ?>>
		<div class="rtp-wrapper">
			<div class="rtp-topbar" style="background:<?php echo esc_attr($topbg); ?>">
				<div class="rtp-topbar-title"><?php echo esc_html($title); ?></div>
				<div class="rtp-devices">
					<button data-w="100%" title="Desktop">•</button>
					<button data-w="768px" title="Tablet">••</button>
					<button data-w="375px" title="Mobile">•••</button>
				</div>
				<a class="rtp-cta" href="<?php echo $url ? $url : '#'; ?>" target="_blank" rel="noopener"><?php esc_html_e('Open Live', 'responsive-theme-preview'); ?></a>
			</div>
			<div class="rtp-framewrap">
				<iframe id="rtp-frame" src="<?php echo $url; ?>" loading="lazy" style="width:100%;height:calc(100vh - 52px)"></iframe>
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
