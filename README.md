=== Responsive Theme Preview ===
Contributors: Kamrul Hasan
Tags: preview, responsive, theme, elementor, gutenberg, bricks
Requires at least: 5.2
Tested up to: 6.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin for previewing templates in responsive frames (popup or separate page). Works with Elementor, Gutenberg, Bricks, and shortcode.

## Features

- **Multiple Integration Methods**: Works with Elementor, Gutenberg (Block Editor), Bricks, and shortcode
- **Dynamic Content Source**: Can pull from custom post type (Previews) or use static content
- **Customizable Breakpoints**: Configure default device breakpoints or set custom ones per preview
- **Device Switching**: Switch between desktop, tablet, and mobile views
- **Performance Optimizations**: Lazy loading, caching, and preloading options
- **Accessibility Features**: Keyboard navigation, screen reader support, focus outlines
- **View Tracking**: Track preview views for analytics
- **Topbar Height Control**: Adjustable topbar height setting (40-100px)

## Shortcode Usage

### Basic Usage

```php
echo do_shortcode('[responsive_preview]');
```

### Advanced Usage with Custom Content

```php
echo do_shortcode('[responsive_preview items="image1.jpg|Preview 1|https://example1.com;image2.jpg|Preview 2|https://example2.com"]');
```

### Shortcode Attributes

| Attribute                    | Type    | Default   | Description                                                                   |
| ---------------------------- | ------- | --------- | ----------------------------------------------------------------------------- | ------- | --------------- | ------- | ----- |
| `columns`                    | number  | 3         | Number of columns in the grid (1-4)                                           |
| `source`                     | string  | static    | Content source: 'static' or 'dynamic'                                         |
| `count`                      | number  | 6         | Number of items to show (dynamic source only)                                 |
| `items`                      | string  |           | Static items in format: "image1.jpg                                           | Title 1 | URL1;image2.jpg | Title 2 | URL2" |
| `breakpoints`                | string  |           | Custom breakpoints in JSON format                                             |
| `cta_text`                   | string  | Open Live | CTA button text                                                               |
| `cta_link`                   | string  |           | CTA button link                                                               |
| `preview_btn_pos`            | string  | pos-br    | Preview button position: 'pos-br', 'pos-bl', 'pos-tr', 'pos-tl', 'pos-center' |
| `preview_type`               | string  | popup     | Preview type: 'popup' or 'page'                                               |
| `topbar_height`              | number  | 52        | Topbar height in pixels (40-100)                                              |
| `device_button_style`        | string  | default   | Device button style: 'default', 'rounded', 'square'                           |
| `device_button_size`         | string  | medium    | Device button size: 'small', 'medium', 'large'                                |
| `device_button_active_color` | string  | #2563eb   | Active button color                                                           |
| `device_button_hover_color`  | string  | #1d4ed8   | Hover button color                                                            |
| `overlay_close_on_click`     | boolean | true      | Close overlay when clicking outside                                           |
| `overlay_close_on_esc`       | boolean | true      | Close overlay with ESC key                                                    |
| `overlay_loading_indicator`  | boolean | true      | Show loading indicator                                                        |
| `overlay_loading_color`      | string  | #2563eb   | Loading indicator color                                                       |
| `enable_keyboard_nav`        | boolean | true      | Enable keyboard navigation                                                    |
| `focus_outline`              | boolean | true      | Show focus outline                                                            |
| `focus_outline_color`        | string  | #2563eb   | Focus outline color                                                           |
| `focus_outline_width`        | number  | 2         | Focus outline width in pixels                                                 |

### Breakpoints Format

The breakpoints should be provided as a JSON-encoded string or array:

```php
// JSON string format
echo do_shortcode('[responsive_preview breakpoints=\'[{"title":"Desktop","width":1280,"icon":"desktop-icon"},{"title":"Tablet","width":768,"icon":"tablet-icon"},{"title":"Mobile","width":375,"icon":"mobile-icon"}]\']');

// PHP array format
$breakpoints = array(
    array('title' => 'Desktop', 'width' => 1280, 'icon' => 'desktop-icon'),
    array('title' => 'Tablet', 'width' => 768, 'icon' => 'tablet-icon'),
    array('title' => 'Mobile', 'width' => 375, 'icon' => 'mobile-icon')
);
echo do_shortcode(array('responsive_preview' => array('breakpoints' => json_encode($breakpoints))));
```

