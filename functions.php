<?php
if ( ! function_exists('htmlpress_create_pages') ) {
	function htmlpress_create_pages($slugs) {
		$first_page_id = null;

		foreach ($slugs as $index => $slug) {
			$existing_page = get_page_by_path($slug);

			if (!$existing_page) {
				$page_data = array(
					'post_title'    => ucfirst($slug),
					'post_content'  => '',
					'post_status'   => 'publish',
					'post_type'     => 'page',
					'post_name'     => $slug
				);

				$page_id = wp_insert_post($page_data);

				if ($index === 0) {
					$first_page_id = $page_id;
				}
			} else {
				if ($index === 0) {
					$first_page_id = $existing_page->ID;
				}
			}
		}

		// Set the first page as the homepage
		if ($first_page_id) {
			update_option('show_on_front', 'page');
			update_option('page_on_front', $first_page_id);
		}
	}
}


if ( ! function_exists('htmlpress_theme_activation') ) {
	function htmlpress_theme_activation() {
		$default_pages = array();
		htmlpress_create_pages($default_pages);

		// Create default template files
		$theme_dir = get_template_directory();
		foreach ($default_pages as $slug) {
			$template_file = $theme_dir . "/pages/{$slug}.php";
			if (!file_exists($template_file)) {
				$default_content = "<?php\n";
				file_put_contents($template_file, $default_content);
			}
		}
	}
}

add_action('after_switch_theme', 'htmlpress_theme_activation');


if ( ! function_exists('htmlpress_custom_page_template') ) {
	function htmlpress_custom_page_template($template) {
		if (is_page()) {
			$page_slug = get_page_uri();
			$custom_template = locate_template("pages/{$page_slug}.php");

			if (!empty($custom_template)) {
				return $custom_template;
			}
		}
		return $template;
	}
}

add_filter('page_template', 'htmlpress_custom_page_template');


if ( ! function_exists('htmlpress_enqueue_assets') ) {
	function htmlpress_enqueue_assets() {
		wp_enqueue_script(
			'htmlpress-contact-form',
			get_template_directory_uri() . '/assets/internal/contact-form.js',
			array(),
			'1.0',
			true
		);

		wp_localize_script('htmlpress-contact-form', 'htmlpressAjax', array(
			'ajaxurl' => admin_url('admin-ajax.php')
		));
	}
}

add_action('wp_enqueue_scripts', 'htmlpress_enqueue_assets' );


if ( ! function_exists('htmlpress_handle_contact_form') ) {
	function htmlpress_handle_contact_form() {
		if ( !isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'htmlpress_contact_form') ) {
			wp_send_json_error('Invalid nonce');
		}

		$name = sanitize_text_field($_POST['name']);
		$email = sanitize_email($_POST['email']);
		$message = sanitize_textarea_field($_POST['message']);

		if ( empty($name) || empty($email) || empty($message) ) {
			wp_send_json_error('Please fill in all fields');
		}

		if ( ! is_email($email) ) {
			wp_send_json_error('Please enter a valid email address');
		}

		$to = get_option('admin_email');
		$subject = 'New Contact Form Submission';
		$body = "Name: $name\n\nEmail: $email\n\nMessage:\n$message";
		$headers = array('Content-Type: text/plain; charset=UTF-8');
		$sent = wp_mail( $to, $subject, $body, $headers );

		if ( $sent ) {
			wp_send_json_success('Message sent successfully');
		} else {
			wp_send_json_error('Failed to send message');
		}
	}
}

add_action('wp_ajax_htmlpress_handle_contact_form', 'htmlpress_handle_contact_form');
add_action('wp_ajax_nopriv_htmlpress_handle_contact_form', 'htmlpress_handle_contact_form');


if ( ! function_exists('htmlpress_get_partial') ) {
	function htmlpress_get_partial($name) {
		$template = "partials/{$name}.php";
		$locate = locate_template($template, false, false);
		if (!empty($locate)) {
			load_template($locate, false);
			return true;
		}
		return false;
	}
}

if ( ! function_exists('htmlpress_get_header') ) {
	function htmlpress_get_header($name) {
		$template = "partials/header-{$name}.php";
		htmlpress_get_partial($template);
	}
}

if ( ! function_exists('htmlpress_get_footer') ) {
	function htmlpress_get_footer($name) {
		$template = "partials/footer-{$name}.php";
		htmlpress_get_partial($template);
	}
}