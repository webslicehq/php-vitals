<?php

class PHPVitals_Ajax
{
	private float $start_time;

	public function __construct()
	{
		add_action('wp_ajax_phpvitals_run_benchmark', [$this, 'phpvitals_run_benchmark']);
		add_action('wp_ajax_phpvitals_save_results', [$this, 'phpvitals_save_results']);
		add_action('wp_ajax_phpvitals_handle_terms', [$this, 'phpvitals_handle_terms']);
	}

	public function start_timer()
	{
		[$usec, $sec] = explode(' ', microtime());
		$this->start_time = ((float) $usec + (float) $sec);
	}

	public function stop_timer()
	{
		[$usec, $sec] = explode(' ', microtime());
		$time = ((float) $usec + (float) $sec) - $this->start_time;

		return $time;
	}

	public function phpvitals_run_benchmark()
	{
		check_admin_referer('phpvitals_nonce', 'nonce');
		$test_index = isset($_POST['test_index']) ? intval($_POST['test_index']) : 0;
		$test = PHPVitals_Tests::get_test($test_index);
		$iterations = $test['iterations'];

		try {
			$this->start_timer();
			for ($i = 0; $i < $iterations; $i++) {
				try {
					$test['function']();
				} catch (Exception $e) {
					if (strpos($e->getMessage(), 'extension not available') !== false) {
						wp_send_json_success([
							'test_name' => $test['name'],
							'test_index' => $test_index,
							'category' => $test['category'],
							'time' => 0,
							'ops_per_ms' => 0,
							'iterations' => $iterations,
							'total_tests' => $test['total_tests'],
							'skipped' => true,
							'skip_reason' => $e->getMessage(),
						]);

						return;
					}

					throw $e;
				}
			}
			$time = $this->stop_timer();

			$ops_per_ms = $iterations / $time / 1000;

			$test_results = [
				'test_name' => $test['name'],
				'test_index' => $test_index,
				'category' => $test['category'],
				'time' => $time,
				'ops_per_ms' => $ops_per_ms,
				'iterations' => $iterations,
				'total_tests' => $test['total_tests'],
			];

			wp_send_json_success($test_results);
		} catch (Exception $e) {
			wp_send_json_error([
				'message' => 'Test failed: ' . $e->getMessage(),
				'test_name' => $test['name'],
			]);
		}
	}

	public function phpvitals_handle_terms()
	{
		check_admin_referer('phpvitals_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error('Insufficient permissions');

			return;
		}

		$accepted = isset($_POST['terms_accept']) ? sanitize_text_field(wp_unslash($_POST['terms_accept'])) : '';

		if ($accepted === 'on') {
			update_option('phpvitals_terms_accepted', 1);
		} else {
			update_option('phpvitals_terms_accepted', 0);
		}

		wp_send_json_success([
			'redirect' => admin_url('admin.php?page=php-vitals'),
		]);
	}

	public function phpvitals_save_results()
	{
		check_admin_referer('phpvitals_nonce', 'nonce');

		global $wpdb;
		$table_name = $wpdb->prefix . 'phpvitals_history';

		$total_time = isset($_POST['total_time']) ? floatval($_POST['total_time']) : 0;
		$base_time = isset($_POST['base_time']) ? floatval($_POST['base_time']) : 0;
		$ext_time = isset($_POST['ext_time']) ? floatval($_POST['ext_time']) : 0;
		$grade = isset($_POST['grade']) ? sanitize_text_field(wp_unslash($_POST['grade'])) : '';

		$data = [
			'run_date' => current_time('mysql'),
			'total_time' => $total_time,
			'base_time' => $base_time,
			'ext_time' => $ext_time,
			'grade' => $grade,
			'php_version' => PHP_VERSION,
			'wp_version' => get_bloginfo('version'),
			'server_software' => isset($_SERVER['SERVER_SOFTWARE']) ? sanitize_text_field(wp_unslash($_SERVER['SERVER_SOFTWARE'])) : '',
			'memory_limit' => ini_get('memory_limit'),
		];

		if (get_option('phpvitals_terms_accepted')) {
			$response = wp_remote_post(
				'https://phpvitals.com/api/benchmark',
				[
					'body' => json_encode([
						'asn' => 'WordPress Plugin',
						'php_version' => PHP_VERSION,
						'php_release' => PHP_RELEASE_VERSION,
						'final_score' => $base_time,
						'benchmark_data' => $data,
						'php_info' => PHP_VERSION,
						'cpu_cores' => 1,
						'cpu_mhz' => 0,
						'host_info' => '',
						'total_time' => $total_time,
					]),
					'headers' => [
						'Content-Type' => 'application/json',
					],
				]
			);
		}
		$format = [
			'%s',
			'%f',
			'%f',
			'%f',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
		];

		$result = $wpdb->insert($table_name, $data, $format); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		if ($result === false) {
			wp_send_json_error(['message' => 'Failed to save benchmark results: ' . $wpdb->last_error]);
		} else {
			wp_send_json_success(['message' => 'Benchmark results saved successfully']);
		}
	}
}