### Icon Options

Icons can be provided in multiple formats:

1. **Image URLs**: Direct URLs to icon images
2. **Icon Classes**: CSS class names (e.g., 'ti-desktop', 'ti-tablet', 'ti-mobile')
3. **Base64 SVG**: Inline SVG data
4. **Font Icons**: Icon font class names

## Page Builder Integration

### Elementor

- **Widget**: Responsive Preview
- **Controls**: Full integration with Elementor's visual controls
- **Dynamic Content**: Pull from Preview CPT or use static repeater
- **Styling**: Full control over all visual aspects through Elementor panels

### Bricks

- **Element**: Responsive Preview
- **Controls**: Complete Bricks integration with custom controls
- **Dynamic Content**: Pull from Preview CPT or use static repeater
- **Styling**: Full CSS control through Bricks interface

### Gutenberg (Block Editor)

- **Block**: Responsive Preview
- **Attributes**: All shortcode attributes available as block attributes
- **Dynamic Content**: Pull from Preview CPT or use static content
- **Styling**: Basic block editor with additional style controls

## Settings

### Admin Settings Page

Located at **Settings → Responsive Theme Preview**

#### General Settings

- **Preview Settings**:
  - Start With Device: Choose default preview device (desktop, tablet, mobile)
  - Default Zoom Level: Set initial zoom level (0.5-2.0)
  - Allow Zoom Controls: Enable/disable zoom controls
- **Default Breakpoints**: Configure device breakpoints with icons

#### Appearance Settings

- **Device Button Settings**:

  - Button Style: Default, rounded, or square
  - Button Size: Small, medium, or large
  - Active Button Color: Color for active device button
  - Hover Button Color: Color for hover state

- **Topbar Settings**:

  - Topbar Height: Adjust topbar height in pixels (40-100px)

- **Overlay Settings**:
  - Close on Click Outside: Enable/disable closing overlay by clicking outside
  - Close on ESC Key: Enable/disable closing overlay with ESC key
  - Show Loading Indicator: Show/hide loading animation
  - Loading Indicator Color: Customize loading spinner color

#### Performance Settings

- **Lazy Load Previews**: Load preview content only when needed
- **Preload Previews**: Load preview content for faster access
- **Cache Previews**: Cache preview content to improve performance
- **Cache Duration**: Set cache expiration time in seconds

#### Accessibility Settings

- **Enable Keyboard Navigation**: Allow keyboard navigation through preview controls
- **Enable Screen Reader Support**: Add ARIA labels for screen readers
- **Show Focus Outline**: Display focus outline on interactive elements
- **Focus Outline Color**: Customize focus outline color
- **Focus Outline Width**: Set focus outline width in pixels

#### Developer Settings

- **Debug Mode**: Enable debug mode for development
- **Log Events**: Log plugin events to browser console

#### Custom Code

- **Custom CSS**: Add custom CSS to override default styles
- **Custom JavaScript**: Add custom JavaScript for additional functionality

#### Reset Settings

- **Reset All Settings**: Reset all settings to default values

## JavaScript Events

The plugin triggers several custom JavaScript events that developers can hook into:

### rtp:beforeOpen

Fires before the preview popup opens.

```javascript
document.addEventListener("rtp:beforeOpen", function (e) {
  console.log("Preview about to open:", e.detail);
  // Custom logic before popup opens
});
```

### rtp:afterOpen

Fires after the preview popup opens.

```javascript
document.addEventListener("rtp:afterOpen", function (e) {
  console.log("Preview opened:", e.detail);
  // Custom logic after popup opens
});
```

### rtp:beforeClose

Fires before the preview popup closes.

