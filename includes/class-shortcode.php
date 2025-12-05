<?php
if (! defined('ABSPATH')) {
	exit;
}

class RTP_Shortcode {
	public static function init() {
		add_shortcode('responsive_preview', array(__CLASS__, 'render'));
	}

	public static function render($atts = array(), $content = null) {
		$atts = shortcode_atts(array(
			'columns'        => 3,
			'source'         => 'static',
			'count'          => 6,
			'items'          => '',
			'breakpoints'    => '[{"title":"Mobile","width":375,"icon":""},{"title":"Tablet","width":768,"icon":""},{"title":"Desktop","width":1280,"icon":""}]',
			'cta_text'       => __('Open Live', 'responsive-theme-preview'),
			'cta_link'       => '',
			'overlay_bg'     => 'rgba(0,0,0,.6)',
			'preview_btn_pos' => 'pos-br',
			'preview_type'   => 'popup',
		), $atts, 'responsive_preview');

		$items = array();
		if ('dynamic' === $atts['source']) {
			$q = new WP_Query(array(
				'post_type'      => RTP_CPT::POST_TYPE,
				'posts_per_page'  => (int) $atts['count'],
				'no_found_rows'   => true,
				'post_status'     => 'publish',
			));
			if ($q->have_posts()) {
				while ($q->have_posts()) {
					$q->the_post();
					$img = get_post_meta(get_the_ID(), RTP_CPT::META_IMAGE, true);
					$url = get_post_meta(get_the_ID(), RTP_CPT::META_URL, true);
					$items[] = array(
						'image'     => $img,
						'title'     => get_the_title(),
						'url'       => $url,
						'btn'       => __('Preview', 'responsive-theme-preview'),
						'post_id'   => get_the_ID(),
						'permalink' => get_permalink(),
					);
				}
				wp_reset_postdata();
			}
		} else {
			if (! empty($atts['items'])) {
				foreach (explode(';', $atts['items']) as $r) {
					$p = array_map('trim', explode('|', $r));
					if (count($p) >= 3) {
						$items[] = array(
							'image' => $p[0],
							'title' => $p[1],
							'url'   => $p[2],
							'btn'   => isset($p[3]) ? $p[3] : __('Preview', 'responsive-theme-preview'),
						);
					}
				}
			}
		}

		// Get global settings to use default breakpoints if not set in shortcode
		$global_settings = RTP_Admin_Settings::get_settings();

		$bps = json_decode($atts['breakpoints'], true);
		if (! is_array($bps) || empty($bps)) {
			// Use breakpoints from global settings if shortcode doesn't provide them
			$bps = isset($global_settings['default_breakpoints']) && !empty($global_settings['default_breakpoints'])
				? $global_settings['default_breakpoints']
				: array(
					array('title' => 'Mobile', 'width' => 375, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE3IDJIN2MtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMGMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE4SDdWNmgxMHYxNHptLTUtMTJoMnY4aC0yVjh6Ii8+PC9zdmc+'),
					array('title' => 'Tablet', 'width' => 768, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE5IDFINVYyM2gxNFYxem0wIDIySDVWM2gxNHYyMHpNOSAxOWg2djJIOXYtMnptMC0xNGg2djEwSDlWNXoiLz48L3N2Zz4='),
					array('title' => 'Desktop', 'width' => 1280, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE4IDJINmMtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMmMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE2SDZWNmgxMnYxMnpNOSA4aDZ2Mkg5Vjh6bTAgNGg2djJIOXYtMnptMCA0aDZ2Mkg5di0yeiIvPjwvc3ZnPg=='),
				);
		}

		// Get global settings as base
		$global_settings = RTP_Admin_Settings::get_settings();

		// Prepare advanced settings, overriding with shortcode attributes if provided
		$advanced_settings = array(
			'topbar_height' => isset($atts['topbar_height']) ? (int) $atts['topbar_height'] : $global_settings['topbar_height'],
			'device_button_style' => isset($atts['device_button_style']) ? $atts['device_button_style'] : $global_settings['device_button_style'],
			'device_button_size' => isset($atts['device_button_size']) ? $atts['device_button_size'] : $global_settings['device_button_size'],
			'device_button_active_color' => isset($atts['device_button_active_color']) ? $atts['device_button_active_color'] : $global_settings['device_button_active_color'],
			'device_button_hover_color' => isset($atts['device_button_hover_color']) ? $atts['device_button_hover_color'] : $global_settings['device_button_hover_color'],
			'overlay_close_on_click' => isset($atts['overlay_close_on_click']) ? filter_var($atts['overlay_close_on_click'], FILTER_VALIDATE_BOOLEAN) : $global_settings['overlay_close_on_click'],
			'overlay_close_on_esc' => isset($atts['overlay_close_on_esc']) ? filter_var($atts['overlay_close_on_esc'], FILTER_VALIDATE_BOOLEAN) : $global_settings['overlay_close_on_esc'],
			'overlay_loading_indicator' => isset($atts['overlay_loading_indicator']) ? filter_var($atts['overlay_loading_indicator'], FILTER_VALIDATE_BOOLEAN) : $global_settings['overlay_loading_indicator'],
			'overlay_loading_color' => isset($atts['overlay_loading_color']) ? $atts['overlay_loading_color'] : $global_settings['overlay_loading_color'],
			'enable_keyboard_nav' => isset($atts['enable_keyboard_nav']) ? filter_var($atts['enable_keyboard_nav'], FILTER_VALIDATE_BOOLEAN) : $global_settings['enable_keyboard_nav'],
			'focus_outline' => isset($atts['focus_outline']) ? filter_var($atts['focus_outline'], FILTER_VALIDATE_BOOLEAN) : $global_settings['focus_outline'],
			'focus_outline_color' => isset($atts['focus_outline_color']) ? $atts['focus_outline_color'] : $global_settings['focus_outline_color'],
			'focus_outline_width' => isset($atts['focus_outline_width']) ? (int) $atts['focus_outline_width'] : $global_settings['focus_outline_width'],
		);

		return RTP_Render::html(array(
			'columns'         => (int) $atts['columns'],
			'overlay_bg'      => $atts['overlay_bg'],
			'preview_btn_pos' => $atts['preview_btn_pos'],
			'cta_text'       => sanitize_text_field($atts['cta_text']),
			'cta_link'       => esc_url($atts['cta_link']),
			'items'          => $items,
			'breakpoints'    => $bps,
			'preview_type'   => ('dynamic' === $atts['source']) ? $atts['preview_type'] : 'popup',
			'advanced_settings' => $advanced_settings,
		));
	}
}
RTP_Shortcode::init();
