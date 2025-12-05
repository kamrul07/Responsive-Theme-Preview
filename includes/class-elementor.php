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

		$this->start_controls_section(
			'rtp_advanced',
			array(
				'label' => __('Advanced Settings', 'responsive-theme-preview'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// Animation settings
		// Note: Animation and frame settings have been removed

		// Device button settings
		$this->add_control(
			'advanced_heading_device_buttons',
			array(
				'label' => __('Device Button Settings', 'responsive-theme-preview'),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'device_button_style',
			array(
				'label'   => __('Button Style', 'responsive-theme-preview'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => __('Default', 'responsive-theme-preview'),
					'rounded' => __('Rounded', 'responsive-theme-preview'),
					'square'  => __('Square', 'responsive-theme-preview'),
				),
			)
		);

		$this->add_control(
			'device_button_size',
			array(
				'label'   => __('Button Size', 'responsive-theme-preview'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'medium',
				'options' => array(
					'small'  => __('Small', 'responsive-theme-preview'),
					'medium' => __('Medium', 'responsive-theme-preview'),
					'large'  => __('Large', 'responsive-theme-preview'),
				),
			)
		);

		$this->add_control(
			'device_button_active_color',
			array(
				'label'     => __('Active Button Color', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2563eb',
			)
		);

		$this->add_control(
			'device_button_hover_color',
			array(
				'label'     => __('Hover Button Color', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1d4ed8',
			)
		);

		// Topbar settings
		$this->add_control(
			'advanced_heading_topbar',
			array(
				'label' => __('Topbar Settings', 'responsive-theme-preview'),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);




		// Overlay settings
		$this->add_control(
			'advanced_heading_overlay',
			array(
				'label' => __('Overlay Settings', 'responsive-theme-preview'),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'overlay_close_on_click',
			array(
				'label'   => __('Close on Click Outside', 'responsive-theme-preview'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'overlay_close_on_esc',
			array(
				'label'   => __('Close on ESC Key', 'responsive-theme-preview'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'overlay_loading_indicator',
			array(
				'label'   => __('Show Loading Indicator', 'responsive-theme-preview'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'overlay_loading_color',
			array(
				'label'     => __('Loading Indicator Color', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2563eb',
				'condition' => array('overlay_loading_indicator' => 'yes'),
			)
		);

		// Accessibility settings
		$this->add_control(
			'advanced_heading_accessibility',
			array(
				'label' => __('Accessibility Settings', 'responsive-theme-preview'),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'enable_keyboard_nav',
			array(
				'label'   => __('Enable Keyboard Navigation', 'responsive-theme-preview'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'focus_outline',
			array(
				'label'   => __('Show Focus Outline', 'responsive-theme-preview'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'focus_outline_color',
			array(
				'label'     => __('Focus Outline Color', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2563eb',
				'condition' => array('focus_outline' => 'yes'),
			)
		);

		$this->add_control(
			'focus_outline_width',
			array(
				'label'     => __('Focus Outline Width (px)', 'responsive-theme-preview'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 2,
				'min'       => 1,
				'max'       => 5,
				'condition' => array('focus_outline' => 'yes'),
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

		// Get global settings as base
		$global_settings = RTP_Admin_Settings::get_settings();

		// Prepare advanced settings, overriding with Elementor settings if provided
		$advanced_settings = array(
			'device_button_style' => isset($s['device_button_style']) ? $s['device_button_style'] : $global_settings['device_button_style'],
			'device_button_size' => isset($s['device_button_size']) ? $s['device_button_size'] : $global_settings['device_button_size'],
			'device_button_active_color' => isset($s['device_button_active_color']) ? $s['device_button_active_color'] : $global_settings['device_button_active_color'],
			'device_button_hover_color' => isset($s['device_button_hover_color']) ? $s['device_button_hover_color'] : $global_settings['device_button_hover_color'],
			'overlay_close_on_click' => isset($s['overlay_close_on_click']) ? ($s['overlay_close_on_click'] === 'yes') : $global_settings['overlay_close_on_click'],
			'overlay_close_on_esc' => isset($s['overlay_close_on_esc']) ? ($s['overlay_close_on_esc'] === 'yes') : $global_settings['overlay_close_on_esc'],
			'overlay_loading_indicator' => isset($s['overlay_loading_indicator']) ? ($s['overlay_loading_indicator'] === 'yes') : $global_settings['overlay_loading_indicator'],
			'overlay_loading_color' => isset($s['overlay_loading_color']) ? $s['overlay_loading_color'] : $global_settings['overlay_loading_color'],
			'enable_keyboard_nav' => isset($s['enable_keyboard_nav']) ? ($s['enable_keyboard_nav'] === 'yes') : $global_settings['enable_keyboard_nav'],
			'focus_outline' => isset($s['focus_outline']) ? ($s['focus_outline'] === 'yes') : $global_settings['focus_outline'],
			'focus_outline_color' => isset($s['focus_outline_color']) ? $s['focus_outline_color'] : $global_settings['focus_outline_color'],
			'focus_outline_width' => isset($s['focus_outline_width']) ? (int) $s['focus_outline_width'] : $global_settings['focus_outline_width'],
		);

		echo RTP_Render::html(array(
			'columns'         => (int) ($s['columns'] ?? 3),
			'overlay_bg'      => $s['overlay_bg'] ?? 'rgba(0,0,0,.6)',
			'preview_btn_pos' => $s['preview_btn_pos'] ?? 'pos-br',
			'cta_text'       => $s['cta_text'] ?? 'Open Live',
			'cta_link'       => $s['cta_link'] ?? '',
			'items'          => $items,
			'breakpoints'    => $bps,
			'preview_type'   => (($s['source'] ?? '') === 'dynamic') ? ($s['preview_type'] ?? 'popup') : 'popup',
			'advanced_settings' => $advanced_settings,
		));
	}
}
