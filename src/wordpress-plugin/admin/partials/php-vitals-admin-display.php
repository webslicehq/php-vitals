<?php
if (!defined('WPINC')) {
	exit;
}

?>

<div class="php-vitals-admin">
    <div class="wrap">
        <h1 class="title">PHP Vitals - Performance Test</h1>
            <div class="benchmark-layout">
                <div class="main-content">
                    <div class="system-info">
                        <div class="system-info-layout">
                            <div class="timer-score">
                                <div class="benchmark-controls">
                                    <button id="runBenchmark" class="button button-primary button-large">
                                        Run Benchmark
                                    </button>
                                </div>
                                <div class="timer-container">
                                    <div id="loading" class="loading" style="display: none;">
                                        <div class="spinner"></div>
                                        Running performance tests...
                                    </div>
                                </div>
                            </div>
                            <div class="info-row server-info">
                                <div class="info-col">
                                    PHP Version: <strong><?php echo esc_html(PHP_VERSION); ?></strong><br>
                                    PHP Memory: <strong><?php echo esc_html(ini_get('memory_limit')); ?></strong>
                                </div>
                                <div class="info-col">
                                    WordPress Version: <strong><?php echo esc_html( get_bloginfo('version') ); ?></strong><br>
                                    Date: <strong><?php echo esc_html( gmdate('Y-m-d H:i:s')); ?></strong>
                                </div>
                                <div class="info-col">
                                    Server Software: <strong><?php echo isset($_SERVER['SERVER_SOFTWARE']) ?
										esc_html(sanitize_text_field(wp_unslash($_SERVER['SERVER_SOFTWARE']))) : 'N/A'; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="grade-container">
                            <div id="gradeDisplay" class="grade-display">--</div>
                            <div id="gradeDescription" class="grade-description">--</div>
                        </div>
                        <div class="brand-logo">
                            <svg class="asterisk" width="769" height="768" viewBox="0 0 769 768" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M709.136 230.105L384.375 381.543M384.375 381.543L59.6153 532.982M384.375 381.543L721.099 504.101M384.375 381.543L47.6523 258.986M384.375 381.543L535.814 706.304M384.375 381.543L232.937 56.7832M384.375 381.543L261.818 718.267M384.375 381.543L506.933 44.8203" stroke="#CED6DE" stroke-opacity="0.2" stroke-width="76.7857" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                </div>

                    <div id="results">
                        <table class="test-results-table" id="testResultsTable">
                            <thead>
                                <tr>
                                    <th>Test Name</th>
                                    <th>Time</th>
                                    <th>Op/ms</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="sidebar">
         				 <?php include_once 'php-vitals-terms.php'; ?>
                    <div id="benchmarkHistory" class="benchmark-history">
                        <h2>Benchmark History</h2>
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <!-- <th>Base Time</th> -->
									<th>Total Time</th>
									<th>Grade</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
								<?php
								global $wpdb;
								$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
									$wpdb->prepare(
										"SELECT * FROM %i ORDER BY run_date DESC LIMIT 20", "{$wpdb->prefix}phpvitals_history"
											),
        								ARRAY_A
        							);

					        	foreach ($results as $result) {
        							echo '<tr>';
        							echo '<td>' . esc_html( substr($result['run_date'], 0, 10) ) . '</td>';
        							// echo '<td>' . esc_html( $result['base_time'] ) . '</td>';
        							echo '<td>' . esc_html( $result['total_time'] ) . '</td>';
        							echo '<td class="grade-history" >' . esc_html( $result['grade'] ) . '</td>';
        							echo '</tr>';
        						}
        						?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
</div>
