<?php

class PHPVitals_Admin
{

	public function __construct()
	{
		add_action('admin_menu', [$this,'add_plugin_admin_menu']);
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_filter('plugin_action_links_php-vitals', [$this, 'add_action_links']);
	}

	public function enqueue_styles()
	{
		$screen = get_current_screen();
		if ($screen->id === 'toplevel_page_php-vitals') {
			wp_enqueue_style('php-vitals', plugin_dir_url(__FILE__) .
				'css/php-vitals-admin.css', [], PHPVITALS_VERSION, 'all');
		}
	}

	public function enqueue_scripts()
	{
		$screen = get_current_screen();
		if ($screen->id === 'toplevel_page_php-vitals') {
			wp_enqueue_script('jquery');
			wp_enqueue_script('php-vitals', plugin_dir_url(__FILE__) .
				'js/php-vitals-admin.js', ['jquery'], PHPVITALS_VERSION, false);
			wp_localize_script('php-vitals', 'phpvitals', [
				'nonce' => wp_create_nonce('phpvitals_nonce'),
				'ajaxurl' => admin_url('admin-ajax.php'),
			]);
		}
	}

	public function add_plugin_admin_menu()
	{
		add_menu_page(
			'PHP Vitals',
			'PHP Vitals',
			'manage_options',
			'php-vitals',
			[$this, 'display_plugin_admin_page'],
			'dashicons-performance',
			100
		);
	}

	public function add_action_links($links)
	{
		$settings_link = [
			'<a href="' . admin_url('admin.php?page=php-vitals') .
				'">' . 'PHP Vitals' . '</a>',
		];

		return array_merge($settings_link, $links);
	}

	public function display_plugin_admin_page()
	{
		include_once 'partials/php-vitals-admin-display.php';
	}
}
