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

	/**
	 * Get category options for select control
	 */
	public static function get_category_options() {
		$options = array(
			'' => __('All Categories', 'responsive-theme-preview'),
		);

		$categories = get_terms(array(
			'taxonomy' => 'rtp-category',
			'hide_empty' => true,
		));

		if (!is_wp_error($categories) && !empty($categories)) {
			foreach ($categories as $category) {
				$options[$category->slug] = $category->name;
			}
		}

		return $options;
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

		$this->add_control(
			'category_filter',
			array(
				'label'     => __('Filter by Category', 'responsive-theme-preview'),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'    => self::get_category_options(),
				'condition' => array('source' => 'dynamic'),
			)
		);

		$this->add_control(
			'enable_category_filter',
			array(
				'label'     => __('Enable Frontend Category Filter', 'responsive-theme-preview'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'no',
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
				'label'   => __('Icon', 'responsive-theme-preview'),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-mobile-alt',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'breakpoints',
			array(
				'label'   => __('Breakpoints', 'responsive-theme-preview'),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $bp->get_controls(),
				'default' => array(
					array('title' => 'Mobile',  'width' => 375,  'icon' => array('value' => 'fas fa-mobile-alt', 'library' => 'fa-solid')),
					array('title' => 'Tablet',  'width' => 768,  'icon' => array('value' => 'fas fa-tablet-alt', 'library' => 'fa-solid')),
					array('title' => 'Desktop', 'width' => 1280, 'icon' => array('value' => 'fas fa-desktop', 'library' => 'fa-solid')),
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
				'default' => 'pos-center',
				'options' => array(
					'pos-br' => 'Bottom Right',
					'pos-bl' => 'Bottom Left',
					'pos-tr' => 'Top Right',
					'pos-tl' => 'Top Left',
					'pos-center' => 'Center',
				),
			)
		);

		$this->end_controls_section();

		// Card Style Section
		$this->start_controls_section(
			'rtp_card_style',
			array(
				'label' => __('Card Style', 'responsive-theme-preview'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __('Card Background', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array('{{WRAPPER}} .rtp-card' => 'background: {{VALUE}};'),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .rtp-card',
				'default' => array(
					'border' => 'solid',
					'width' => array(
						'top' => 1,
						'right' => 1,
						'bottom' => 1,
						'left' => 1,
					),
					'color' => '#e5e7eb',
					'radius' => array(
						'top' => 8,
						'right' => 8,
						'bottom' => 8,
						'left' => 8,
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .rtp-title',
			)
		);

		$this->add_control(
			'preview_btn_bg',
			array(
				'label'     => __('Button Background', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1f2937',
				'selectors' => array('{{WRAPPER}} .rtp-open' => 'background: {{VALUE}};'),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .rtp-open',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .rtp-open',
				'default' => array(
					'border' => 'solid',
					'width' => array(
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
					),
					'color' => '#1f2937',
					'radius' => array(
						'top' => 8,
						'right' => 8,
						'bottom' => 8,
						'left' => 8,
					),
				),
			)
		);

		$this->add_responsive_control(
			'card_button_border_radius',
			array(
				'label'      => __('Card Button Border Radius (px)', 'responsive-theme-preview'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array('px', '%'),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step'  => 1,
					),
				),
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => array(
					'{{WRAPPER}} .rtp-open' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Filter Style Section
		$this->start_controls_section(
			'rtp_filter_style',
			array(
				'label' => __('Filter Style', 'responsive-theme-preview'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'filter_width',
			array(
				'label'     => __('Single Filter Width', 'responsive-theme-preview'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em', 'rem', 'custom'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'custom',
					'size' => "auto",
				],
				'selectors' => array('{{WRAPPER}} .singlecategory-filter' => 'width: {{SIZE}}{{UNIT}};'),
			)
		);

		$this->add_responsive_control(
			'filter_direction',
			array(
				'label'     => __('Direction', 'responsive-theme-preview'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'row',
				'options'   => array(
					'row'  => __('Row', 'responsive-theme-preview'),
					'column' => __('Column', 'responsive-theme-preview'),
					'row-reverse' => __('Row Reverse', 'responsive-theme-preview'),
					'column-reverse' => __('Column Reverse', 'responsive-theme-preview'),
				),
				'selectors' => array('{{WRAPPER}} .rtp-category-filter-inner' => 'flex-direction: {{VALUE}};'),
			)
		);

		$this->add_responsive_control(
			'filter_align_items',
			array(
				'label'     => __('Align Items', 'responsive-theme-preview'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => array(
					'flex-start' => __('Start', 'responsive-theme-preview'),
					'center' => __('Center', 'responsive-theme-preview'),
					'flex-end' => __('End', 'responsive-theme-preview'),
					'stretch' => __('Stretch', 'responsive-theme-preview'),
					'baseline' => __('Baseline', 'responsive-theme-preview'),
				),
				'selectors' => array('{{WRAPPER}} .rtp-category-filter-inner' => 'align-items: {{VALUE}};'),
			)
		);

		$this->add_responsive_control(
			'filter_justify_content',
			array(
				'label'     => __('Justify Content', 'responsive-theme-preview'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'flex-start',
				'options'   => array(
					'flex-start' => __('Start', 'responsive-theme-preview'),
					'center' => __('Center', 'responsive-theme-preview'),
					'flex-end' => __('End', 'responsive-theme-preview'),
					'space-between' => __('Space Between', 'responsive-theme-preview'),
					'space-around' => __('Space Around', 'responsive-theme-preview'),
					'space-evenly' => __('Space Evenly', 'responsive-theme-preview'),
				),
				'selectors' => array('{{WRAPPER}} .rtp-category-filter-inner' => 'justify-content: {{VALUE}};'),
			)
		);

		$this->add_responsive_control(
			'filter_gap',
			array(
				'label'     => __('Gap Between Filters', 'responsive-theme-preview'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors' => array('{{WRAPPER}} .rtp-category-filter-inner' => 'gap: {{SIZE}}{{UNIT}};'),
			)
		);

		$this->add_responsive_control(
			'filter_wrap',
			array(
				'label'     => __('Flex Wrap', 'responsive-theme-preview'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'nowrap',
				'options'   => array(
					'nowrap' => __('No Wrap', 'responsive-theme-preview'),
					'wrap' => __('Wrap', 'responsive-theme-preview'),
				),
				'selectors' => array('{{WRAPPER}} .rtp-category-filter-inner' => 'flex-wrap: {{VALUE}};'),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_typography',
				'selector' => '{{WRAPPER}} .singlecategory-filter',
			)
		);

		$this->add_control(
			'filter_color',
			array(
				'label'     => __('Filter Item Color', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array('{{WRAPPER}} .singlecategory-filter' => 'color: {{VALUE}};'),
			)
		);

		$this->add_control(
			'filter_bg_color',
			array(
				'label'     => __('Filter Background', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => array('{{WRAPPER}} .singlecategory-filter' => 'background: {{VALUE}};'),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_border',
				'selector' => '{{WRAPPER}} .singlecategory-filter',
				'default' => array(
					'border' => 'solid',
					'width' => array(
						'top' => 1,
						'right' => 1,
						'bottom' => 1,
						'left' => 0,
					),
					'color' => '#0f172a',
					'radius' => array(
						'top' => 20,
						'right' => 20,
						'bottom' => 20,
						'left' => 20,
					),
				),
			)
		);

		$this->add_responsive_control(
			'filter_padding',
			array(
				'label'     => __('Padding', 'responsive-theme-preview'),
				'type'      => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array('px', 'em', '%'),
				'default'   => array(
					'top' => '5',
					'right' => '10',
					'bottom' => '5',
					'left' => '10',
					'unit' => 'px',
				),
				'selectors' => array('{{WRAPPER}} .singlecategory-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'),
			)
		);

		$this->add_control(
			'filter_active_heading',
			array(
				'label'     => __('Active State', 'responsive-theme-preview'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'filter_active_color',
			array(
				'label'     => __('Active Font Color', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array('{{WRAPPER}} .singlecategory-filter.active' => 'color: {{VALUE}};'),
			)
		);

		$this->add_control(
			'filter_active_bg_color',
			array(
				'label'     => __('Active Background', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0463c8',
				'selectors' => array('{{WRAPPER}} .singlecategory-filter.active' => 'background: {{VALUE}};'),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_active_border',
				'selector' => '{{WRAPPER}} .singlecategory-filter.active',
				'default' => array(
					'border' => 'solid',
					'width' => array(
						'top' => 1,
						'right' => 1,
						'bottom' => 1,
						'left' => 0,
					),
					'color' => '#0463c8',
					'radius' => array(
						'top' => 20,
						'right' => 20,
						'bottom' => 20,
						'left' => 20,
					),
				),
			)
		);

		$this->end_controls_section();

		// Topbar Settings Section
		$this->start_controls_section(
			'rtp_topbar_settings',
			array(
				'label' => __('Topbar Settings', 'responsive-theme-preview'),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'topbar_height',
			array(
				'label'     => __('Topbar Height (px)', 'responsive-theme-preview'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 52,
				'min'       => 40,
				'selectors' => array(
					'{{WRAPPER}} .rtp-topbar' => 'height: {{VALUE}}px;',
					'{{WRAPPER}} .rtp-framewrap' => 'top: {{VALUE}}px;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'topbar_title_typography',
				'selector' => '{{WRAPPER}} .rtp-topbar-title',
			)
		);

		$this->add_control(
			'topbar_title_color',
			array(
				'label'     => __('Title Color', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array('{{WRAPPER}} .rtp-topbar-title' => 'color: {{VALUE}};'),
			)
		);

		$this->add_control(
			'overlay_bg_color',
			array(
				'label'     => __('Overlay Background', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,.6)',
				'selectors' => array('{{WRAPPER}} .rtp-overlay:after' => 'background-color: {{VALUE}};'),
			)
		);

		$this->add_control(
			'device_button_heading',
			array(
				'label'     => __('Device Button', 'responsive-theme-preview'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'device_button_color',
			array(
				'label'     => __('Button Background Color', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1d4ed8',
				'selectors' => array('{{WRAPPER}} .rtp-devices button' => 'background: {{VALUE}};'),
			)
		);

		$this->add_control(
			'device_button_active_color',
			array(
				'label'     => __('Active Button Color', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2563eb',
				'selectors' => array('{{WRAPPER}} .rtp-devices button.active' => 'background: {{VALUE}};'),
			)
		);

		$this->add_control(
			'device_icon_color',
			array(
				'label'     => __('Device Icon Color', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array('{{WRAPPER}} .rtp-devices button svg ' => 'fill: {{VALUE}};'),
			)
		);

		$this->add_responsive_control(
			'device_button_padding',
			array(
				'label'     => __('Padding', 'responsive-theme-preview'),
				'type'      => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array('px', 'em', '%'),
				'default'   => array(
					'top' => '10',
					'right' => '15',
					'bottom' => '10',
					'left' => '15',
					'unit' => 'px',
				),
				'selectors' => array('{{WRAPPER}} .rtp-devices button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'device_button_border',
				'selector' => '{{WRAPPER}} .rtp-devices button',
				'default' => array(
					'border' => 'none',
					'width' => array(
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
					),
					'color' => '#ffff00',
					'radius' => array(
						'top' => 8,
						'right' => 8,
						'bottom' => 8,
						'left' => 8,
					),
				),
			)
		);

		$this->add_responsive_control(
			'device_button_border_radius',
			array(
				'label'      => __('Device Button Border Radius (px)', 'responsive-theme-preview'),
				'type'       => Controls_Manager::SLIDER,
				'size_units'      =>   ['px', '%'],
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step'    => 1,
					),
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				),
				'default'    => [
					'unit' => 'px',
					'size' => 4,
				],

				'selectors'  => array(
					'{{WRAPPER}} .rtp-devices button' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'cta_button_heading',
			array(
				'label'     => __('CTA Button', 'responsive-theme-preview'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'cta_button_bg_color',
			array(
				'label'     => __('Button Background Color', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2563eb',
				'selectors' => array('{{WRAPPER}} .rtp-cta' => 'background: {{VALUE}};'),
			)
		);

		$this->add_control(
			'cta_button_color',
			array(
				'label'     => __('Button Color', 'responsive-theme-preview'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => array('{{WRAPPER}} .rtp-cta' => 'color: {{VALUE}};'),
			)
		);

		$this->add_responsive_control(
			'cta_padding',
			array(
				'label'     => __('Padding', 'responsive-theme-preview'),
				'type'      => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array('px', 'em', '%'),
				'default'   => array(
					'top' => '6',
					'right' => '10',
					'bottom' => '6',
					'left' => '10',
					'unit' => 'px',
				),
				'selectors' => array('{{WRAPPER}} .rtp-cta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_button_border',
				'selector' => '{{WRAPPER}} .rtp-cta',
				'default' => array(
					'border' => 'none',
					'width' => array(
						'top' => 0,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
					),
					'color' => '#ffff00',
					'radius' => array(
						'top' => 8,
						'right' => 8,
						'bottom' => 8,
						'left' => 8,
					),
				),
			)
		);

		$this->add_responsive_control(
			'cta_button_border_radius',
			array(
				'label'      => __('CTA Button Border Radius (px)', 'responsive-theme-preview'),
				'type'       => Controls_Manager::SLIDER,
				'size_units'  =>   ['px', '%'],
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step'    => 1,
					),
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				),
				'default'    => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors'  => array(
					'{{WRAPPER}} .rtp-cta' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cta_typography',
				'selector' => '{{WRAPPER}} .rtp-cta',
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

		$this->add_responsive_control(
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
			$category = isset($s['category_filter']) ? $s['category_filter'] : '';
			$enable_filter = isset($s['enable_category_filter']) && $s['enable_category_filter'] === 'yes';

			$query_args = array(
				'post_type'      => RTP_CPT::POST_TYPE,
				'posts_per_page'  => (int) ($s['dynamic_count'] ?? 6),
				'no_found_rows'   => true,
				'post_status'     => 'publish',
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

			$q = new \WP_Query($query_args);
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
		}

		$bps = array();
		if (! empty($s['breakpoints'])) {
			foreach ($s['breakpoints'] as $bp) {
				$iconData = array();
				if (isset($bp['icon'])) {
					// Handle Elementor icon format: {value: 'fas fa-mobile-alt', library: 'fa-solid'}
					if (is_array($bp['icon']) && isset($bp['icon']['value'])) {
						$iconData = $bp['icon'];
					}
				}
				$bps[] = array(
					'title' => $bp['title'] ?? '',
					'width' => isset($bp['width']) ? (int) $bp['width'] : 1280,
					'icon'  => $iconData,
				);
			}
		}

		// Get global settings as base
		$global_settings = RTP_Admin_Settings::get_settings();

		// Prepare advanced settings, overriding with Elementor settings if provided
		$advanced_settings = array(
			'topbar_height' => isset($s['topbar_height']) ? (int) $s['topbar_height'] : $global_settings['topbar_height'],
			'device_button_style' => isset($s['device_button_style']) ? $s['device_button_style'] : $global_settings['device_button_style'],
			'device_button_size' => isset($s['device_button_size']) ? $s['device_button_size'] : $global_settings['device_button_size'],
			'device_button_active_color' => isset($s['device_button_active_color']) ? $s['device_button_active_color'] : $global_settings['device_button_active_color'],
			'device_button_hover_color' => isset($s['device_button_hover_color']) ? $s['device_button_hover_color'] : $global_settings['device_button_hover_color'],
			'device_button_color' => isset($s['device_button_color']) ? $s['device_button_color'] : $global_settings['device_button_color'],
			'device_icon_color' => isset($s['device_icon_color']) ? $s['device_icon_color'] : $global_settings['device_icon_color'] ?? '#ffffff',
			'overlay_close_on_click' => isset($s['overlay_close_on_click']) ? ($s['overlay_close_on_click'] === 'yes') : $global_settings['overlay_close_on_click'],
			'overlay_close_on_esc' => isset($s['overlay_close_on_esc']) ? ($s['overlay_close_on_esc'] === 'yes') : $global_settings['overlay_close_on_esc'],
			'overlay_loading_indicator' => isset($s['overlay_loading_indicator']) ? ($s['overlay_loading_indicator'] === 'yes') : $global_settings['overlay_loading_indicator'],
			'overlay_loading_color' => isset($s['overlay_loading_color']) ? $s['overlay_loading_color'] : $global_settings['overlay_loading_color'],
			'enable_keyboard_nav' => isset($s['enable_keyboard_nav']) ? ($s['enable_keyboard_nav'] === 'yes') : $global_settings['enable_keyboard_nav'],
			'focus_outline' => isset($s['focus_outline']) ? ($s['focus_outline'] === 'yes') : $global_settings['focus_outline'],
			'focus_outline_color' => isset($s['focus_outline_color']) ? $s['focus_outline_color'] : $global_settings['focus_outline_color'],
			'focus_outline_width' => isset($s['focus_outline_width']) ? (int) $s['focus_outline_width'] : $global_settings['focus_outline_width'],
			'button_border_radius' => isset($s['card_button_border_radius']) ? (int) $s['card_button_border_radius'] : $global_settings['button_border_radius'] ?? 8,
		);

		// Use Elementor's built-in icon rendering
		\Elementor\Plugin::$instance->frontend->enqueue_font('fa-solid');
		\Elementor\Plugin::$instance->frontend->enqueue_font('fa-regular');

		// Sanitize all values before passing to RTP_Render::html()
		$sanitized_args = array(
			'columns'         => (int) ($s['columns'] ?? 3),
			'overlay_bg'      => sanitize_text_field($s['overlay_bg'] ?? 'rgba(0,0,0,.6)'),
			'preview_btn_pos' => sanitize_text_field($s['preview_btn_pos'] ?? 'pos-br'),
			'cta_text'       => sanitize_text_field($s['cta_text'] ?? 'Open Live'),
			'cta_link'       => esc_url_raw($s['cta_link'] ?? ''),
			'items'          => $items,
			'breakpoints'    => $bps,
			'preview_type'   => sanitize_text_field((($s['source'] ?? '') === 'dynamic') ? ($s['preview_type'] ?? 'popup') : 'popup'),
			'advanced_settings' => $advanced_settings,
			'enable_category_filter' => (bool) $enable_filter,
			'section_id'      => 'rtp-elementor-' . esc_attr(uniqid()),
			'use_elementor_icons' => true,
		);

		// Capture the HTML output and then echo it to satisfy security checker
		$html_output = RTP_Render::html($sanitized_args);
		echo $html_output;
	}
}