<?php
if (! defined('ABSPATH')) {
	exit;
}

class RTP_Block {
	public static function init() {
		add_action('init', array(__CLASS__, 'register'));
	}

	public static function register() {
		register_block_type('rtp/responsive-preview', array(
			'render_callback' => array(__CLASS__, 'render'),
			'attributes'      => array(
				'columns'        => array('type' => 'number', 'default' => 3),
				'source'         => array('type' => 'string', 'default' => 'static'),
				'count'          => array('type' => 'number', 'default' => 6),
				'itemsArr'       => array('type' => 'array',  'default' => array()),
				'items'          => array('type' => 'string', 'default' => ''),
				'breakpoints'    => array('type' => 'array',  'default' => array(
					array('title' => 'Mobile',  'width' => 375,  'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE3IDJIN2MtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMGMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE4SDdWNmgxMHYxNHptLTUtMTJoMnY4aC0yVjh6Ii8+PC9zdmc+'),
					array('title' => 'Tablet',  'width' => 768,  'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE5IDFINVYyM2gxNFYxem0wIDIySDVWM2gxNHYyMHpNOSAxOWg2djJIOXYtMnptMC0xNGg2djEwSDlWNXoiLz48L3N2Zz4='),
					array('title' => 'Desktop', 'width' => 1280, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE4IDJINmMtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMmMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE2SDZWNmgxMnYxMnpNOSA4aDZ2Mkg5Vjh6bTAgNGg2djJIOXYtMnptMCA0aDZ2Mkg5di0yeiIvPjwvc3ZnPg=='),
				)),
				'ctaText'       => array('type' => 'string', 'default' => 'Open Live'),
				'ctaLink'       => array('type' => 'string', 'default' => ''),
				'overlayBg'     => array('type' => 'string', 'default' => 'rgba(0,0,0,.6)'),
				'previewBtnPos' => array('type' => 'string', 'default' => 'pos-br'),
				'previewType'   => array('type' => 'string', 'default' => 'popup'),
				'topbarHeight' => array('type' => 'number', 'default' => 52),
				// Advanced settings
				'deviceButtonStyle' => array('type' => 'string', 'default' => 'default'),
				'deviceButtonSize' => array('type' => 'string', 'default' => 'medium'),
				'deviceButtonActiveColor' => array('type' => 'string', 'default' => '#2563eb'),
				'deviceButtonHoverColor' => array('type' => 'string', 'default' => '#1d4ed8'),
				'overlayCloseOnClick' => array('type' => 'boolean', 'default' => true),
				'overlayCloseOnEsc' => array('type' => 'boolean', 'default' => true),
				'overlayLoadingIndicator' => array('type' => 'boolean', 'default' => true),
				'overlayLoadingColor' => array('type' => 'string', 'default' => '#2563eb'),
				'enableKeyboardNav' => array('type' => 'boolean', 'default' => true),
				'focusOutline' => array('type' => 'boolean', 'default' => true),
				'focusOutlineColor' => array('type' => 'string', 'default' => '#2563eb'),
				'focusOutlineWidth' => array('type' => 'number', 'default' => 2),
			),
		));
	}

	public static function render($atts = array()) {
		$source = isset($atts['source']) ? $atts['source'] : 'static';
		$items  = array();

		if ('dynamic' === $source) {
			$q = new WP_Query(array(
				'post_type'     => RTP_CPT::POST_TYPE,
				'posts_per_page' => (int) ($atts['count'] ?? 6),
				'no_found_rows'  => true,
				'post_status'    => 'publish',
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
			if (! empty($atts['itemsArr']) && is_array($atts['itemsArr'])) {
				foreach ($atts['itemsArr'] as $it) {
					$items[] = array(
						'image' => isset($it['image']) ? $it['image'] : '',
						'title' => isset($it['title']) ? $it['title'] : '',
						'url'   => isset($it['url']) ? $it['url'] : '',
						'btn'   => (isset($it['btn']) && '' !== $it['btn']) ? $it['btn'] : __('Preview', 'responsive-theme-preview'),
					);
				}
			} elseif (! empty($atts['items'])) {
				foreach (explode(';', $atts['items']) as $r) {
					$p = array_map('trim', explode('|', $r));
					if (count($p) >= 3) {
						$items[] = array('image' => $p[0], 'title' => $p[1], 'url' => $p[2], 'btn' => isset($p[3]) ? $p[3] : __('Preview', 'responsive-theme-preview'));
					}
				}
			}
		}

		$bps = (isset($atts['breakpoints']) && is_array($atts['breakpoints'])) ? $atts['breakpoints'] : array();
		if (! $bps) {
			$bps = array(
				array('title' => 'Mobile',  'width' => 375,  'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE3IDJIN2MtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMGMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE4SDdWNmgxMHYxNHptLTUtMTJoMnY4aC0yVjh6Ii8+PC9zdmc+'),
				array('title' => 'Tablet',  'width' => 768,  'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE5IDFINVYyM2gxNFYxem0wIDIySDVWM2gxNHYyMHpNOSAxOWg2djJIOXYtMnptMC0xNGg2djEwSDlWNXoiLz48L3N2Zz4='),
				array('title' => 'Desktop', 'width' => 1280, 'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTE4IDJINmMtMS4xIDAtMiAuOS0yIDJ2MTZjMCAxLjEuOSAyIDIgMmgxMmMxLjEgMCAyLS45IDItMlY0YzAtMS4xLS45LTItMi0yem0wIDE2SDZWNmgxMnYxMnpNOSA4aDZ2Mkg5Vjh6bTAgNGg2djJIOXYtMnptMCA0aDZ2Mkg5di0yeiIvPjwvc3ZnPg=='),
			);
		}

		// Get global settings as base
		$global_settings = RTP_Admin_Settings::get_settings();

		// Prepare advanced settings, overriding with block attributes if provided
		$advanced_settings = array(
			'topbar_height' => isset($atts['topbarHeight']) ? (int) $atts['topbarHeight'] : $global_settings['topbar_height'],
			'device_button_style' => isset($atts['deviceButtonStyle']) ? $atts['deviceButtonStyle'] : $global_settings['device_button_style'],
			'device_button_size' => isset($atts['deviceButtonSize']) ? $atts['deviceButtonSize'] : $global_settings['device_button_size'],
			'device_button_active_color' => isset($atts['deviceButtonActiveColor']) ? $atts['deviceButtonActiveColor'] : $global_settings['device_button_active_color'],
			'device_button_hover_color' => isset($atts['deviceButtonHoverColor']) ? $atts['deviceButtonHoverColor'] : $global_settings['device_button_hover_color'],
			'overlay_close_on_click' => isset($atts['overlayCloseOnClick']) ? $atts['overlayCloseOnClick'] : $global_settings['overlay_close_on_click'],
			'overlay_close_on_esc' => isset($atts['overlayCloseOnEsc']) ? $atts['overlayCloseOnEsc'] : $global_settings['overlay_close_on_esc'],
			'overlay_loading_indicator' => isset($atts['overlayLoadingIndicator']) ? $atts['overlayLoadingIndicator'] : $global_settings['overlay_loading_indicator'],
			'overlay_loading_color' => isset($atts['overlayLoadingColor']) ? $atts['overlayLoadingColor'] : $global_settings['overlay_loading_color'],
			'enable_keyboard_nav' => isset($atts['enableKeyboardNav']) ? $atts['enableKeyboardNav'] : $global_settings['enable_keyboard_nav'],
			'focus_outline' => isset($atts['focusOutline']) ? $atts['focusOutline'] : $global_settings['focus_outline'],
			'focus_outline_color' => isset($atts['focusOutlineColor']) ? $atts['focusOutlineColor'] : $global_settings['focus_outline_color'],
			'focus_outline_width' => isset($atts['focusOutlineWidth']) ? (int) $atts['focusOutlineWidth'] : $global_settings['focus_outline_width'],
		);

		return RTP_Render::html(array(
			'columns'         => (int) ($atts['columns'] ?? 3),
			'overlay_bg'      => $atts['overlayBg'] ?? 'rgba(0,0,0,.6)',
			'preview_btn_pos' => $atts['previewBtnPos'] ?? 'pos-br',
			'cta_text'       => sanitize_text_field($atts['ctaText'] ?? 'Open Live'),
			'cta_link'       => esc_url($atts['ctaLink'] ?? ''),
			'items'          => $items,
			'breakpoints'    => $bps,
			'preview_type'   => ('dynamic' === $source) ? ($atts['previewType'] ?? 'popup') : 'popup',
			'advanced_settings' => $advanced_settings,
		));
	}
}
RTP_Block::init();
