<?php
if (! defined('ABSPATH')) {
	exit;
}

class RTP_Render {
	public static function html($args) {
		$columns         = isset($args['columns']) ? (int) $args['columns'] : 3;
		$overlay_bg      = isset($args['overlay_bg']) ? sanitize_text_field($args['overlay_bg']) : 'rgba(0,0,0,.6)';

		$preview_btn_pos = isset($args['preview_btn_pos']) ? sanitize_text_field($args['preview_btn_pos']) : 'pos-br';
		$cta_text        = (isset($args['cta_text']) && '' !== $args['cta_text']) ? sanitize_text_field($args['cta_text']) : __('Open Live', 'responsive-theme-preview');
		$cta_link        = isset($args['cta_link']) ? esc_url_raw($args['cta_link']) : '';
		$section_id       = isset($args['section_id']) ? sanitize_text_field($args['section_id']) : 'rtp-preview-' . uniqid();
		$preview_type    = isset($args['preview_type']) ? sanitize_text_field($args['preview_type']) : 'popup';

		$items = (isset($args['items']) && is_array($args['items'])) ? array_map(function ($item) {
			return array_map('sanitize_text_field', $item);
		}, $args['items']) : array();
		// Get global settings to use default breakpoints if not set in shortcode
		$global_settings = RTP_Admin_Settings::get_settings();

		$bps = (isset($args['breakpoints']) && is_array($args['breakpoints'])) ? array_map(function ($bp) {
			return array_map('sanitize_text_field', $bp);
		}, $args['breakpoints']) : (isset($global_settings['default_breakpoints']) && !empty($global_settings['default_breakpoints']) ? $global_settings['default_breakpoints'] : array(
			array('title' => 'Mobile', 'width' => 375, 'icon' => array('value' => 'fas fa-mobile-alt', 'library' => 'fa-solid')),
			array('title' => 'Tablet', 'width' => 768, 'icon' => array('value' => 'fas fa-tablet-alt', 'library' => 'fa-solid')),
			array('title' => 'Desktop', 'width' => 1280, 'icon' => array('value' => 'fas fa-desktop', 'library' => 'fa-solid')),
		));

		$bps_json = esc_attr(rawurlencode(wp_json_encode($bps)));

		// Always use global settings from admin settings page
		$advanced_settings = RTP_Admin_Settings::get_settings();

		// If specific settings are passed in args, merge them with global settings
		if (isset($args['advanced_settings']) && is_array($args['advanced_settings'])) {
			$override_settings = RTP_Advanced_Settings::sanitize_settings($args['advanced_settings']);
			$advanced_settings = array_merge($advanced_settings, $override_settings);
		}

		// Generate advanced CSS
		$advanced_css = RTP_Advanced_Settings::generate_css($advanced_settings);

		// Generate JavaScript config
		$js_config = RTP_Advanced_Settings::generate_js_config($advanced_settings);

		ob_start();
?>
<style>
<?php echo wp_kses_post($advanced_css);
?>
</style>
<div class='rtp-wrapper'>
    <?php if (isset($args['enable_category_filter']) && $args['enable_category_filter']) : ?>
    <div class="rtp-category-filter-wrapper">
        <div class="rtp-category-filter-inner">
            <div class="singlecategory-filter" data-cat="">All</div>
            <?php
						$categories = get_terms(array(
							'taxonomy' => 'rtp-category',
							'hide_empty' => true,
						));
						if (!is_wp_error($categories) && !empty($categories)) {
							foreach ($categories as $category) {
								echo '<div class="singlecategory-filter" data-cat="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</div>';
							}
						}
						?>
        </div>
    </div>
    <?php endif; ?>
    <div class="rtp-grid cols-<?php echo (int) $columns; ?>">
        <?php foreach ($items as $it) :
					$img  = ! empty($it['image']) ? esc_url($it['image']) : '';
					$ttl  = ! empty($it['title']) ? sanitize_text_field($it['title']) : '';
					$url  = ! empty($it['url']) ? esc_url($it['url']) : '';
					$btn  = ! empty($it['btn']) ? sanitize_text_field($it['btn']) : __('Preview', 'responsive-theme-preview');
					$pid  = ! empty($it['post_id']) ? (int) $it['post_id'] : 0;
					$plink = ! empty($it['permalink']) ? esc_url($it['permalink']) : '';
				?>
        <div class="rtp-card <?php echo esc_attr($preview_btn_pos); ?>" <?php echo isset($it['category_slug']) && !empty($it['category_slug']) ? 'data-category="' . esc_attr($it['category_slug']) . '"' : ''; ?>>
            <?php if ($img) : ?>
            <div class="rtp-thumb">
                <img src="<?php echo esc_attr($img); ?>" alt="<?php echo esc_attr($ttl); ?>">
                <button class="rtp-open <?php echo esc_attr($preview_btn_pos); ?>" data-mode="<?php echo esc_attr($preview_type); ?>" data-url="<?php echo esc_attr($url); ?>" data-title="<?php echo esc_attr($ttl); ?>" data-bps="<?php echo esc_attr($bps_json); ?>" data-cta-text="<?php echo esc_attr($cta_text); ?>" data-cta-link="<?php echo esc_attr($cta_link); ?>" <?php echo $pid ? 'data-postid="' . (int) $pid . '"' : ''; ?> <?php echo $plink ? 'data-permalink="' . esc_url($plink) . '"' : ''; ?>><?php echo esc_html($btn); ?></button>
            </div>
            <?php endif; ?>
            <?php if ($ttl) : ?><div class="rtp-title"><?php echo esc_html($ttl); ?></div><?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>



    <div class="topbarWrapper" id="<?php echo esc_attr($section_id); ?>">
        <div class="rtp-overlay dd" id="rtp-overlay">
            <div class="rtp-topbar">
                <div class="rtp-topbar-title" id="rtp-topbar-title"></div>
                <div class="rtp-devices">
                    <?php foreach ($bps as $bp) :
								$w = isset($bp['width']) ? (int) $bp['width'] : 1280;
								$t = isset($bp['title']) ? sanitize_text_field($bp['title']) : '';
								$iconVal = isset($bp['icon']) ? $bp['icon'] : '';
								$iconUrl = '';
								$iconClass = '';

								// Handle different icon formats
								if (is_array($iconVal)) {
									if (!empty($iconVal['url'])) {
										// Image icon format: {url: 'https://...'}
										$iconUrl = esc_url($iconVal['url']);
									} elseif (!empty($iconVal['src'])) {
										// Alternative image format: {src: 'https://...'}
										$iconUrl = esc_url($iconVal['src']);
									} elseif (!empty($iconVal['value'])) {
										// Elementor icon format: {value: 'fas fa-eye', library: 'fa-solid'}
										$iconClass = esc_attr($iconVal['value']);
									} elseif (!empty($iconVal['icon'])) {
										// Nested icon format: {icon: 'ti-desktop'}
										$iconClass = esc_attr($iconVal['icon']);
									}
								} elseif (is_string($iconVal) && preg_match('/^https?:\/\//', $iconVal)) {
									$iconUrl = esc_url($iconVal);
								} elseif (is_string($iconVal) && !empty($iconVal)) {
									// For icon classes like 'ti-desktop', 'ti-tablet', etc.
									$iconClass = esc_attr($iconVal);
								}
							?>
                    <button data-w="<?php echo (int) $w; ?>" title="<?php echo esc_attr($t); ?>">
                        <?php if ($iconUrl) : ?>
                        <img class="rtp-ic" src="<?php echo esc_attr($iconUrl); ?>" alt="<?php echo esc_attr($t); ?>" />
                        <?php elseif ($iconClass) : ?>
                        <?php if (isset($args['use_elementor_icons']) && $args['use_elementor_icons']) : ?>
                        <?php
											// Use Elementor's icon rendering for Elementor pages
											$icon_settings = array(
												'library' => 'fa-solid',
												'value' => $iconClass
											);
											$icon_html = \Elementor\Icons_Manager::render_icon($icon_settings, array('aria-hidden' => 'true'));
											// Extract just the SVG part from the returned HTML
											if (preg_match('/<i[^>]*>(.*?)<\/i>/s', $icon_html, $matches)) {
												echo wp_kses_post($matches[0]);
											}
											?>
                        <?php else : ?>
                        <i class="rtp-ic <?php echo esc_attr($iconClass); ?>"></i>
                        <?php endif; ?>
                        <?php else : ?>
                        <span class="rtp-ic"><?php echo esc_html($t ? substr($t, 0, 1) : '•'); ?></span>
                        <?php endif; ?>
                    </button>
                    <?php endforeach; ?>
                </div>
                <a class="rtp-cta" href="<?php echo esc_url($cta_link ? $cta_link : '#'); ?>" target="_blank" rel="noopener"><?php echo esc_html($cta_text); ?></a>
                <button class="rtp-close" aria-label="<?php esc_attr_e('Close', 'responsive-theme-preview'); ?>">✕</button>
            </div>
            <div class="rtp-framewrap"><iframe id="rtp-frame" src="" loading="lazy"></iframe></div>
        </div>
    </div>
</div>
<script>
window.RTPAdvanced = <?php echo wp_json_encode($js_config); ?>;
</script>
<?php
		return ob_get_clean();
	}
}