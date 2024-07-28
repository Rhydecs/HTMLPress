# HTMLPress

HTMLPress is a simple WordPress theme designed to convert static HTML sites into functional WordPress themes with minimal effort. It provides a streamlined structure for organizing your site's content and templates.

## Features

- Easy conversion of static HTML to WordPress
- Modular structure with partials for headers, footers, and other components
- Support for multiple header and footer styles
- Built-in contact form functionality
- Automatic page creation on theme activation

## Installation

1. Download the HTMLPress theme.
2. Upload it to your WordPress site's `wp-content/themes/` directory.
3. Activate the theme through the WordPress admin panel.

## Usage

### Converting Static HTML

1. Place your static HTML files in the `pages/` directory, naming them according to the desired slug (e.g., `about.php` for an About page).
2. The theme will automatically use these files as templates for the corresponding pages.

### Creating Pages

The theme automatically creates basic pages (Home, About, Contact) upon activation. To create additional pages:

1. Go to the WordPress admin panel.
2. Navigate to Pages > Add New.
3. Create your page content.
4. Set the page slug to match your desired template file name in the `pages/` directory.

### Using Custom Headers and Footers

To use a custom header or footer:

1. Create your header/footer file in the `partials/` directory (e.g., `header-custom.php`).
2. In your page template, use:
   ```php
   <?php 
   htmlpress_get_header('custom');
   // Your page content here
   htmlpress_get_footer('custom');
   ?>