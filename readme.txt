=== Responsive Theme Preview ===
Contributors: kamrulhasan
Tags: responsive, preview, theme, elementor, gutenberg, bricks, shortcode, mobile, tablet, desktop
Requires at least: 4.0
Tested up to: 6.8
Stable tag: 3.11.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Responsive Theme Preview allows you to preview your WordPress templates in responsive frames (popup or separate page). Works with Elementor, Gutenberg, Bricks, and shortcode.

== Description ==
Responsive Theme Preview is a powerful WordPress plugin that allows you to showcase your website designs in various device viewports. Perfect for agencies, freelancers, and anyone who wants to demonstrate responsive design capabilities.

== Features ==
* Multiple Integration Methods - Works with Elementor, Gutenberg (Block Editor), Bricks, and shortcode
* Dynamic Content Source - Pull content from custom post type (Previews) or use static content
* Customizable Breakpoints - Configure default device breakpoints or set custom ones per preview
* Device Switching - Switch between desktop, tablet, and mobile views with smooth transitions
* Performance Optimizations - Lazy loading, caching, and preloading options for faster previews
* Accessibility Features - Keyboard navigation, screen reader support, and focus outlines
* View Tracking - Track preview views for analytics and engagement metrics
* Topbar Height Control - Adjustable topbar height (40-100px) to suit your design needs
* Popup Prevention - Prevents background scrolling when popup is open

== Installation ==
1. Upload the plugin folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the WordPress admin dashboard
3. Configure your default settings under Settings → Responsive Theme Preview

== Shortcode Usage ==
Basic usage:
`[responsive_preview]`

Advanced usage with custom content:
`[responsive_preview items="image1.jpg|Preview 1|https://example1.com;image2.jpg|Preview 2|https://example2.com"]`

All available attributes:
- columns: Number of columns in grid (1-4)
- source: Content source ('static' or 'dynamic')
- count: Number of items to show (dynamic source only)
- items: Static items in format "image1.jpg|Title 1|URL1;image2.jpg|Title 2|URL2"
- breakpoints: Custom device breakpoints in JSON format
- cta_text: Call-to-action button text
- cta_link: Call-to-action button URL
- preview_btn_pos: Button position ('pos-br', 'pos-bl', 'pos-tr', 'pos-tl', 'pos-center')
- preview_type: Preview type ('popup' or 'page')
- topbar_height: Topbar height in pixels (40-100)

== Page Builder Integration ==

=== Elementor ===
* Full widget integration with all controls exposed through Elementor panels
* Visual controls for styling, content, and behavior
* Dynamic content pulling from Preview CPT
* Custom breakpoint configuration with icon support

=== Bricks ===
* Complete Bricks element integration with custom controls
* Visual interface for all settings
* Dynamic content support with repeater controls
* Icon picker with Themify icons and custom image support

=== Gutenberg (Block Editor) ===
* Native Gutenberg block with all shortcode attributes
* Visual block editor with intuitive controls
* Dynamic content support from Preview CPT
* Responsive preview within the block editor

== Frequently Asked Questions ==

Q: Can I use custom icons instead of the default ones?
A: Yes! You can upload custom icon images or use icon classes like 'ti-desktop', 'ti-tablet', 'ti-mobile'.

Q: How do I change the topbar height?
A: Go to Settings → Responsive Theme Preview → Appearance → Topbar Settings → Topbar Height. Set your preferred height in pixels (40-100).

Q: Can I track preview views?
A: Yes! Enable view tracking in Settings → Performance Settings to collect analytics data.

Q: Is this plugin translation ready?
A: Yes! The plugin includes translation files and is ready for internationalization.

== Technical Details ==
* Plugin Name: responsive-theme-preview
* Text Domain: responsive-theme-preview
* Version: 3.11.1
* Minimum PHP: 7.4
* WordPress Version: 6.0+

== Changelog ==
= 3.11.1 =
* Added topbar height control to all page builder integrations
* Removed Themify Icons dependency and switched to SVG icons
* Fixed iframe height calculation to use dynamic topbar height
* Added popup-open class to prevent background scrolling
* Improved accessibility and performance features
* Enhanced shortcode with additional attributes
* Created comprehensive documentation

= 3.11.0 =
* Initial release version

== Upgrade Notice ==
If you're upgrading from a previous version, please re-save your settings after upgrading to ensure compatibility.

== Support ==
For support, feature requests, or bug reports, please visit:
* WordPress.org plugin directory: https://wordpress.org/plugins/responsive-theme-preview/
* Support email: kamrulhasanshuvo04@gmail.com
* GitHub repository: https://github.com/yourusername/responsive-theme-preview

== License ==
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This license gives you the freedom to use the plugin for any purpose, including commercial projects, without any restrictions.