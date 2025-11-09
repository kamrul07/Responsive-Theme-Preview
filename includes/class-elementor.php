<?php
if (! defined('ABSPATH')) {
	exit;
}
if (! class_exists('\Elementor\Widget_Base')) {
	return;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class RTP_Elementor_Widget extends \Elementor\Widget_Base {
	public function get_name() {
		return 'responsive_preview';
	}
	public function get_title() {
		return __('Responsive Preview', 'responsive-theme-preview');
	}
	public function get_icon() {
		return 'eicon-device-desktop';
	}
	public function get_categories() {
		return array('basic');
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'rtp_content',
			array('label' => __('Content', 'responsive-theme-preview'))
		);

		$this->add_control(
			'columns',
			array(
				'label'   => __('Columns', 'responsive-theme-preview'),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
				'min'     => 1,
				'max'     => 6,
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __('Source', 'responsive-theme-preview'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'static',
				'options' => array(
					'static'  => __('Static (Repeater)', 'responsive-theme-preview'),
					'dynamic' => __('Dynamic (CPT: Previews)', 'responsive-theme-preview'),
				),
			)
		);

		$this->add_control(
			'dynamic_count',
			array(
				'label'     => __('Items (dynamic)', 'responsive-theme-preview'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6,
				'min'       => 1,
				'condition' => array('source' => 'dynamic'),
			)
		);

		$this->add_control(
			'preview_type',
			array(
				'label'     => __('Preview Type', 'responsive-theme-preview'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'popup',
				'options'   => array(
					'popup' => __('Popup', 'responsive-theme-preview'),
					'page'  => __('Separate URL', 'responsive-theme-preview'),
				),
				'condition' => array('source' => 'dynamic'),
			)
		);

		$rep = new \Elementor\Repeater();
		$rep->add_control(
			'image',
			array(
				'label' => __('Image', 'responsive-theme-preview'),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$rep->add_control(
			'title',
			array(
				'label' => __('Title', 'responsive-theme-preview'),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$rep->add_control(
			'url',
			array(
				'label' => __('Preview URL', 'responsive-theme-preview'),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$rep->add_control(
			'btn',
			array(
				'label'   => __('Button Text', 'responsive-theme-preview'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Preview', 'responsive-theme-preview'),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'     => __('Items', 'responsive-theme-preview'),
				'type'      => Controls_Manager::REPEATER,
				'fields'    => $rep->get_controls(),
				'condition' => array('source' => 'static'),
			)
		);

		$bp = new \Elementor\Repeater();
		$bp->add_control(
			'title',
			array(
				'label'   => __('Title', 'responsive-theme-preview'),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Mobile',
			)
		);
		$bp->add_control(
			'width',
			array(
				'label'   => __('Width', 'responsive-theme-preview'),
				'type'    => Controls_Manager::NUMBER,
				'default' => 375,
				'min'     => 240,
				'step'    => 1,
			)
		);
		$bp->add_control(
			'icon',
			array(
				'label' => __('Icon Image', 'responsive-theme-preview'),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'breakpoints',
			array(
				'label'   => __('Breakpoints', 'responsive-theme-preview'),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $bp->get_controls(),
				'default' => array(
					array('title' => 'Mobile',  'width' => 375,  'icon' => array('url' => '')),
					array('title' => 'Tablet',  'width' => 768,  'icon' => array('url' => '')),
					array('title' => 'Desktop', 'width' => 1280, 'icon' => array('url' => '')),
				),
			)
		);

		$this->add_control(
			'cta_text',
			array(
				'label'   => __('CTA Text', 'responsive-theme-preview'),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Open Live', 'responsive-theme-preview'),
			)
		);

		$this->add_control(
			'cta_link',
			array(
				'label'   => __('CTA Link', 'responsive-theme-preview'),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'overlay_bg',
			array(
				'label'   => __('Overlay Background', 'responsive-theme-preview'),
				'type'    => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,.6)',
			)
		);

		$this->add_control(
			'topbar_bg',
			array(
				'label'   => __('Topbar Background', 'responsive-theme-preview'),
				'type'    => Controls_Manager::COLOR,
				'default' => '#0f172a',
			)
		);

		$this->add_control(
			'preview_btn_pos',
			array(
				'label'   => __('Preview Button Position', 'responsive-theme-preview'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'pos-br',
				'options' => array(
					'pos-br' => 'Bottom Right',
					'pos-bl' => 'Bottom Left',
					'pos-tr' => 'Top Right',
					'pos-tl' => 'Top Left',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'rtp_style',
			array(
				'label' => __('Styles', 'responsive-theme-preview'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typo',
				'selector' => '{{WRAPPER}} .rtp-title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __('Title Color', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array('{{WRAPPER}} .rtp-title' => 'color: {{VALUE}};'),
			)
		);

		$this->add_control(
			'preview_btn_bg',
			array(
				'label'     => __('Preview Button BG', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array('{{WRAPPER}} .rtp-open' => 'background: {{VALUE}};'),
			)
		);

		$this->add_control(
			'preview_btn_color',
			array(
				'label'     => __('Preview Button Text', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array('{{WRAPPER}} .rtp-open' => 'color: {{VALUE}};'),
			)
		);

		$this->add_control(
			'preview_btn_radius',
			array(
				'label'     => __('Preview Button Radius', 'responsive-theme-preview'),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => array('{{WRAPPER}} .rtp-open' => 'border-radius: {{VALUE}}px;'),
			)
		);

		$this->add_control(
			'cta_bg',
			array(
				'label'     => __('CTA BG', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array('{{WRAPPER}} .rtp-cta' => 'background: {{VALUE}};'),
			)
		);

		$this->add_control(
			'cta_color',
			array(
				'label'     => __('CTA Text', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array('{{WRAPPER}} .rtp-cta' => 'color: {{VALUE}};'),
			)
		);

		$this->add_control(
			'cta_radius',
			array(
				'label'     => __('CTA Radius', 'responsive-theme-preview'),
				'type'      => Controls_Manager::NUMBER,
				'selectors' => array('{{WRAPPER}} .rtp-cta' => 'border-radius: {{VALUE}}px;'),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$items = array();

		if (($s['source'] ?? 'static') === 'static' && ! empty($s['items'])) {
			foreach ($s['items'] as $it) {
				$items[] = array(
					'image' => (isset($it['image']['url']) ? $it['image']['url'] : ''),
					'title' => (isset($it['title']) ? $it['title'] : ''),
					'url'   => (isset($it['url']) ? $it['url'] : ''),
					'btn'   => (isset($it['btn']) ? $it['btn'] : ''),
				);
			}
		} else {
			$q = new \WP_Query(array(
				'post_type'      => RTP_CPT::POST_TYPE,
				'posts_per_page'  => (int) ($s['dynamic_count'] ?? 6),
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
		}

		$bps = array();
		if (! empty($s['breakpoints'])) {
			foreach ($s['breakpoints'] as $bp) {
				$iconUrl = '';
				if (isset($bp['icon']['url']) && $bp['icon']['url']) {
					$iconUrl = $bp['icon']['url'];
				}
				$bps[] = array(
					'title' => $bp['title'] ?? '',
					'width' => isset($bp['width']) ? (int) $bp['width'] : 1280,
					'icon'  => $iconUrl,
				);
			}
		}

		echo RTP_Render::html(array(
			'columns'         => (int) ($s['columns'] ?? 3),
			'overlay_bg'      => $s['overlay_bg'] ?? 'rgba(0,0,0,.6)',
			'preview_btn_pos' => $s['preview_btn_pos'] ?? 'pos-br',
			'cta_text'       => $s['cta_text'] ?? 'Open Live',
			'cta_link'       => $s['cta_link'] ?? '',
			'items'          => $items,
			'breakpoints'    => $bps,
			'topbar_bg'      => $s['topbar_bg'] ?? '#0f172a',
			'preview_type'   => (($s['source'] ?? '') === 'dynamic') ? ($s['preview_type'] ?? 'popup') : 'popup',
		));
	}
}
