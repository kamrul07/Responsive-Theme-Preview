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
					array('title' => 'Mobile',  'width' => 375,  'icon' => array('value' => 'fas fa-mobile-alt', 'library' => 'fa-solid')),
					array('title' => 'Tablet',  'width' => 768,  'icon' => array('value' => 'fas fa-tablet-alt', 'library' => 'fa-solid')),
					array('title' => 'Desktop', 'width' => 1280, 'icon' => array('value' => 'fas fa-desktop', 'library' => 'fa-solid')),
				)),
				'ctaText'       => array('type' => 'string', 'default' => 'Open Live'),
				'ctaLink'       => array('type' => 'string', 'default' => ''),
				'overlayBg'     => array('type' => 'string', 'default' => 'rgba(0,0,0,.6)'),
				'previewBtnPos' => array('type' => 'string', 'default' => 'pos-br'),
				'previewType'   => array('type' => 'string', 'default' => 'popup'),
				'topbarHeight' => array('type' => 'number', 'default' => 52),
				'categoryFilter' => array('type' => 'string', 'default' => ''),
				'enableCategoryFilter' => array('type' => 'boolean', 'default' => false),
				// Card Style settings
				'cardBg' => array('type' => 'string', 'default' => '#ffffff'),
				'cardBorder' => array('type' => 'object', 'default' => array()),
				'titleTypography' => array('type' => 'object', 'default' => array()),
				'buttonBg' => array('type' => 'string', 'default' => '#1f2937'),
				'buttonTypography' => array('type' => 'object', 'default' => array()),
				'buttonBorder' => array('type' => 'object', 'default' => array()),
				'buttonBorderRadius' => array('type' => 'number', 'default' => 8),
				// Filter Style settings
				'filterWidth' => array('type' => 'number', 'default' => 'auto'),
				'filterDirection' => array('type' => 'string', 'default' => 'row'),
				'filterAlignItems' => array('type' => 'string', 'default' => 'center'),
				'filterJustifyContent' => array('type' => 'string', 'default' => 'flex-start'),
				'filterGap' => array('type' => 'number', 'default' => 10),
				'filterWrap' => array('type' => 'string', 'default' => 'nowrap'),
				'filterTypography' => array('type' => 'object', 'default' => array()),
				'filterColor' => array('type' => 'string', 'default' => '#ffffff'),
				'filterBgColor' => array('type' => 'string', 'default' => '#0f172a'),
				'filterBorder' => array('type' => 'object', 'default' => array()),
				'filterPadding' => array('type' => 'object', 'default' => array()),
				'filterActiveColor' => array('type' => 'string', 'default' => '#ffffff'),
				'filterActiveBgColor' => array('type' => 'string', 'default' => '#0463c8'),
				'filterActiveBorder' => array('type' => 'object', 'default' => array()),
				// Topbar settings
				'topbarTitleTypography' => array('type' => 'object', 'default' => array()),
				'topbarTitleColor' => array('type' => 'string', 'default' => '#ffffff'),
				'overlayBgColor' => array('type' => 'string', 'default' => 'rgba(0,0,0,.6)'),
				'deviceButtonColor' => array('type' => 'string', 'default' => '#1d4ed8'),
				'deviceButtonPadding' => array('type' => 'object', 'default' => array()),
				'deviceButtonBorder' => array('type' => 'object', 'default' => array()),
				'ctaButtonBgColor' => array('type' => 'string', 'default' => '#2563eb'),
				'ctaButtonColor' => array('type' => 'string', 'default' => '#fff'),
				'ctaPadding' => array('type' => 'object', 'default' => array()),
				'ctaButtonBorder' => array('type' => 'object', 'default' => array()),
				'ctaTypography' => array('type' => 'object', 'default' => array()),
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
			$category = isset($atts['categoryFilter']) ? $atts['categoryFilter'] : '';
			$enable_filter = isset($atts['enableCategoryFilter']) ? $atts['enableCategoryFilter'] : false;

			$query_args = array(
				'post_type'     => RTP_CPT::POST_TYPE,
				'posts_per_page' => (int) ($atts['count'] ?? 6),
				'no_found_rows'  => true,
				'post_status'    => 'publish',
			);

			// Add category filter if specified
			if (!empty($category)) {
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => 'rtp-category',
						'field'    => 'slug',
						'terms'    => $category,
					),
				);
			}

			$q = new WP_Query($query_args);
			if ($q->have_posts()) {
				while ($q->have_posts()) {
					$q->the_post();
					$img = get_post_meta(get_the_ID(), RTP_CPT::META_IMAGE, true);
					$url = get_post_meta(get_the_ID(), RTP_CPT::META_URL, true);

					// Get categories for this preview
					$categories = get_the_terms(get_the_ID(), 'rtp-category');
					$category_slugs = array();
					if (!is_wp_error($categories) && !empty($categories)) {
						$category_slugs = wp_list_pluck($categories, 'slug');
					}

					$items[] = array(
						'image'     => $img,
						'title'     => get_the_title(),
						'url'       => $url,
						'btn'       => __('Preview', 'responsive-theme-preview'),
						'post_id'   => get_the_ID(),
						'permalink' => get_permalink(),
						'categories' => $category_slugs,
						'category_slug' => !empty($category_slugs) ? implode(',', $category_slugs) : '',
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
						'icon'  => isset($it['icon']) ? $it['icon'] : array(),
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
			'device_button_color' => isset($atts['deviceButtonColor']) ? $atts['deviceButtonColor'] : $global_settings['device_button_color'],
			'overlay_close_on_click' => isset($atts['overlayCloseOnClick']) ? $atts['overlayCloseOnClick'] : $global_settings['overlay_close_on_click'],
			'overlay_close_on_esc' => isset($atts['overlayCloseOnEsc']) ? $atts['overlayCloseOnEsc'] : $global_settings['overlay_close_on_esc'],
			'overlay_loading_indicator' => isset($atts['overlayLoadingIndicator']) ? $atts['overlayLoadingIndicator'] : $global_settings['overlay_loading_indicator'],
			'overlay_loading_color' => isset($atts['overlayLoadingColor']) ? $atts['overlayLoadingColor'] : $global_settings['overlay_loading_color'],
			'enable_keyboard_nav' => isset($atts['enableKeyboardNav']) ? $atts['enableKeyboardNav'] : $global_settings['enable_keyboard_nav'],
			'focus_outline' => isset($atts['focusOutline']) ? $atts['focusOutline'] : $global_settings['focus_outline'],
			'focus_outline_color' => isset($atts['focusOutlineColor']) ? $atts['focusOutlineColor'] : $global_settings['focus_outline_color'],
			'focus_outline_width' => isset($atts['focusOutlineWidth']) ? (int) $atts['focusOutlineWidth'] : $global_settings['focus_outline_width'],
			'button_border_radius' => isset($atts['buttonBorderRadius']) ? (int) $atts['buttonBorderRadius'] : $global_settings['button_border_radius'] ?? 8,
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
			'enable_category_filter' => $enable_filter,
		));
	}
}
RTP_Block::init();