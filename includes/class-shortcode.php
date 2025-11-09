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
			'topbar_bg'      => '#0f172a',
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

		$bps = json_decode($atts['breakpoints'], true);
		if (! is_array($bps) || empty($bps)) {
			$bps = array(
				array('title' => 'Mobile',  'width' => 375,  'icon' => ''),
				array('title' => 'Tablet',  'width' => 768,  'icon' => ''),
				array('title' => 'Desktop', 'width' => 1280, 'icon' => ''),
			);
		}

		return RTP_Render::html(array(
			'columns'         => (int) $atts['columns'],
			'overlay_bg'      => $atts['overlay_bg'],
			'preview_btn_pos' => $atts['preview_btn_pos'],
			'cta_text'       => sanitize_text_field($atts['cta_text']),
			'cta_link'       => esc_url($atts['cta_link']),
			'items'          => $items,
			'breakpoints'    => $bps,
			'topbar_bg'      => $atts['topbar_bg'],
			'preview_type'   => ('dynamic' === $atts['source']) ? $atts['preview_type'] : 'popup',
		));
	}
}
RTP_Shortcode::init();