```javascript
document.addEventListener("rtp:beforeClose", function (e) {
  console.log("Preview about to close:", e.detail);
  // Custom logic before popup closes
});
```

### rtp:afterClose

Fires after the preview popup closes.

```javascript
document.addEventListener("rtp:afterClose", function (e) {
  console.log("Preview closed:", e.detail);
  // Custom logic after popup closes
});
```

### rtp:deviceChange

Fires when the device view changes.

```javascript
document.addEventListener("rtp:deviceChange", function (e) {
  console.log("Device changed to:", e.detail.device, e.detail.width);
  // Custom logic for device changes
});
```

## CSS Classes

The plugin uses several CSS classes that can be targeted for custom styling:

### Main Container

- `.rtp-grid`: Main grid container
- `.rtp-grid.cols-1`, `.rtp-grid.cols-2`, `.rtp-grid.cols-3`, `.rtp-grid.cols-4`: Column-specific classes

### Preview Card

- `.rtp-card`: Individual preview card container
- `.rtp-thumb`: Thumbnail container
- `.rtp-title`: Preview title
- `.rtp-open`: Preview button

### Popup Overlay

- `.rtp-overlay`: Full-screen overlay
- `.rtp-overlay.show`: Active overlay state
- `.rtp-topbar`: Top bar with device buttons
- `.rtp-topbar-title`: Preview title in topbar
- `.rtp-devices`: Device button container
- `.rtp-framewrap`: Frame wrapper
- `#rtp-frame`: Preview iframe

### Device Buttons

- `.rtp-devices button`: Individual device button
- `.rtp-devices button.active`: Active device button

### Loading Indicator

- `.rtp-loading`: Loading spinner animation

### CTA Button

- `.rtp-cta`: Call-to-action button

### Close Button

- `.rtp-close`: Close button

### States

- `.rtp-popup-open`: Added to body when popup is open (prevents background scrolling)

## Customization

### Topbar Height Control

The topbar height can be controlled through:

1. **Admin Settings**: Appearance → Topbar Settings → Topbar Height
2. **Page Builder Controls**: Each page builder provides a topbar height control
3. **Shortcode Attribute**: `topbar_height="60"`

The height affects:

- Top bar height: `.rtp-topbar { height: 60px; }`
- Frame positioning: `.rtp-framewrap { top: 60px; }`
- Frame height: `#rtp-frame { height: calc(100vh - 60px); }`

### Body Scroll Prevention

When the popup is open, the plugin adds `.rtp-popup-open` class to the body element:

```css
.rtp-popup-open {
  overflow: hidden;
}
```

This prevents scrolling on the main page behind the popup while it's active.

## Troubleshooting

### Common Issues

1. **Icons Not Showing**: Ensure icon URLs are accessible and valid
2. **Preview Not Loading**: Check browser console for JavaScript errors
3. **CSS Conflicts**: Use browser developer tools to identify style conflicts
4. **Shortcode Not Working**: Verify all attributes are properly formatted

### Debug Mode

Enable debug mode in admin settings to see detailed console logging and error messages.

## Compatibility

- **WordPress Version**: 6.0+
- **PHP Version**: 7.4+
- **Tested Page Builders**: Elementor 3.31+, Bricks latest, Gutenberg
- **Browsers**: Chrome, Firefox, Safari, Edge

## Changelog

### Version 3.11.1

- Added topbar height control to all page builder integrations
- Removed Themify Icons dependency
- Fixed iframe height calculation in single preview template
- Added body scroll prevention when popup is open
- Improved performance optimizations
- Enhanced accessibility features

### Previous Versions

For detailed changelog information, please refer to the plugin documentation.

## Support

For support, feature requests, or bug reports, please contact:

- **Email**: kamrulhasanshuvo04@gmail.com
- **WordPress.org**: [Plugin Support Forum](https://wordpress.org/support/plugin/responsive-theme-preview)

## License

GPL v2 or later

---

_This README file provides comprehensive documentation for the Responsive Theme Preview plugin, including shortcode usage, page builder integration details, settings explanations, and customization options._
