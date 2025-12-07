# Category-Based Filtering for Responsive Theme Preview

This document explains how to use the new category-based filtering functionality in the Responsive Theme Preview plugin.

## Overview

The category filtering feature allows you to:

- Filter preview posts by specific categories in dynamic mode
- Enable frontend category filtering for users
- Display filtered previews in Bricks Builder, Elementor, and shortcodes

## Features Added

### 1. Bricks Builder Integration

- **Category Filter Control**: Select a specific category to filter previews
- **Frontend Filter Toggle**: Enable/disable category filter dropdown for users
- **Dynamic Category Loading**: Automatically loads available categories

### 2. Elementor Integration

- **Category Filter Control**: Dropdown to select category for filtering
- **Frontend Filter Switch**: Toggle to enable user-facing category filter
- **Real-time Updates**: Categories update when you add new ones

### 3. Shortcode Enhancement

- **Category Attribute**: Filter by specific category slug
- **Enable Filter Attribute**: Show/hide frontend category filter
- **Backward Compatibility**: Existing shortcodes continue to work

## Usage Examples

### Shortcode Usage

#### Basic Category Filter

```shortcode
[responsive_preview source="dynamic" category="business" count="6"]
```

#### With Frontend Filter Enabled

```shortcode
[responsive_preview source="dynamic" enable_filter="true" count="6"]
```

#### Combined Usage

```shortcode
[responsive_preview source="dynamic" category="portfolio" enable_filter="true" count="4" columns="2"]
```

#### Static Mode (No Filtering)

```shortcode
[responsive_preview source="static" items="image1.jpg|Title 1|url1|Preview;image2.jpg|Title 2|url2|Preview"]
```

### Bricks Builder Usage

1. Add a Responsive Preview element to your page
2. Set **Source** to "Dynamic (CPT: Previews)"
3. Use the **Filter by Category** dropdown to select a category
4. Toggle **Enable Frontend Category Filter** to show user filter
5. Configure other settings as needed

### Elementor Usage

1. Add the Responsive Preview widget to your page
2. Set **Source** to "Dynamic (CPT: Previews)"
3. Select a category from the **Filter by Category** dropdown
4. Enable **Enable Frontend Category Filter** for user filtering
5. Adjust other settings as desired

## Admin Settings

Navigate to **Previews → Settings** in your WordPress admin to configure global filtering options:

### Filtering Tab

- **Enable Preview Filtering**: Global toggle for filtering features
- **Filter by Category**: Enable category-based filtering
- **Show Filter Count**: Display item count when filtering

## Frontend User Experience

When frontend filtering is enabled, users will see:

- A dropdown with available categories
- Real-time filtering without page reload
- Item count showing visible results
- Responsive design for mobile devices

## Technical Implementation

### Category Data Structure

Each preview card includes category data:

```html
<div class="rtp-card" data-category="business,portfolio"></div>
```

### JavaScript Filtering

The filtering system:

- Reads category data from card attributes
- Handles multiple categories per preview
- Updates visibility based on selection
- Maintains responsive layout

### CSS Styling

Category filter styles include:

- Consistent design with plugin theme
- Responsive layout for mobile
- Focus states for accessibility
- Smooth transitions

## Testing

Use the included test file to verify functionality:

1. Access `/wp-content/plugins/responsive-theme-preview-3.11.1/test-category-filtering.php`
2. Create preview posts with categories
3. Test different shortcode combinations
4. Verify frontend filtering behavior

## Migration Guide

### Existing Shortcodes

No changes needed - existing shortcodes continue to work as before.

### New Features

To add filtering to existing implementations:

1. Add `category="slug"` attribute for backend filtering
2. Add `enable_filter="true"` for frontend filtering
3. Update Bricks/Elementor elements with new controls

## Troubleshooting

### Categories Not Showing

1. Verify categories are created in WordPress admin
2. Check that preview posts have categories assigned
3. Ensure categories are not empty (have posts assigned)

### Filter Not Working

1. Check browser console for JavaScript errors
2. Verify jQuery is loaded on the page
3. Ensure category data attributes are present on cards

### Styling Issues

1. Check CSS is properly enqueued
2. Verify no theme conflicts
3. Test with different themes

## Developer Notes

### Hook Integration

The filtering system integrates with:

- `get_terms()` for category retrieval
- `WP_Query` tax_query for filtering
- WordPress sanitization functions

### Performance

- Categories loaded once per page load
- Efficient DOM manipulation
- Minimal JavaScript overhead

### Security

- All user inputs sanitized
- Category slugs validated
- SQL injection prevention via WP_Query

## Future Enhancements

Potential improvements for future versions:

- AJAX-based category loading
- Multi-select category filters
- Search within categories
- Category hierarchy support
- Filter persistence across page loads
