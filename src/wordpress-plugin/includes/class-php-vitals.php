<?php

class PHPVitals
{

	public function activate()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'phpvitals_history';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			run_date datetime NOT NULL,
			total_time float NOT NULL,
			base_time float NOT NULL,
			ext_time float NOT NULL,
			grade varchar(2) NOT NULL,
			php_version varchar(20) NOT NULL,
			wp_version varchar(20) NOT NULL,
			server_software varchar(255) NOT NULL,
			memory_limit varchar(20) NOT NULL,
			results longtext NOT NULL,
			PRIMARY KEY  (id)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
	}
}
