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
					array('title' => 'Mobile',  'width' => 375,  'icon' => ''),
					array('title' => 'Tablet',  'width' => 768,  'icon' => ''),
					array('title' => 'Desktop', 'width' => 1280, 'icon' => ''),
				)),
				'ctaText'       => array('type' => 'string', 'default' => 'Open Live'),
				'ctaLink'       => array('type' => 'string', 'default' => ''),
				'overlayBg'     => array('type' => 'string', 'default' => 'rgba(0,0,0,.6)'),
				'topbarBg'      => array('type' => 'string', 'default' => '#0f172a'),
				'previewBtnPos' => array('type' => 'string', 'default' => 'pos-br'),
				'previewType'   => array('type' => 'string', 'default' => 'popup'),
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
				array('title' => 'Mobile',  'width' => 375,  'icon' => ''),
				array('title' => 'Tablet',  'width' => 768,  'icon' => ''),
				array('title' => 'Desktop', 'width' => 1280, 'icon' => ''),
			);
		}

		return RTP_Render::html(array(
			'columns'         => (int) ($atts['columns'] ?? 3),
			'overlay_bg'      => $atts['overlayBg'] ?? 'rgba(0,0,0,.6)',
			'preview_btn_pos' => $atts['previewBtnPos'] ?? 'pos-br',
			'cta_text'       => sanitize_text_field($atts['ctaText'] ?? 'Open Live'),
			'cta_link'       => esc_url($atts['ctaLink'] ?? ''),
			'items'          => $items,
			'breakpoints'    => $bps,
			'topbar_bg'      => $atts['topbarBg'] ?? '#0f172a',
			'preview_type'   => ('dynamic' === $source) ? ($atts['previewType'] ?? 'popup') : 'popup',
		));
	}
}
RTP_Block::init();
