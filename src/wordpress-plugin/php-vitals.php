<?php

/**
 * Plugin Name: PHP Vitals
 * Plugin URI: https://phpvitals.com
 * Description: Benchmark your PHP performance, identify potential areas for optimization and see how your hosting ranks.
 * Version: 1.2.1
 * Requires at least: 6.2
 * Requires PHP: 7.0
 * Author: Webslice
 * Author URI: https://webslice.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: php-vitals
 */

if (!defined('WPINC')) {
	exit;
}

define('PHPVITALS_VERSION', '1.2.1');
define('PHPVITALS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PHPVITALS_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once PHPVITALS_PLUGIN_DIR . 'includes/class-php-vitals.php';
require_once PHPVITALS_PLUGIN_DIR . 'admin/class-php-vitals-admin.php';
require_once PHPVITALS_PLUGIN_DIR . 'includes/class-php-vitals-ajax.php';
require_once PHPVITALS_PLUGIN_DIR . 'includes/class-php-vitals-tests.php';

register_activation_hook(__FILE__, function () {
	$plugin = new PHPVitals();
	$plugin->activate();
});

function phpvitals_init()
{
	new PHPVitals_Admin();
	new PHPVitals_Ajax();
}
phpvitals_init();
