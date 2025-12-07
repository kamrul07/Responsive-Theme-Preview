<?php
if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('\Bricks\Element')) {
	return;
}

class RTP_Bricks_Element extends \Bricks\Element {
	public $category = 'general';
	public $name = 'responsive_preview';
	public $icon = 'ti-desktop';
	public $css_selector = '.rtp-grid';

	public function get_label() {
		return esc_html__('Responsive Preview', 'responsive-theme-preview');
	}

	/**
	 * Get category options for select control
	 */
	public static function get_category_options() {
		$options = array(
			'' => esc_html__('All Categories', 'responsive-theme-preview'),
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
	public function set_control_groups() {

		// Group shown when “Card” tab is active
		$this->control_groups['card'] = [
			'title' => esc_html__('Card style', 'responsive-theme-preview'),
			'tab'   => 'content',
		];
		// Group shown when “Filter” tab is active
		$this->control_groups['filter'] = [
			'title' => esc_html__('Filter style', 'responsive-theme-preview'),
			'tab'   => 'content',
		];

		// Group shown when "Topbar" tab is active
		$this->control_groups['topbar'] = [
			'title' => esc_html__('Topbar Settings', 'responsive-theme-preview'),
			'tab'   => 'content',
		];

		// Group shown when "Advanced" tab is active
		$this->control_groups['advanced'] = [
			'title' => esc_html__('Advanced Settings', 'responsive-theme-preview'),
			'tab'   => 'content',
		];
	}

	public function set_controls() {
		$this->controls['columns'] = array(
			'tab'    => 'content',
			'label'  => esc_html__('Columns', 'responsive-theme-preview'),
			'type'    => 'select',
			'options' => array(
				'1'  => '1',
				'2'  => '2',
				'3'  => '3',
				'4'  => '4',
			),
			'default' => '3',
		);

		$this->controls['source'] = array(
			'tab'     => 'content',
			'label'   => esc_html__('Source', 'responsive-theme-preview'),
			'type'    => 'select',
			'options' => array(
				'static'  => 'Static (Repeater)',
				'dynamic' => 'Dynamic (CPT: Previews)',
			),
			'default' => 'static',
		);

		$this->controls['dynamic_count'] = array(
			'tab'      => 'content',
			'label'    => esc_html__('Items (dynamic)', 'responsive-theme-preview'),
			'type'     => 'number',
			'default'  => 6,
			'min'      => 1,
			'max'      => 48,
			'required' => array('source', '=', 'dynamic'),
		);

		$this->controls['preview_type'] = array(
			'tab'      => 'content',
			'label'    => esc_html__('Preview Type', 'responsive-theme-preview'),
			'type'     => 'select',
			'options'  => array(
				'popup' => 'Popup',
				'page'  => 'Separate URL',
			),
			'default'  => 'popup',
			'required' => array('source', '=', 'dynamic'),
		);

		$this->controls['category_filter'] = array(
			'tab'      => 'content',
			'label'    => esc_html__('Filter by Category', 'responsive-theme-preview'),
			'type'     => 'select',
			'options'  => self::get_category_options(),
			'default'  => '',
			'required' => array('source', '=', 'dynamic'),
		);

		$this->controls['enable_category_filter'] = array(
			'tab'      => 'content',
			'label'    => esc_html__('Enable Frontend Category Filter', 'responsive-theme-preview'),
			'type'     => 'checkbox',
			'default'  => false,
			'required' => array('source', '=', 'dynamic'),
		);

		$this->controls['items'] = array(
			'tab'      => 'content',
			'label'    => esc_html__('Items', 'responsive-theme-preview'),
			'type'     => 'repeater',
			'required' => array('source', '=', 'static'),
			'titleProperty' => 'title',
			'fields'   => array(
				'title' => [
					'label' => esc_html__('Title', 'bricks'),
					'type' => 'text',
				],
				'image' => [
					'label' => esc_html__('Image', 'bricks'),
					'type' => 'image',
				],
				'url' => [
					'label' => esc_html__('Preview URL', 'bricks'),
					'type' => 'text',
				],
				'btn' => [
					'label' => esc_html__('Button Text', 'bricks'),
					'type' => 'text',
				],
			),
			'default' => array(
				array(
					'title' => 'Preview 1',
					'image' => '',
					'url'   => '',
					'btn'   => 'Preview',
				),
				array(
					'title' => 'Preview 2',
					'image' => '',
					'url'   => '',
					'btn'   => 'Preview',
				),
				array(
					'title' => 'Preview 3',
					'image' => '',
					'url'   => '',
					'btn'   => 'Preview',
				),
			),
		);

		$this->controls['breakpoints'] = array(
			'tab'    => 'content',
			'label'  => esc_html__('Breakpoints', 'responsive-theme-preview'),
			'type'   => 'repeater',
			'fields' => array(

				'title' => array(
					'label' => esc_html__('Title', 'responsive-theme-preview'),
					'type'  => 'text',
				),
				'width' => array(
					'label' => esc_html__('Width', 'responsive-theme-preview'),
					'type'  => 'number',
				),
				'icon' => array(
					'label' => esc_html__('Icon', 'responsive-theme-preview'),
					'type'  => 'icon',
					'default' => 'ti-desktop',
				),
			),
			'default' => array(
				array('title' => 'Desktop', 'width' => 1280, 'icon' => 'ti-desktop'),
				array('title' => 'Tablet',  'width' => 768,  'icon' => 'ti-tablet'),
				array('title' => 'Mobile',  'width' => 375,  'icon' => 'ti-mobile'),
			),
		);

		$this->controls['cta_text'] = array(
			'tab'     => 'content',
			'label'   => esc_html__('CTA Text', 'responsive-theme-preview'),
			'type'    => 'text',
			'default' => 'Open Live',
		);

		$this->controls['cta_link'] = array(
			'tab'     => 'content',
			'label'   => esc_html__('CTA Link', 'responsive-theme-preview'),
			'type'    => 'text',
			'default' => '',
		);

		$this->controls['cardbg'] = [
			'tab' => 'content',
			'label' => esc_html__('Card BG', 'responsive-theme-preview'),
			'type' => 'color',
			'group' => 'card',
			'inline' => true,
			'css' => [
				[
					'property' => 'background',
					'selector' => '.rtp-card',
				]
			],
			'default' => [
				'hex' => '#fff',
				'rgb' => 'rgba(255, 255, 255, 0.9)',
			],
		];

		$this->controls['cardBorder'] = [
			'tab' => 'content',
			'group' => 'card',
			'label' => esc_html__('Card border', 'responsive-theme-preview'),
			'type' => 'border',
			'css' => [
				[
					'property' => 'border',
					'selector' => '.rtp-card',
				],
			],
			'inline' => true,
			'small' => true,
			'default' => [
				'width' => [
					'top' => 1,
					'right' => 1,
					'bottom' => 1,
					'left' => 1,
				],
				'style' => 'solid',
				'color' => [
					'hex' => '#e5e7eb',
				],
				'radius' => [
					'top' => 8,
					'right' => 8,
					'bottom' => 8,
					'left' => 8,
				],
			],


		];

		$this->controls['titleTypography'] = [
			'tab' => 'content',
			'group' => 'card',
			'label' => esc_html__('Title Typography', 'responsive-theme-preview'),
			'type' => 'typography',
			'css' => [
				[
					'property' => 'typography',
					'selector' => '.rtp-title',
				],
			],
			'inline' => true,
		];

		$this->controls['pbuttonbg'] = [
			'tab' => 'content',
			'label' => esc_html__('Button bg', 'responsive-theme-preview'),
			'type' => 'color',
			'group' => 'card',
			'inline' => true,
			'css' => [
				[
					'property' => 'background',
					'selector' => '.rtp-open',
				]
			],
			'default' => [
				'hex' => '#1f2937',
				'rgb' => 'rgba(31, 41, 55, 0.9)',
			],
		];

		$this->controls['pbuttonTypography'] = [
			'tab' => 'content',
			'group' => 'card',
			'label' => esc_html__('Button Typography', 'responsive-theme-preview'),
			'type' => 'typography',
			'css' => [
				[
					'property' => 'typography',
					'selector' => '.rtp-open',
				],
			],
			'inline' => true,
		];

		$this->controls['pbuttonBorder'] = [
			'tab' => 'content',
			'group' => 'card',
			'label' => esc_html__('Button border', 'responsive-theme-preview'),
			'type' => 'border',
			'css' => [
				[
					'property' => 'border',
					'selector' => '.rtp-open',
				],
			],
			'inline' => true,
			'small' => true,
			'default' => [
				'width' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
				],
				'style' => 'solid',
				'color' => [
					'hex' => '#1f2937',
				],
				'radius' => [
					'top' => 8,
					'right' => 8,
					'bottom' => 8,
					'left' => 8,
				],
			],


		];

		$this->controls['card_button_border_radius'] = array(
			'tab'     => 'content',
			'group'    => 'card',
			'label'     => esc_html__('Button Border Radius (px)', 'responsive-theme-preview'),
			'type'      => 'number',
			'default'   => 8,
			'min'       => 0,
			'max'       => 50,
			'css' => [
				[
					'property' => 'border-radius',
					'selector' => '.rtp-open',
				]
			],
		);

		//filter settings

		// $this->controls['filterDisplay'] = [
		// 	'tab' => 'content',
		// 	'label' => esc_html__('Display', 'responsive-theme-preview'),
		// 	'type' => 'select',
		// 	'group' => 'filter',
		// 	'options' => [
		// 		'block' => esc_html__('Block', 'responsive-theme-preview'),
		// 		'inline-block' => esc_html__('Inline Block', 'responsive-theme-preview'),
		// 		'flex' => esc_html__('Flex', 'responsive-theme-preview'),
		// 		'inline-flex' => esc_html__('Inline Flex', 'responsive-theme-preview'),
		// 	],
		// 	'inline' => true,
		// 	'css' => [
		// 		[
		// 			'property' => 'display',
		// 			'selector' => '.rtp-category-filter-inner',
		// 		],
		// 	],
		// 	'placeholder' => esc_html__('Select', 'responsive-theme-preview'),
		// 	'default' => 'flex',
		// ];

		$this->controls['filterWidth'] = array(
			'tab'     => 'content',
			'group'    => 'filter',
			'label'     => esc_html__('Single filter width', 'responsive-theme-preview'),
			'type'      => 'number',
			'default'   => "auto",
			'css' => [
				[
					'property' => 'width',
					'selector' => '.singlecategory-filter',
				]
			],
		);

		$this->controls['directionFilter'] = [
			'tab'   => 'content',
			'group' => 'filter',
			'label' => esc_html__('Direction', 'responsive-theme-preview'),
			'type'  => 'direction',
			'css'   => [
				[
					'property' => 'flex-direction',
					'selector' => '.rtp-category-filter-inner',

				],
			],
		];
		$this->controls['alignItemsFilter'] = [
			'tab'   => 'content',
			'group' => 'filter',
			'label' => esc_html__('Align items', 'responsive-theme-preview'),
			'type'  => 'align-items',
			'css'   => [
				[
					'property' => 'align-items',
					'selector' => '.rtp-category-filter-inner',
				],
			],
		];
		$this->controls['justifyContentFilter'] = [
			'tab'   => 'content',
			'group' => 'filter',
			'label' => esc_html__('Justify content', 'responsive-theme-previewbricks'),
			'type'  => 'justify-content',
			'css'   => [
				[
					'property' => 'justify-content',
					'selector' => '.rtp-category-filter-inner',
				],
			],
		];
		$this->controls['gapfilter'] = array(
			'tab'     => 'content',
			'group'    => 'filter',
			'label'     => esc_html__('Gap Between Filters', 'responsive-theme-preview'),
			'type'      => 'number',
			'default'   => "10px",
			'css' => [
				[
					'property' => 'gap',
					'selector' => '.rtp-category-filter-inner',
				]
			],
		);
		$this->controls['filterWrap'] = [
			'tab' => 'content',
			'group' => "filter",
			'label' => esc_html__('Flex wrap', 'responsive-theme-preview'),
			'type' => 'select',
			'options' => [
				'no-wrap' => esc_html__('No Wrap', 'responsive-theme-preview'),
				'wrap' => esc_html__('Wrap', 'responsive-theme-preview'),
			],
			'inline' => true,
			'css' => [
				[
					'property' => 'flex-wrap',
					'selector' => '.rtp-category-filter-inner',
				],
			],
			'default' => 'no-wrap',
		];
		$this->controls['filter_title_typography'] = [
			'tab' => 'content',
			'group' => 'filter',
			'label' => esc_html__('Typography', 'responsive-theme-preview'),
			'type' => 'typography',
			'css' => [
				[
					'property' => 'typography',
					'selector' => '.singlecategory-filter',
				],
			],
			'inline' => true,
		];

		$this->controls['filter_title_color'] = array(
			'tab'     => 'content',
			'group'    => 'filter',
			'label'     => esc_html__('Filter item Color', 'responsive-theme-preview'),
			'type'      => 'color',
			'default'   => '#ffffff',
			'css' => [
				[
					'property' => 'color',
					'selector' => '.singlecategory-filter',
				]
			],
		);
		$this->controls['filter_bg_color'] = array(
			'tab'     => 'content',
			'group'    => 'filter',
			'label'     => esc_html__('Background', 'responsive-theme-preview'),
			'type'      => 'color',
			'default'   => '#0f172a',
			'css' => [
				[
					'property' => 'background',
					'selector' => '.singlecategory-filter',
				]
			],
		);
		$this->controls['filterBorder'] = [
			'tab' => 'content',
			'group' => 'filter',
			'label' => esc_html__('Border', 'responsive-theme-preview'),
			'type' => 'border',
			'css' => [
				[
					'property' => 'border',
					'selector' => '.singlecategory-filter',
				],
			],
			'inline' => true,
			'small' => true,
			'default' => [
				'width' => [
					'top' => 1,
					'right' => 1,
					'bottom' => 1,
					'left' => 0,
				],
				'style' => 'solid',
				'color' => [
					'hex' => '#0f172a',
				],
				'radius' => [
					'top' => 20,
					'right' => 20,
					'bottom' => 20,
					'left' => 20,
				],
			],
		];
		$this->controls['filterPadding'] = [
			'tab' => 'content',
			'group' => 'filter',
			'label' => esc_html__('Padding', 'responsive-theme-preview'),
			'type' => 'dimensions',
			'css' => [
				[
					'property' => 'padding',
					'selector' => '.singlecategory-filter',
				]
			],
			'default' => [
				'top' => '5px',
				'right' => '10px',
				'bottom' => '5px',
				'left' => '10px',
			],
		];
		$this->controls['advanced_heading_filter'] = array(
			'tab'   => 'content',
			'group'  => 'filter',
			'label'  => esc_html__('Active', 'responsive-theme-preview'),
			'type'   => 'separator',
		);

		$this->controls['filter_title_color_active'] = array(
			'tab'     => 'content',
			'group'    => 'filter',
			'label'     => esc_html__('Font Color', 'responsive-theme-preview'),
			'type'      => 'color',
			'default'   => '#ffffff',
			'css' => [
				[
					'property' => 'color',
					'selector' => '.singlecategory-filter.active',
				]
			],
		);

		$this->controls['active_bg_color'] = array(
			'tab'     => 'content',
			'group'    => 'filter',
			'label'     => esc_html__('Background', 'responsive-theme-preview'),
			'type'      => 'color',
			'default'   => '#0463c8',
			'css' => [
				[
					'property' => 'background',
					'selector' => '.singlecategory-filter.active',
				]
			],
		);
		$this->controls['filterBorderActive'] = [
			'tab' => 'content',
			'label' => esc_html__('Border', 'responsive-theme-preview'),
			'type' => 'border',
			'css' => [
				[
					'property' => 'border',
					'selector' => '.singlecategory-filter.active',
				],
			],
			'inline' => true,
			'small' => true,
			'default' => [
				'width' => [
					'top' => 1,
					'right' => 1,
					'bottom' => 1,
					'left' => 0,
				],
				'style' => 'solid',
				'color' => [
					'hex' => '#0463c8',
				],
				'radius' => [
					'top' => 20,
					'right' => 20,
					'bottom' => 20,
					'left' => 20,
				],
			],
		];
		$this->controls['filterBorderActive'] = [
			'tab' => 'content',
			'group' => 'filter',
			'label' => esc_html__('Border', 'responsive-theme-preview'),
			'type' => 'border',
			'css' => [
				[
					'property' => 'border',
					'selector' => '.singlecategory-filter.active',
				],
			],
			'inline' => true,
			'small' => true,
			'default' => [
				'width' => [
					'top' => 1,
					'right' => 1,
					'bottom' => 1,
					'left' => 0,
				],
				'style' => 'solid',
				'color' => [
					'hex' => '#0f172a',
				],
				'radius' => [
					'top' => 20,
					'right' => 20,
					'bottom' => 20,
					'left' => 20,
				],
			],
		];
		//topbar settings
		$this->controls['advanced_heading_general'] = array(
			'tab'   => 'content',
			'group'  => 'topbar',
			'label'  => esc_html__('General', 'responsive-theme-preview'),
			'type'   => 'separator',
		);

		$this->controls['topbar_height'] = array(
			'tab'     => 'content',
			'group'    => 'topbar',
			'label'     => esc_html__('Topbar Height (px)', 'responsive-theme-preview'),
			'type'      => 'number',
			'default'   => 52,
			'min'       => 40,
			'css' => [
				[
					'property' => 'height',
					'selector' => '.rtp-topbar',
				],
				[
					'property' => 'top',
					'selector' => '.rtp-framewrap',
				]
			],
		);

		$this->controls['topbar_title_typography'] = [
			'tab' => 'content',
			'group' => 'topbar',
			'label' => esc_html__('Title Typography', 'responsive-theme-preview'),
			'type' => 'typography',
			'css' => [
				[
					'property' => 'typography',
					'selector' => '.rtp-topbar-title',
				],
			],
			'inline' => true,
		];

		$this->controls['topbar_title_color'] = array(
			'tab'     => 'content',
			'group'    => 'topbar',
			'label'     => esc_html__('Title Color', 'responsive-theme-preview'),
			'type'      => 'color',
			'default'   => '#ffffff',
			'css' => [
				[
					'property' => 'color',
					'selector' => '.rtp-topbar-title',
				]
			],
		);
		$this->controls['overlay_bg'] = array(
			'tab'     => 'content',
			'group'   => 'topbar',
			'label'   => esc_html__('Overlay BG', 'responsive-theme-preview'),
			'type'    => 'color',
			'default' => 'rgba(0,0,0,.6)',
			'css'     => array(
				array(
					'property' => 'background-color',
					'selector' => '.rtp-overlay:after',
				),
			),
		);

		$this->controls['overlay_opacity'] = [
			'tab' => 'content',
			'group' => 'topbar',
			'label' => esc_html__('CSS filters', 'responsive-theme-preview'),
			'type' => 'filters',
			'inline' => true,
			'css' => [
				[
					'property' => 'filter',
					'selector' => '.rtp-overlay:after',
				],
			],
		];




		$this->controls['preview_btn_pos'] = array(
			'tab'     => 'content',
			'label'   => esc_html__('Preview Button Position', 'responsive-theme-preview'),
			'type'    => 'select',
			'options' => array(
				'pos-br' => 'Bottom Right',
				'pos-bl' => 'Bottom Left',
				'pos-tr' => 'Top Right',
				'pos-tl' => 'Top Left',
				'pos-center' => 'Center',
			),
			'default' => 'pos-br',
		);
		$this->controls['advanced_heading_device'] = array(
			'tab'   => 'content',
			'group'  => 'topbar',
			'label'  => esc_html__('Device Button', 'responsive-theme-preview'),
			'type'   => 'separator',
		);
		$this->controls['device_button_color'] = array(
			'tab'     => 'content',
			'group'    => 'topbar',
			'label'     => esc_html__('Button BG Color', 'responsive-theme-preview'),
			'type'      => 'color',
			'default'   => '#1d4ed8',
			'css' => [
				[
					'property' => 'background',
					'selector' => '.rtp-devices button',
				]
			],
		);
		$this->controls['device_button_active_color'] = array(
			'tab'     => 'content',
			'group'    => 'topbar',
			'label'     => esc_html__('Active Button Color', 'responsive-theme-preview'),
			'type'      => 'color',
			'default'   => '#2563eb',
			'css' => [
				[
					'property' => 'background',
					'selector' => '.rtp-devices button.active',
				]
			],
		);

		$this->controls['device_icon_color'] = array(
			'tab'     => 'content',
			'group'    => 'topbar',
			'label'     => esc_html__('Device Icon Color', 'responsive-theme-preview'),
			'type'      => 'color',
			'default'   => '#ffffff',
			'css' => [
				[
					'property' => 'color',
					'selector' => '.rtp-devices button i',
				]
			],
		);
		$this->controls['buttonDevice'] = [
			'tab'   => 'content',
			'group' => 'topbar',
			'label' => esc_html__('Padding', 'responsive-theme-preview'),
			'type'  => 'dimensions',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => '.rtp-devices button',
				]
			],
			'default' => [
				'top' => '10px',
				'right' => '15px',
				'bottom' => '10px',
				'left' => '15px',
			],
		];
		$this->controls['devicebuttonBorder'] = [
			'tab'   => 'content',
			'group' => 'topbar',
			'label' => esc_html__('Title border', 'responsive-theme-preview'),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => '.rtp-devices button',
				],
			],
			'inline' => true,
			'small' => true,
			'default' => [
				'width' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
				],
				'style' => 'none',
				'color' => [
					'hex' => '#ffff00',
				],
				'radius' => [
					'top' => 8,
					'right' => 8,
					'bottom' => 8,
					'left' => 8,
				],
			],


		];
		$this->controls['advanced_heading_cta'] = array(
			'tab'   => 'content',
			'group'  => 'topbar',
			'label'  => esc_html__('CTA Button', 'responsive-theme-preview'),
			'type'   => 'separator',
		);
		$this->controls['cta_button_bg_color'] = array(
			'tab'     => 'content',
			'group'    => 'topbar',
			'label'     => esc_html__('Button BG Color', 'responsive-theme-preview'),
			'type'      => 'color',
			'default'   => '#2563eb',
			'css' => [
				[
					'property' => 'color',
					'selector' => '.rtp-cta',
				]
			],
		);
		$this->controls['cta_button_color'] = array(
			'tab'     => 'content',
			'group'    => 'topbar',
			'label'     => esc_html__('Button Color', 'responsive-theme-preview'),
			'type'      => 'color',
			'default'   => '#fff',
			'css' => [
				[
					'property' => 'background',
					'selector' => '.rtp-cta',
				]
			],
		);

		$this->controls['cta_padding'] = [
			'tab'   => 'content',
			'group' => 'topbar',
			'label' => esc_html__('Padding', 'responsive-theme-preview'),
			'type'  => 'dimensions',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => '.rtp-cta',
				]
			],
			'default' => [
				'top' => '6px',
				'right' => '10px',
				'bottom' => '6px',
				'left' => '10px',
			],
		];
		$this->controls['ctabuttonBorder'] = [
			'tab'   => 'content',
			'group' => 'topbar',
			'label' => esc_html__('border', 'responsive-theme-preview'),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => '.rtp-cta',
				],
			],
			'inline' => true,
			'small' => true,
			'default' => [
				'width' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
				],
				'style' => 'none',
				'color' => [
					'hex' => '#ffff00',
				],
				'radius' => [
					'top' => 8,
					'right' => 8,
					'bottom' => 8,
					'left' => 8,
				],
			],


		];
		$this->controls['cta_typography'] = [
			'tab' => 'content',
			'group' => 'topbar',
			'label' => esc_html__('Typography', 'responsive-theme-preview'),
			'type' => 'typography',
			'css' => [
				[
					'property' => 'typography',
					'selector' => '.rtp-cta',
				],
			],
			'inline' => true,
		];
		// Overlay settings
		$this->controls['advanced_heading_overlay'] = array(
			'tab'   => 'content',
			'group'  => 'advanced',
			'label'  => esc_html__('Close Settings', 'responsive-theme-preview'),
			'type'   => 'separator',
		);

		$this->controls['overlay_close_on_click'] = array(
			'tab'    => 'content',
			'group'   => 'advanced',
			'label'   => esc_html__('Close on Click Outside', 'responsive-theme-preview'),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['overlay_close_on_esc'] = array(
			'tab'    => 'content',
			'group'   => 'advanced',
			'label'   => esc_html__('Close on ESC Key', 'responsive-theme-preview'),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['overlay_loading_indicator'] = array(
			'tab'    => 'content',
			'group'   => 'advanced',
			'label'   => esc_html__('Show Loading Indicator', 'responsive-theme-preview'),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['overlay_loading_color'] = array(
			'tab'      => 'content',
			'group'     => 'advanced',
			'label'     => esc_html__('Loading Indicator Color', 'responsive-theme-preview'),
			'type'      => 'color',
			'default'   => '#2563eb',
			'required'  => array('overlay_loading_indicator', '=', true),
		);

		// Accessibility settings
		$this->controls['advanced_heading_accessibility'] = array(
			'tab'   => 'content',
			'group'  => 'advanced',
			'label'  => esc_html__('Accessibility Settings', 'responsive-theme-preview'),
			'type'   => 'separator',
		);

		$this->controls['enable_keyboard_nav'] = array(
			'tab'    => 'content',
			'group'   => 'advanced',
			'label'   => esc_html__('Enable Keyboard Navigation', 'responsive-theme-preview'),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['focus_outline'] = array(
			'tab'    => 'content',
			'group'   => 'advanced',
			'label'   => esc_html__('Show Focus Outline', 'responsive-theme-preview'),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['focus_outline_color'] = array(
			'tab'      => 'content',
			'group'     => 'advanced',
			'label'     => esc_html__('Focus Outline Color', 'responsive-theme-preview'),
			'type'      => 'color',
			'default'   => '#2563eb',
			'required'  => array('focus_outline', '=', true),
		);

		$this->controls['focus_outline_width'] = array(
			'tab'      => 'content',
			'group'     => 'advanced',
			'label'     => esc_html__('Focus Outline Width (px)', 'responsive-theme-preview'),
			'type'      => 'number',
			'default'   => 2,
			'min'       => 1,
			'max'       => 5,
			'required'  => array('focus_outline', '=', true),
		);
	}

	public function render() {
		$s     = $this->settings;
		$items = array();

		// Get current Bricks element DOM id (e.g., "brxe-abc123")
		$section_id = '';
		if (isset($this->id) && !empty($this->id)) {
			$section_id = 'brxe-' . $this->id;
		}



		if (($s['source'] ?? 'static') === 'static' && !empty($s['items']) && is_array($s['items'])) {
			foreach ($s['items'] as $it) {
				$img = '';
				if (!empty($it['image'])) {
					if (is_array($it['image']) && !empty($it['image']['url'])) {
						$img = $it['image']['url'];
					} elseif (is_string($it['image'])) {
						$img = $it['image'];
					}
				}
				$items[] = array(
					'image' => $img,
					'title' => isset($it['title']) ? $it['title'] : '',
					'url'   => isset($it['url']) ? $it['url'] : '',
					'btn'   => isset($it['btn']) ? $it['btn'] : '',
				);
			}
		} else {
			$count = isset($s['dynamic_count']) ? (int)$s['dynamic_count'] : 6;
			$category = isset($s['category_filter']) ? $s['category_filter'] : '';
			$enable_filter = isset($s['enable_category_filter']) ? $s['enable_category_filter'] : false;

			$query_args = array(
				'post_type'      => RTP_CPT::POST_TYPE,
				'posts_per_page' => $count,
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
		if (!empty($s['breakpoints']) && is_array($s['breakpoints'])) {
			foreach ($s['breakpoints'] as $bp) {
				$iconVal = isset($bp['icon']) ? $bp['icon'] : '';
				$iconUrl = '';
				$iconClass = '';

				// Debug: Log the icon value to understand its format
				error_log('RTP Bricks Icon Value: ' . print_r($iconVal, true));

				// Handle different icon formats from Bricks
				if (is_array($iconVal)) {
					// Bricks might return icon as array with different structure
					if (!empty($iconVal['library']) && !empty($iconVal['name'])) {
						// Font icon format: {library: 'themify', name: 'desktop'}
						$iconClass = 'ti-' . $iconVal['name'];
					} elseif (!empty($iconVal['url'])) {
						// Image icon format: {url: 'https://...'}
						$iconUrl = $iconVal['url'];
					} elseif (!empty($iconVal['src'])) {
						// Alternative image format: {src: 'https://...'}
						$iconUrl = $iconVal['src'];
					} elseif (!empty($iconVal['icon'])) {
						// Nested icon format: {icon: 'ti-desktop'}
						$iconClass = $iconVal['icon'];
					}
				} elseif (is_string($iconVal)) {
					if (preg_match('/^https?:\/\//', $iconVal)) {
						// Direct URL string
						$iconUrl = $iconVal;
					} elseif (!empty($iconVal)) {
						// Icon class string like 'ti-desktop'
						$iconClass = $iconVal;
					}
				}

				// If we have an icon class, use it; otherwise use the URL
				$finalIcon = $iconClass ? $iconClass : $iconUrl;

				$bps[] = array(
					'title' => isset($bp['title']) ? $bp['title'] : '',
					'width' => isset($bp['width']) ? (int)$bp['width'] : 1280,
					'icon'  => $finalIcon,
				);
			}
		}
		// Get global settings as base
		$global_settings = RTP_Admin_Settings::get_settings();

		// Prepare advanced settings, overriding with Bricks settings if provided
		$advanced_settings = array(
			'device_button_style' => isset($s['device_button_style']) ? $s['device_button_style'] : $global_settings['device_button_style'],
			'device_button_size' => isset($s['device_button_size']) ? $s['device_button_size'] : $global_settings['device_button_size'],
			'device_button_active_color' => isset($s['device_button_active_color']) ? $s['device_button_active_color'] : $global_settings['device_button_active_color'],
			'device_button_hover_color' => isset($s['device_button_hover_color']) ? $s['device_button_hover_color'] : $global_settings['device_button_hover_color'],
			'device_icon_color' => isset($s['device_icon_color']) ? $s['device_icon_color'] : $global_settings['device_icon_color'] ?? '#ffffff',
			'overlay_close_on_click' => isset($s['overlay_close_on_click']) ? ($s['overlay_close_on_click'] === true) : $global_settings['overlay_close_on_click'],
			'overlay_close_on_esc' => isset($s['overlay_close_on_esc']) ? ($s['overlay_close_on_esc'] === true) : $global_settings['overlay_close_on_esc'],
			'overlay_loading_indicator' => isset($s['overlay_loading_indicator']) ? ($s['overlay_loading_indicator'] === true) : $global_settings['overlay_loading_indicator'],
			'overlay_loading_color' => isset($s['overlay_loading_color']) ? $s['overlay_loading_color'] : $global_settings['overlay_loading_color'],
			'enable_keyboard_nav' => isset($s['enable_keyboard_nav']) ? ($s['enable_keyboard_nav'] === true) : $global_settings['enable_keyboard_nav'],
			'focus_outline' => isset($s['focus_outline']) ? ($s['focus_outline'] === true) : $global_settings['focus_outline'],
			'focus_outline_color' => isset($s['focus_outline_color']) ? $s['focus_outline_color'] : $global_settings['focus_outline_color'],
			'focus_outline_width' => isset($s['focus_outline_width']) ? (int) $s['focus_outline_width'] : $global_settings['focus_outline_width'],
			'button_border_radius' => isset($s['card_button_border_radius']) ? (int) $s['card_button_border_radius'] : $global_settings['button_border_radius'] ?? 8,
		);

		echo "<div id='" . esc_attr($section_id) . "' >";

		echo "<style>#$section_id #rtp-frame {
				height: calc(100vh - " . esc_attr($s['topbar_height'] ?? 3) . ");
			}</style>";
		echo RTP_Render::html(array(
			'columns'         => (int)($s['columns'] ?? 3),
			'overlay_bg'      => $s['overlay_bg'] ?? 'rgba(0,0,0,.6)',
			'preview_btn_pos' => $s['preview_btn_pos'] ?? 'pos-br',
			'cta_text'        => $s['cta_text'] ?? 'Open Live',
			'cta_link'        => $s['cta_link'] ?? '',
			'items'           => $items,
			'breakpoints'     => $bps,
			'preview_type'    => (($s['source'] ?? '') === 'dynamic') ? ($s['preview_type'] ?? 'popup') : 'popup',
			'advanced_settings' => $advanced_settings,
			'enable_category_filter' => $enable_filter,
			'section_id'      => $section_id,
		));
		echo "</div>";
	}
}