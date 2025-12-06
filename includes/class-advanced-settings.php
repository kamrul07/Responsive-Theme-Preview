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
			'topbar_bg' => '#ffffff',
			'topbar_title_size' => 16,
			'topbar_title_color' => '#333333',
			'topbar_button_color' => '#2563eb',
			'topbar_button_bg' => '#ffffff',
			'topbar_button_font_size' => 14,
			'topbar_button_border_radius' => 4,
			'topbar_button_padding_top' => 8,
			'topbar_button_padding_right' => 16,
			'topbar_button_padding_bottom' => 8,
			'topbar_button_padding_left' => 16,

			// Additional color settings
			'overlay_bg_color' => '#000000',
			'overlay_border_color' => '#cccccc',
			'frame_border_color' => '#dddddd',
			'frame_shadow_color' => 'rgba(0, 0, 0, 0.2)',

			// CTA Button settings
			'cta_button_color' => '#ffffff',
			'cta_button_bg_color' => '#2563eb',
			'cta_button_hover_color' => '#1d4ed8',

			// Device Button settings
			'device_button_text_color' => '#333333',
			'device_button_bg_color' => '#f5f5f5',

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
					'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMjQiIDyNCIgZmlsbD0iI2ZmZmYiLz48c3ZnPg==',
				),
				array(
					'title' => 'Tablet',
					'width' => 768,
					'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjE5IDIzIiBmaWxsPSIjZmZmZmYiLz48c3ZnPg==',
				),
				array(
					'title' => 'Mobile',
					'width' => 375,
					'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjE3IDciIGZpbGw9IiNmZmZmYiLz48c3ZnPg==',
				),
			),
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
					$sanitized[$key] = (bool) $value;
					break;

				// Integer values
				case 'topbar_height':
				case 'topbar_title_size':
				case 'topbar_button_font_size':
				case 'topbar_button_border_radius':
				case 'topbar_button_padding_top':
				case 'topbar_button_padding_right':
				case 'topbar_button_padding_bottom':
				case 'topbar_button_padding_left':
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
				case 'topbar_bg':
				case 'topbar_title_color':
				case 'topbar_button_color':
				case 'topbar_button_bg':
				case 'overlay_bg_color':
				case 'overlay_border_color':
				case 'frame_border_color':
				case 'cta_button_color':
				case 'cta_button_bg_color':
				case 'cta_button_hover_color':
				case 'device_button_text_color':
				case 'device_button_bg_color':
					// Handle both string and array color values from Bricks
					if (is_array($value) && isset($value['hex'])) {
						$sanitized[$key] = sanitize_hex_color($value['hex']);
					} else {
						$sanitized[$key] = sanitize_hex_color($value);
					}
					break;

				case 'frame_shadow_color':
					// Allow rgba values for shadow color
					if (is_array($value) && isset($value['hex'])) {
						$sanitized[$key] = sanitize_text_field($value['hex']);
					} else {
						$sanitized[$key] = preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $value) ? $value : sanitize_text_field($value);
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

		// Device button colors
		if ($settings['device_button_hover_color'] !== '#1d4ed8') {
			$css .= ".rtp-devices button:hover { background: {$settings['device_button_hover_color']}; }\n";
		}

		if ($settings['device_button_active_color'] !== '#2563eb') {
			$css .= ".rtp-devices button.active { background: {$settings['device_button_active_color']}; }\n";
		}

		// Topbar settings
		if ($settings['topbar_height'] !== 52) {
			$css .= ".rtp-topbar { height: {$settings['topbar_height']}px; }\n";
			$css .= ".rtp-framewrap { top: {$settings['topbar_height']}px; }\n";
			$css .= "#rtp-frame { height: calc(100vh - {$settings['topbar_height']}px); }\n";
		}

		if ($settings['topbar_bg'] !== '#ffffff') {
			$css .= ".rtp-topbar { background-color: {$settings['topbar_bg']}; }\n";
		}

		if ($settings['topbar_title_size'] !== 16) {
			$css .= ".rtp-topbar h3 { font-size: {$settings['topbar_title_size']}px; }\n";
		}

		if ($settings['topbar_title_color'] !== '#333333') {
			$css .= ".rtp-topbar h3 { color: {$settings['topbar_title_color']}; }\n";
		}

		if ($settings['topbar_button_color'] !== '#2563eb') {
			$css .= ".rtp-close { color: {$settings['topbar_button_color']}; }\n";
		}

		if ($settings['topbar_button_bg'] !== '#ffffff') {
			$css .= ".rtp-close { background-color: {$settings['topbar_button_bg']}; }\n";
		}

		if ($settings['topbar_button_font_size'] !== 14) {
			$css .= ".rtp-close { font-size: {$settings['topbar_button_font_size']}px; }\n";
		}

		if ($settings['topbar_button_border_radius'] !== 4) {
			$css .= ".rtp-close { border-radius: {$settings['topbar_button_border_radius']}px; }\n";
		}

		if (
			$settings['topbar_button_padding_top'] !== 8 ||
			$settings['topbar_button_padding_right'] !== 16 ||
			$settings['topbar_button_padding_bottom'] !== 8 ||
			$settings['topbar_button_padding_left'] !== 16
		) {
			$css .= ".rtp-close { padding: {$settings['topbar_button_padding_top']}px {$settings['topbar_button_padding_right']}px {$settings['topbar_button_padding_bottom']}px {$settings['topbar_button_padding_left']}px; }\n";
		}

		// Additional color settings
		if ($settings['overlay_bg_color'] !== '#000000') {
			$css .= ".rtp-overlay { background-color: {$settings['overlay_bg_color']}; }\n";
		}

		if ($settings['overlay_border_color'] !== '#cccccc') {
			$css .= ".rtp-overlay { border-color: {$settings['overlay_border_color']}; }\n";
		}

		if ($settings['frame_border_color'] !== '#dddddd') {
			$css .= ".rtp-frame { border-color: {$settings['frame_border_color']}; }\n";
		}

		if ($settings['frame_shadow_color'] !== 'rgba(0, 0, 0, 0.2)') {
			$css .= ".rtp-frame { box-shadow: 0 4px 20px {$settings['frame_shadow_color']}; }\n";
		}

		// CTA Button colors
		if ($settings['cta_button_color'] !== '#ffffff') {
			$css .= ".rtp-cta { color: {$settings['cta_button_color']}; }\n";
		}

		if ($settings['cta_button_bg_color'] !== '#2563eb') {
			$css .= ".rtp-cta { background-color: {$settings['cta_button_bg_color']}; }\n";
		}

		if ($settings['cta_button_hover_color'] !== '#1d4ed8') {
			$css .= ".rtp-cta:hover { background-color: {$settings['cta_button_hover_color']}; }\n";
		}

		// Device Button colors
		if ($settings['device_button_text_color'] !== '#333333') {
			$css .= ".rtp-devices button { color: {$settings['device_button_text_color']}; }\n";
		}

		if ($settings['device_button_bg_color'] !== '#f5f5f5') {
			$css .= ".rtp-devices button { background-color: {$settings['device_button_bg_color']}; }\n";
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
				'background' => $settings['topbar_bg'],
				'titleSize' => $settings['topbar_title_size'],
				'titleColor' => $settings['topbar_title_color'],
				'button' => array(
					'color' => $settings['topbar_button_color'],
					'background' => $settings['topbar_button_bg'],
					'fontSize' => $settings['topbar_button_font_size'],
					'borderRadius' => $settings['topbar_button_border_radius'],
					'padding' => array(
						'top' => $settings['topbar_button_padding_top'],
						'right' => $settings['topbar_button_padding_right'],
						'bottom' => $settings['topbar_button_padding_bottom'],
						'left' => $settings['topbar_button_padding_left'],
					),
				),
			),
			'deviceButtons' => array(
				'style' => $settings['device_button_style'],
				'size' => $settings['device_button_size'],
				'colors' => array(
					'active' => $settings['device_button_active_color'],
					'hover' => $settings['device_button_hover_color'],
					'text' => $settings['device_button_text_color'],
					'background' => $settings['device_button_bg_color'],
				),
			),
			'overlay' => array(
				'closeOnClick' => $settings['overlay_close_on_click'],
				'closeOnEsc' => $settings['overlay_close_on_esc'],
				'loadingIndicator' => $settings['overlay_loading_indicator'],
				'loadingColor' => $settings['overlay_loading_color'],
				'backgroundColor' => $settings['overlay_bg_color'],
				'borderColor' => $settings['overlay_border_color'],
			),
			'frame' => array(
				'borderColor' => $settings['frame_border_color'],
				'shadowColor' => $settings['frame_shadow_color'],
			),
			'ctaButton' => array(
				'color' => $settings['cta_button_color'],
				'backgroundColor' => $settings['cta_button_bg_color'],
				'hoverColor' => $settings['cta_button_hover_color'],
			),
			'preview' => array(
				'startWithDevice' => $settings['preview_start_with_device'],
				'zoomLevel' => $settings['preview_zoom_level'],
				'allowZoom' => $settings['preview_allow_zoom'],
			),
			'keyboard' => array(
				'enabled' => $settings['enable_keyboard_nav'],
			),
			'accessibility' => array(
				'screenReader' => $settings['enable_screen_reader'],
				'focusOutline' => array(
					'enabled' => $settings['focus_outline'],
					'color' => $settings['focus_outline_color'],
					'width' => $settings['focus_outline_width'],
				),
			),
		);

		return $config;
	}
}