<?php
if (! defined('ABSPATH')) {
	exit;
}

class RTP_CPT {

	const POST_TYPE  = 'rtp_preview';
	const META_IMAGE = '_rtp_image';
	const META_URL   = '_rtp_url';
	const META_VIEWS = '_rtp_views';

	public static function init() {
		add_action('init', array(__CLASS__, 'register'));
		add_action('init', array(__CLASS__, 'register_taxonomy'));
		add_action('add_meta_boxes', array(__CLASS__, 'metaboxes'));
		add_action('save_post', array(__CLASS__, 'save'), 10, 2);
		add_filter('manage_' . self::POST_TYPE . '_posts_columns', array(__CLASS__, 'columns'));
		add_action('manage_' . self::POST_TYPE . '_posts_custom_column', array(__CLASS__, 'column_data'), 10, 2);
		add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_media'));
	}

	/**
	 * Register the custom post type
	 */
	public static function register() {
		register_post_type(self::POST_TYPE, array(
			'label' => __('Previews', 'responsive-theme-preview'),
			'labels' => array(
				'name' => __('Previews', 'responsive-theme-preview'),
				'singular_name' => __('Preview', 'responsive-theme-preview'),
			),
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'supports' => array('title'),
			'has_archive' => false,
			'rewrite' => array('slug' => 'preview', 'with_front' => false),
			'menu_icon' => 'dashicons-visibility',
		));
	}

	/**
	 * Register taxonomy for preview categories
	 */
	public static function register_taxonomy() {
		$labels = array(
			'name'              => _x('Preview Categories', 'taxonomy general name', 'responsive-theme-preview'),
			'singular_name'     => _x('Preview Category', 'taxonomy singular name', 'responsive-theme-preview'),
			'search_items'      => __('Search Preview Categories', 'responsive-theme-preview'),
			'all_items'         => __('All Preview Categories', 'responsive-theme-preview'),
			'parent_item'       => __('Parent Preview Category', 'responsive-theme-preview'),
			'parent_item_colon' => __('Parent Preview Category:', 'responsive-theme-preview'),
			'edit_item'         => __('Edit Preview Category', 'responsive-theme-preview'),
			'update_item'       => __('Update Preview Category', 'responsive-theme-preview'),
			'add_new_item'      => __('Add New Preview Category', 'responsive-theme-preview'),
			'new_item_name'     => __('New Preview Category Name', 'responsive-theme-preview'),
			'menu_name'         => __('Preview Categories', 'responsive-theme-preview'),
			'popular_items'     => __('Popular Preview Categories', 'responsive-theme-preview'),
			'separate_items_with_commas' => __('Separate preview categories with commas', 'responsive-theme-preview'),
			'add_or_remove_items'        => __('Add or remove preview categories', 'responsive-theme-preview'),
			'choose_from_most_used'      => __('Choose from the most used preview categories', 'responsive-theme-preview'),
			'not_found'          => __('No preview categories found.', 'responsive-theme-preview'),
			'no_terms'           => __('No preview categories', 'responsive-theme-preview'),
			'items_list_navigation' => __('Preview categories navigation', 'responsive-theme-preview'),
			'items_list'         => __('Preview categories list', 'responsive-theme-preview'),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'          => $labels,
			'show_ui'         => true,
			'show_admin_column' => true,
			'query_var'       => true,
			'rewrite'          => array('slug' => 'rtp-category'),
			'show_in_rest'    => true,
		);

		register_taxonomy('rtp-category', array(self::POST_TYPE), $args);
	}

	public static function admin_media($hook) {
		wp_enqueue_media();
	}

	public static function metaboxes() {
		add_meta_box('rtp_preview_meta', __('Preview Details', 'responsive-theme-preview'), array(__CLASS__, 'render_meta'), self::POST_TYPE, 'normal', 'default');
	}

	public static function render_meta($post) {
		wp_nonce_field('rtp_preview_meta', 'rtp_preview_nonce');
		$image = get_post_meta($post->ID, self::META_IMAGE, true);
		$url   = get_post_meta($post->ID, self::META_URL, true);
		$views = (int) get_post_meta($post->ID, self::META_VIEWS, true);
?>
<table class="form-table">
    <tr>
        <th><label for="rtp_image"><?php esc_html_e('Preview Image', 'responsive-theme-preview'); ?></label></th>
        <td>
            <input type="text" id="rtp_image" name="rtp_image" value="<?php echo esc_attr($image); ?>" class="regular-text" />
            <button type="button" class="button" id="rtp_image_btn"><?php esc_html_e('Upload', 'responsive-theme-preview'); ?></button>
        </td>
    </tr>
    <tr>
        <th><label for="rtp_url"><?php esc_html_e('Template URL', 'responsive-theme-preview'); ?></label></th>
        <td><input type="url" id="rtp_url" name="rtp_url" value="<?php echo esc_attr($url); ?>" class="regular-text" placeholder="https://" /></td>
    </tr>
    <tr>
        <th><?php esc_html_e('Views', 'responsive-theme-preview'); ?></th>
        <td><strong><?php echo esc_html((string) $views); ?></strong></td>
    </tr>
</table>
<script>
jQuery(function($) {
    $('#rtp_image_btn').on('click', function(e) {
        e.preventDefault();
        var f = wp.media({
            title: 'Select Image',
            multiple: false,
            library: {
                type: 'image'
            }
        });
        f.on('select', function() {
            var u = f.state().get('selection').first().get('url');
            $('#rtp_image').val(u).trigger('change');
        });
        f.open();
    });
});
</script>
<?php
	}

	public static function save($post_id, $post) {
		if ($post->post_type !== self::POST_TYPE) {
			return;
		}
		if (! isset($_POST['rtp_preview_nonce']) || ! wp_verify_nonce($_POST['rtp_preview_nonce'], 'rtp_preview_meta')) {
			return;
		}
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		if (! current_user_can('edit_post', $post_id)) {
			return;
		}

		if (isset($_POST['rtp_image'])) {
			update_post_meta($post_id, self::META_IMAGE, esc_url_raw(wp_unslash($_POST['rtp_image'])));
		}
		if (isset($_POST['rtp_url'])) {
			update_post_meta($post_id, self::META_URL, esc_url_raw(wp_unslash($_POST['rtp_url'])));
		}
	}

	public static function columns($c) {
		$c['rtp_image'] = __('Image', 'responsive-theme-preview');
		$c['rtp_url']   = __('URL', 'responsive-theme-preview');
		$c['rtp_views'] = __('Views', 'responsive-theme-preview');
		return $c;
	}

	public static function column_data($col, $post_id) {
		if ('rtp_image' === $col) {
			$img = get_post_meta($post_id, self::META_IMAGE, true);
			if ($img) {
				echo '<img src="' . esc_url($img) . '" style="max-width:80px;height:auto;border-radius:4px" />';
			}
		} elseif ('rtp_url' === $col) {
			$u = get_post_meta($post_id, self::META_URL, true);
			if ($u) {
				echo '<a href="' . esc_url($u) . '" target="_blank" rel="noopener">' . esc_html($u) . '</a>';
			}
		} elseif ('rtp_views' === $col) {
			echo (int) get_post_meta($post_id, self::META_VIEWS, true);
		}
	}
}

RTP_CPT::init();