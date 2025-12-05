<?php
if (! defined('ABSPATH')) {
	exit;
}

class RTP_Advanced_Settings {

	/**
	 * Default advanced settings
	 */
	public static function get_defaults() {
		return array(
			// Device button settings
			'device_button_style' => 'default', // default, rounded, square
			'device_button_size' => 'medium', // small, medium, large
			'device_button_active_color' => '#2563eb',
			'device_button_hover_color' => '#1d4ed8',


			// Topbar settings
			'topbar_height' => 52,

			// Overlay settings
			'overlay_close_on_click' => true,
			'overlay_close_on_esc' => true,
			'overlay_loading_indicator' => true,
			'overlay_loading_color' => '#2563eb',

			// Preview settings
			'preview_start_with_device' => 'desktop', // desktop, tablet, mobile
			'preview_zoom_level' => 1.0,
			'preview_allow_zoom' => true,

			// Performance settings
			'lazy_load_preview' => true,
			'preload_previews' => false,
			'cache_previews' => true,
			'cache_duration' => 3600, // 1 hour

			// Accessibility settings
			'enable_keyboard_nav' => true,
			'enable_screen_reader' => true,
			'focus_outline' => true,
			'focus_outline_color' => '#2563eb',
			'focus_outline_width' => 2,

			// Custom CSS/JS
			'custom_css' => '',
			'custom_js' => '',

			// Default breakpoints
			'default_breakpoints' => array(
				array(
					'title' => 'Desktop',
					'width' => 1280,
					'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE4IDJINmMtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMmMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE2SDZWNmgxMnYxMnpNOSA4aDZ2Mkg5Vjh6bTAgNGg2djJIOXYtMnptMCA0aDZ2Mkg5di0yeiIvPjwvc3ZnPg==',
				),
				array(
					'title' => 'Tablet',
					'width' => 768,
					'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE5IDFINVYyM2gxNFYxem0wIDIySDVWM2gxNHYyMHpNOSAxOWg2djJIOXYtMnptMC0xNGg2djEwSDlWNXoiLz48L3N2Zz4=',
				),
				array(
					'title' => 'Mobile',
					'width' => 375,
					'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE3IDJIN2MtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMGMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE4SDdWNmgxMHYxNHptLTUtMTJoMnY4aC0yVjh6Ii8+PC9zdmc+',
				),
			),

			// Developer settings
			'debug_mode' => false,
			'log_events' => false,
		);
	}

	/**
	 * Sanitize advanced settings
	 */
	public static function sanitize_settings($settings) {
		$defaults = self::get_defaults();
		$sanitized = array();

		foreach ($defaults as $key => $default) {
			$value = isset($settings[$key]) ? $settings[$key] : $default;

			switch ($key) {
				// Boolean values
				case 'topbar_height':
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
				case 'topbar_height':
				case 'cache_duration':
				case 'focus_outline_width':
					$sanitized[$key] = (int) $value;
					break;

				// Float values
				case 'preview_zoom_level':
					$sanitized[$key] = (float) $value;
					break;

				// Color values
				case 'device_button_active_color':
				case 'device_button_hover_color':
				case 'overlay_loading_color':
				case 'focus_outline_color':
					// Handle both string and array color values from Bricks
					if (is_array($value) && isset($value['hex'])) {
						$sanitized[$key] = sanitize_hex_color($value['hex']);
					} else {
						$sanitized[$key] = sanitize_hex_color($value);
					}
					break;

					// Text values with specific options
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
	 * Generate CSS from advanced settings
	 */
	public static function generate_css($settings) {
		$css = '';
		$defaults = self::get_defaults();
		$settings = wp_parse_args($settings, $defaults);

		// Note: Animation and frame settings have been removed

		// Device button settings
		switch ($settings['device_button_style']) {
			case 'rounded':
				$css .= ".rtp-devices button { border-radius: 20px; }\n";
				break;
			case 'square':
				$css .= ".rtp-devices button { border-radius: 0; }\n";
				break;
		}

		switch ($settings['device_button_size']) {
			case 'small':
				$css .= ".rtp-devices button { padding: 4px 8px; font-size: 12px; }\n";
				break;
			case 'large':
				$css .= ".rtp-devices button { padding: 8px 16px; font-size: 16px; }\n";
				break;
		}

		// Note: Button colors are now handled by Bricks CSS controls
		// $css .= ".rtp-devices button:hover { background: {$settings['device_button_hover_color']}; }\n";
		// $css .= ".rtp-devices button.active { background: {$settings['device_button_active_color']}; }\n";

		// Topbar height settings
		if ($settings['topbar_height'] !== 52) {
			$css .= ".rtp-topbar { height: {$settings['topbar_height']}px; }\n";
			$css .= ".rtp-framewrap { top: {$settings['topbar_height']}px; }\n";
			$css .= "#rtp-frame { height: calc(100vh - {$settings['topbar_height']}px); }\n";
		}

		// Focus settings
		if ($settings['focus_outline']) {
			$css .= ".rtp-devices button:focus, .rtp-open:focus, .rtp-cta:focus, .rtp-close:focus { outline: {$settings['focus_outline_width']}px solid {$settings['focus_outline_color']}; outline-offset: 2px; }\n";
		} else {
			$css .= ".rtp-devices button:focus, .rtp-open:focus, .rtp-cta:focus, .rtp-close:focus { outline: none; }\n";
		}

		// Custom CSS
		if (! empty($settings['custom_css'])) {
			$css .= "\n/* Custom CSS */\n" . $settings['custom_css'] . "\n";
		}

		return $css;
	}

	/**
	 * Generate JavaScript configuration from advanced settings
	 */
	public static function generate_js_config($settings) {
		$defaults = self::get_defaults();
		$settings = wp_parse_args($settings, $defaults);

		$config = array(
			'topbar' => array(
				'height' => $settings['topbar_height'],
			),
			'overlay' => array(
				'closeOnClick' => $settings['overlay_close_on_click'],
				'closeOnEsc' => $settings['overlay_close_on_esc'],
				'loadingIndicator' => $settings['overlay_loading_indicator'],
				'loadingColor' => $settings['overlay_loading_color'],
			),
			'preview' => array(
				'startWithDevice' => $settings['preview_start_with_device'],
				'zoomLevel' => $settings['preview_zoom_level'],
				'allowZoom' => $settings['preview_allow_zoom'],
			),
			'keyboard' => array(
				'enabled' => $settings['enable_keyboard_nav'],
			),
			'debug' => array(
				'enabled' => $settings['debug_mode'],
				'logEvents' => $settings['log_events'],
			),
		);

		return $config;
	}
}
