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
	public function set_control_groups() {

		// Group shown when “Card” tab is active
		$this->control_groups['card'] = [
			'title' => esc_html__('Card style', 'responsive-theme-preview'),
			'tab'   => 'content',
		];

		// Group shown when “Topbar” tab is active
		$this->control_groups['topbar'] = [
			'title' => esc_html__('Topbar Settings', 'responsive-theme-preview'),
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
					'label' => esc_html__('Icon Image URL', 'responsive-theme-preview'),
					'type'  => 'icon',
				),
			),
			'default' => array(
				array('title' => 'Desktop', 'width' => 1280, 'icon' => ''),
				array('title' => 'Tablet',  'width' => 768,  'icon' => ''),
				array('title' => 'Mobile',  'width' => 375,  'icon' => ''),
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
		$this->controls['overlay_bg'] = array(
			'tab'     => 'content',
			'group'  => 'topbar',
			'label'   => esc_html__('Overlay BG', 'responsive-theme-preview'),
			'type'    => 'color',
			'default' => 'rgba(0,0,0,.6)',
		);

		$this->controls['topbar_bg'] = array(
			'tab'     => 'content',
			'group'  => 'topbar',
			'label'   => esc_html__('Topbar BG', 'responsive-theme-preview'),
			'type'    => 'color',
			'default' => '#0f172a',
		);

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
	}

	public function render() {
		$s     = $this->settings;
		$items = array();

		// Get current Bricks element DOM id (e.g., "brxe-abc123")
		$section_id = '';
		if (method_exists($this, 'get_id') && $this->get_id()) {
			$section_id = 'brxe-' . $this->get_id();
		} elseif (property_exists($this, 'id') && !empty($this->id)) {
			$section_id = 'brxe-' . $this->id;
		} elseif (property_exists($this, 'element_id') && !empty($this->element_id)) {
			$section_id = 'brxe-' . $this->element_id;
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
			$q     = new \WP_Query(array(
				'post_type'      => RTP_CPT::POST_TYPE,
				'posts_per_page' => $count,
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
		}

		$bps = array();
		if (!empty($s['breakpoints']) && is_array($s['breakpoints'])) {
			foreach ($s['breakpoints'] as $bp) {
				$bps[] = array(
					'title' => isset($bp['title']) ? $bp['title'] : '',
					'width' => isset($bp['width']) ? (int)$bp['width'] : 1280,
					'icon'  => isset($bp['icon']) ? $bp['icon'] : '',
				);
			}
		}
		echo "<div id='" . esc_attr($section_id) . "'>";
		echo RTP_Render::html(array(
			'columns'         => (int)($s['columns'] ?? 3),
			'overlay_bg'      => $s['overlay_bg'] ?? 'rgba(0,0,0,.6)',
			'preview_btn_pos' => $s['preview_btn_pos'] ?? 'pos-br',
			'cta_text'        => $s['cta_text'] ?? 'Open Live',
			'cta_link'        => $s['cta_link'] ?? '',
			'items'           => $items,
			'breakpoints'     => $bps,
			'topbar_bg'       => $s['topbar_bg']["hex"] ?? '#0f172a',
			'preview_type'    => (($s['source'] ?? '') === 'dynamic') ? ($s['preview_type'] ?? 'popup') : 'popup',
		));
		echo "</div>";
	}
}
