<?php
if (! defined('ABSPATH')) {
	exit;
}

class RTP_Render {
	public static function html($args) {
		$columns         = isset($args['columns']) ? (int) $args['columns'] : 3;
		$overlay_bg      = isset($args['overlay_bg']) ? $args['overlay_bg'] : 'rgba(0,0,0,.6)';
		$preview_btn_pos = isset($args['preview_btn_pos']) ? $args['preview_btn_pos'] : 'pos-br';
		$cta_text        = (isset($args['cta_text']) && '' !== $args['cta_text']) ? $args['cta_text'] : __('Open Live', 'responsive-theme-preview');
		$cta_link        = isset($args['cta_link']) ? $args['cta_link'] : '';
		$topbar_bg       = (isset($args['topbar_bg']) && '' !== $args['topbar_bg']) ? $args['topbar_bg'] : '#0f172a';
		$preview_type    = isset($args['preview_type']) ? $args['preview_type'] : 'popup';

		$items = (isset($args['items']) && is_array($args['items'])) ? $args['items'] : array();
		$bps   = (isset($args['breakpoints']) && is_array($args['breakpoints'])) ? $args['breakpoints'] : array(
			array('title' => 'Mobile',  'width' => 375,  'icon' => ''),
			array('title' => 'Tablet',  'width' => 768,  'icon' => ''),
			array('title' => 'Desktop', 'width' => 1280, 'icon' => ''),
		);
		$bps_json = esc_attr(rawurlencode(wp_json_encode($bps)));

		ob_start();
?>
<div class="rtp-grid cols-<?php echo (int) $columns; ?>">
    <?php foreach ($items as $it) :
				$img  = ! empty($it['image']) ? esc_url($it['image']) : '';
				$ttl  = ! empty($it['title']) ? sanitize_text_field($it['title']) : '';
				$url  = ! empty($it['url']) ? esc_url($it['url']) : '';
				$btn  = ! empty($it['btn']) ? sanitize_text_field($it['btn']) : __('Preview', 'responsive-theme-preview');
				$pid  = ! empty($it['post_id']) ? (int) $it['post_id'] : 0;
				$plink = ! empty($it['permalink']) ? esc_url($it['permalink']) : '';
			?>
    <div class="rtp-card <?php echo esc_attr($preview_btn_pos); ?>">
        <?php if ($img) : ?>
        <div class="rtp-thumb">
            <img src="<?php echo $img; ?>" alt="<?php echo esc_attr($ttl); ?>">
            <button class="rtp-open <?php echo esc_attr($preview_btn_pos); ?>" data-mode="<?php echo esc_attr($preview_type); ?>" data-url="<?php echo $url; ?>" data-title="<?php echo esc_attr($ttl); ?>" data-bps="<?php echo $bps_json; ?>" data-topbar="<?php echo esc_attr($topbar_bg); ?>" data-cta-text="<?php echo esc_attr($cta_text); ?>" data-cta-link="<?php echo esc_attr($cta_link); ?>" <?php echo $pid ? 'data-postid="' . (int) $pid . '"' : ''; ?> <?php echo $plink ? 'data-permalink="' . esc_url($plink) . '"' : ''; ?>><?php echo esc_html($btn); ?></button>
        </div>
        <?php endif; ?>
        <?php if ($ttl) : ?><div class="rtp-title"><?php echo esc_html($ttl); ?></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<div class="rtp-overlay" id="rtp-overlay" style="background:<?php echo esc_attr($overlay_bg); ?>">
    <div class="rtp-topbar" style="background:<?php echo esc_attr($topbar_bg); ?>">
        <div class="rtp-topbar-title" id="rtp-topbar-title"></div>
        <div class="rtp-devices">
            <?php foreach ($bps as $bp) :
						$w = isset($bp['width']) ? (int) $bp['width'] : 1280;
						$t = isset($bp['title']) ? sanitize_text_field($bp['title']) : '';
						$iconVal = isset($bp['icon']) ? $bp['icon'] : '';
						$iconUrl = '';
						if (is_array($iconVal) && ! empty($iconVal['url'])) {
							$iconUrl = esc_url($iconVal['url']);
						} elseif (is_string($iconVal) && preg_match('/^https?:\/\//', $iconVal)) {
							$iconUrl = esc_url($iconVal);
						}
					?>
            <button data-w="<?php echo (int) $w; ?>" title="<?php echo esc_attr($t); ?>">
                <?php if ($iconUrl) : ?>
                <img class="rtp-ic" src="<?php echo $iconUrl; ?>" alt="<?php echo esc_attr($t); ?>" />
                <?php else : ?>
                <?php echo esc_html(is_string($iconVal) ? $iconVal : '•'); ?>
                <?php endif; ?>
            </button>
            <?php endforeach; ?>
        </div>
        <a class="rtp-cta" href="<?php echo esc_url($cta_link ? $cta_link : '#'); ?>" target="_blank" rel="noopener"><?php echo esc_html($cta_text); ?></a>
        <button class="rtp-close" aria-label="<?php esc_attr_e('Close', 'responsive-theme-preview'); ?>">✕</button>
    </div>
    <div class="rtp-framewrap"><iframe id="rtp-frame" src="" loading="lazy"></iframe></div>
</div>
<?php
		return ob_get_clean();
	}
}