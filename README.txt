=== Responsive Theme Preview ===
Contributors: Kamrul Hasan
Tags: preview, responsive, theme, elementor, gutenberg, bricks
Requires at least: 5.2
Tested up to: 6.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Preview templates in responsive frames (popup or separate page). Works with Elementor, Gutenberg, Bricks, and shortcode. Dynamic CPT source, view tracking, and customizable breakpoints.

== Description ==

Responsive Theme Preview is a powerful WordPress plugin that allows you to showcase your website designs in various device frames. Perfect for theme developers, web designers, and agencies who need to demonstrate responsive designs.

== Features ==

* Multiple Integration Options:
  * Elementor widget support
  * Gutenberg block support
  * Bricks element support
  * WordPress shortcode support

* Preview Modes:
  * Popup overlay with customizable breakpoints
  * Separate page preview option
  * Live URL opening

* Customization Options:
  * Customizable device breakpoints (Desktop, Tablet, Mobile)
  * Adjustable topbar height and styling
  * Custom button styles and colors
  * CTA button configuration
  * Overlay settings (click outside, ESC key)

* Advanced Features:
  * Dynamic CPT (Custom Post Type) integration
  * Category filtering for organized previews
  * View tracking for analytics
  * Loading indicators
  * Keyboard navigation support
  * Accessibility features (ARIA labels, focus outlines)

* Performance Optimizations:
  * Lazy loading options
  * Preview caching
  * Optimized JavaScript

== Installation ==

1. Upload the plugin files to your WordPress installation's `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings through 'Responsive Theme Preview' menu in WordPress admin

== Usage ==

=== Elementor ===

1. Add a new page or edit an existing one with Elementor
2. Drag the 'Responsive Preview' widget from the element panel
3. Configure your preview settings in the widget panel
4. Set your preview items (images, URLs, titles)
5. Choose between static items or dynamic CPT source

=== Gutenberg ===

1. Add a new block or edit an existing page
2. Search for 'Responsive Preview' block
3. Insert the block and configure settings in the block panel
4. Add your preview items or use dynamic CPT source

=== Bricks ===

1. Add a new page or edit an existing one with Bricks
2. Add the 'Responsive Preview' element
3. Configure your preview settings in the element panel
4. Set your preview items or use dynamic CPT source

=== Shortcode ===

Basic usage:
`[responsive_preview columns="3" preview_btn_pos="pos-br"]`

With dynamic CPT source:
`[responsive_preview source="dynamic" dynamic_count="6" category_filter="web-design"]`

Available parameters:
* `columns` - Number of columns (1-4, default: 3)
* `source` - Source type: 'static' or 'dynamic' (default: 'static')
* `dynamic_count` - Number of items to show for dynamic source (default: 6)
* `preview_btn_pos` - Button position: 'pos-br', 'pos-bl', 'pos-tr', 'pos-tl', 'pos-center' (default: 'pos-br')
* `category_filter` - Filter by category slug (for dynamic source)
* `enable_category_filter` - Enable frontend category filtering (true/false)
* `preview_type` - Preview type: 'popup' or 'page' (default: 'popup')
* `cta_text` - Custom CTA button text (default: 'Open Live')
* `cta_link` - Custom CTA button URL

== Custom Post Type ==

The plugin creates a 'Preview' custom post type where you can:
* Add preview images
* Set template URLs
* Organize with categories
* Track view counts

== Configuration ==

All plugin settings can be configured through:
**WordPress Admin → Responsive Theme Preview**

Configuration sections:
* General - Preview settings and default breakpoints
* Appearance - Device buttons, topbar, and overlay styling
* Performance - Loading, caching, and optimization settings
* Accessibility - Screen reader and keyboard navigation options
* Filtering - Category filtering and display options
* Advanced - Developer options and custom code

== Security ==

This plugin follows WordPress security best practices:
* All output is properly escaped using WordPress functions
* Sanitization of all user input
* CSRF protection with nonces
* XSS prevention
* SQL injection prevention

For detailed security information, see SECURITY_FIXES_README.txt

== Developer Documentation ==

For developers who want to extend or customize the plugin:

* Hooks and filters are available throughout the codebase
* Custom CSS and JavaScript can be added through settings
* Modular structure allows for easy customization
* Follows WordPress coding standards

== Screenshots ==

1. Admin settings dashboard
2. Elementor widget configuration
3. Gutenberg block settings
4. Bricks element panel
5. Frontend preview grid
6. Popup preview with device options

== Changelog ==

= 1.0.0 =
* Initial release to wp.org


== Frequently Asked Questions ==

Q: Can I use this with page builders?
A: Yes! The plugin supports Elementor, Gutenberg (blocks), and Bricks.

Q: How do I add custom breakpoints?
A: Go to Responsive Theme Preview → Settings → General to configure custom device breakpoints.

Q: Is the plugin translation ready?
A: Yes, all strings are internationalized and ready for translation.

Q: Can I track preview views?
A: Yes, the plugin includes built-in view tracking for analytics.

Q: Does this work with multisite?
A: Yes, the plugin is compatible with WordPress multisite installations.

== Support ==

* For support: https://wordpress.org/support/plugin/responsive-theme-preview

* Development: https://github.com/yourusername/responsive-theme-preview

== License ==

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA