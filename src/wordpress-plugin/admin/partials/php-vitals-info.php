<?php
if (!defined('WPINC')) {
	exit;
}

$hosting_info = get_option('phpvitals_hosting_info');
$has_hosting_info = !empty($hosting_info);
$hosting_data = $has_hosting_info ? json_decode($hosting_info, true) : null;

$terms_accepted = get_option('phpvitals_terms_accepted');

function get_phpvitals_options() {
	$cached_options = get_transient('phpvitals_api_options');

	if ($cached_options !== false) {
		return $cached_options;
	}

	$response = wp_remote_get('https://phpvitals.com/api/options', [
		'timeout' => 15,
		'headers' => [
			'Content-Type' => 'application/json',
		],
	]);

	if (is_wp_error($response)) {
		return [
			'error' => $response->get_error_message()
		];
	}

	$body = wp_remote_retrieve_body($response);
	$response_data = json_decode($body, true);

	if (json_last_error() !== JSON_ERROR_NONE || !is_array($response_data) || !isset($response_data['data'])) {
		return [
			'error' => 'Invalid API response'
		];
	}

	$options = $response_data['data'];

	set_transient('phpvitals_api_options', $options, HOUR_IN_SECONDS);

	return $options;
}

function get_hosting_type_label($value) {
	$options = get_phpvitals_options();

	if (isset($options['server_types']) && isset($options['server_types'][$value])) {
		return $options['server_types'][$value];
	}

	$fallback_labels = [
		'1' => 'Shared Hosting',
		'2' => 'VPS/Dedicated',
		'3' => 'Self-hosted',
		'99' => 'Other'
	];

	return isset($fallback_labels[$value]) ? $fallback_labels[$value] : $value;
}

function get_currency_info() {
	$locale = get_locale();
	$currency = 'usd';
	$currency_symbol = "$";

	return [
		'currency' => $currency,
		'currency_symbol' => $currency_symbol
	];
}

function get_hosting_cost_label($value) {
	$options = get_phpvitals_options();
	$currency_info = get_currency_info();
	$currency = $currency_info['currency'];
	$currency_symbol = $currency_info['currency_symbol'];

	if (isset($options[$currency]) && is_array($options[$currency])) {
		$tiers = $options[$currency];

		switch ($value) {
			case '1':
				return $currency_symbol . ' (Less than ' . $currency_symbol . $tiers[0] . '/month)';
			case '2':
				return str_repeat($currency_symbol, 2) . ' (' . $currency_symbol . $tiers[0] . ' to ' . $currency_symbol . $tiers[1] . '/month)';
			case '3':
				return str_repeat($currency_symbol, 3) . ' (' . $currency_symbol . $tiers[1] . ' to ' . $currency_symbol . $tiers[2] . '/month)';
			case '4':
				return str_repeat($currency_symbol, 4) . ' (More than ' . $currency_symbol . $tiers[2] . '/month)';
		}
	}

	$fallback_labels = [
		'1' => '$ (Less than $10/month)',
		'2' => '$$ ($10 to $20/month)',
		'3' => '$$$ ($20 to $50/month)',
		'4' => '$$$$ (More than $50/month)'
	];

	return isset($fallback_labels[$value]) ? $fallback_labels[$value] : $value;
}


?>

<div class="php-vitals-info">
    <div class="info-container">
        <h2>Compare your results!</h2>
        <p>To be able to compare your server's performance with other similar servers, we would like to ask you a few questions.</p>



        <form id="unified-form" class="unified-form" <?php echo ($has_hosting_info && $terms_accepted) ? 'style="display: none;"' : ''; ?>>

            <div class="form-section" id="hosting-info-section" <?php echo ($has_hosting_info) ? 'style="display: none;"' : ''; ?>>
                <h3>Hosting Information</h3>
                <p>Help us understand your hosting setup for better performance comparisons.</p>

                <div class="server-hosting-section">
                    <div class="hosting-type-group">
                        <label class="section-label">What type of hosting do you use?</label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="hosting_type" value="1" id="hosting-shared" <?php echo ($has_hosting_info && $hosting_data['hosting_type'] === '1') ? 'checked' : ''; ?>>
                                <span class="radio-custom"></span>
                                <span class="radio-label"><?php echo esc_html(get_hosting_type_label('1')); ?></span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="hosting_type" value="2" id="hosting-vps" <?php echo ($has_hosting_info && $hosting_data['hosting_type'] === '2') ? 'checked' : ''; ?>>
                                <span class="radio-custom"></span>
                                <span class="radio-label"><?php echo esc_html(get_hosting_type_label('2')); ?></span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="hosting_type" value="3" id="hosting-other" <?php echo ($has_hosting_info && $hosting_data['hosting_type'] === '3') ? 'checked' : ''; ?>>
                                <span class="radio-custom"></span>
                                <span class="radio-label"><?php echo esc_html(get_hosting_type_label('3')); ?></span>
                            </label>
                        </div>
                    </div>

                    <div class="hosting-cost-group">
                        <label class="section-label">What is your monthly hosting cost? (in <?php echo esc_html(strtoupper(get_currency_info()['currency'])); ?>)</label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="hosting_cost" value="1" id="cost-less-10" <?php echo ($has_hosting_info && $hosting_data['hosting_cost'] === '1') ? 'checked' : ''; ?>>
                                <span class="radio-custom"></span>
                                <span class="radio-label"><?php echo esc_html(get_hosting_cost_label('1')); ?></span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="hosting_cost" value="2" id="cost-10-20" <?php echo ($has_hosting_info && $hosting_data['hosting_cost'] === '2') ? 'checked' : ''; ?>>
                                <span class="radio-custom"></span>
                                <span class="radio-label"><?php echo esc_html(get_hosting_cost_label('2')); ?></span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="hosting_cost" value="3" id="cost-20-50" <?php echo ($has_hosting_info && $hosting_data['hosting_cost'] === '3') ? 'checked' : ''; ?>>
                                <span class="radio-custom"></span>
                                <span class="radio-label"><?php echo esc_html(get_hosting_cost_label('3')); ?></span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="hosting_cost" value="4" id="cost-more-50" <?php echo ($has_hosting_info && $hosting_data['hosting_cost'] === '4') ? 'checked' : ''; ?>>
                                <span class="radio-custom"></span>
                                <span class="radio-label"><?php echo esc_html(get_hosting_cost_label('4')); ?></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Terms and Privacy</h3>
                <div class="terms-content">
                    <?php if ($terms_accepted): ?>
                        <p>You have already accepted the Terms of Use and Privacy Policy.</p>
                        <p>If you would like to disable this feature, please uncheck the following checkbox:</p>
                    <?php else: ?>
                        <p>This plugin connects to <a href="https://phpvitals.com" target="_blank">PHP Vitals</a> to compare the user's benchmark test results (This is an optional service).</p>
                        <p>If the "Compare my Results" option is active, each time the user runs the Benchmark Test their results will be shared with PHP Vitals.</p>
                        <p>If you would like to share and compare your results, please read over and check the <a href="https://phpvitals.com/privacy-policy" target="_blank">Privacy Policy</a> and <a href="https://phpvitals.com/terms" target="_blank">Terms of Use</a>.</p>
                    <?php endif; ?>
                </div>

                <div class="terms-checkbox">
                    <label>
                        <input type="checkbox" id="terms-accept" name="terms_accept" <?php echo $terms_accepted ? 'checked' : ''; ?>>
                        I have read and agree to the Terms of Use and Privacy Policy.
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary button-large" id="submit-unified-form">
                    <?php echo ($has_hosting_info && $terms_accepted) ? 'Update Information' : 'Save Information'; ?>
                </button>
                <?php if ($has_hosting_info && $terms_accepted): ?>
                    <button type="button" class="button button-secondary" id="cancel-edit">Cancel</button>
                <?php endif; ?>
            </div>
        </form>

        <?php if ($has_hosting_info && $terms_accepted): ?>
            <div class="current-status-display" id="current-status-display">
                <h3>Current Status</h3>
                <div class="status-summary">
                    <div class="status-item">
                        <strong>Hosting Info:</strong> ✅ Configured
                    </div>
                    <div class="status-item">
                        <strong>Terms Accepted:</strong> ✅ Accepted
                    </div>
                </div>
            </div>
        <div class="server-info-section">
            <h3>Server Information</h3>
            <div class="server-info-table">
                <table class="info-table">
                    <tbody>
                        <tr>
                            <td class="info-label">PHP Version:</td>
                            <td class="info-value"><?php echo esc_html(PHP_VERSION); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">PHP Memory Limit:</td>
                            <td class="info-value"><?php echo esc_html(ini_get('memory_limit')); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">WordPress Version:</td>
                            <td class="info-value"><?php echo esc_html(get_bloginfo('version')); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Server Software:</td>
                            <td class="info-value"><?php echo isset($_SERVER['SERVER_SOFTWARE']) ? esc_html(sanitize_text_field(wp_unslash($_SERVER['SERVER_SOFTWARE']))) : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Server Date:</td>
                            <td class="info-value"><?php echo esc_html(gmdate('Y-m-d H:i:s')); ?></td>
                        </tr>
                        <?php if ($has_hosting_info): ?>
                        <tr>
                            <td class="info-label">Hosting Type:</td>
                            <td class="info-value"><?php echo esc_html(get_hosting_type_label($hosting_data['hosting_type'])); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Monthly Cost (in <?php echo esc_html(strtoupper(get_currency_info()['currency'])); ?>):</td>
                            <td class="info-value"><?php echo esc_html(get_hosting_cost_label($hosting_data['hosting_cost'])); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($has_hosting_info): ?>
            <div class="server-info-actions">
                <button type="button" class="button button-secondary" id="edit-hosting-info">Edit Hosting Info</button>
            </div>
            <?php endif; ?>
        </div>
		<?php endif; ?>

        <div id="inline-edit-form" class="inline-edit-form" style="display: none;">
            <h3>Edit Hosting Information</h3>
            <form id="inline-hosting-form">
                <div class="inline-edit-section">
                    <label class="section-label">Hosting Type:</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="inline_hosting_type" value="1" id="inline-hosting-shared">
                            <span class="radio-custom"></span>
                            <span class="radio-label"><?php echo esc_html(get_hosting_type_label('1')); ?></span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="inline_hosting_type" value="2" id="inline-hosting-vps">
                            <span class="radio-custom"></span>
                            <span class="radio-label"><?php echo esc_html(get_hosting_type_label('2')); ?></span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="inline_hosting_type" value="3" id="inline-hosting-other">
                            <span class="radio-custom"></span>
                            <span class="radio-label"><?php echo esc_html(get_hosting_type_label('3')); ?></span>
                        </label>
                    </div>
                </div>

                <div class="inline-edit-section">
                    <label class="section-label">Monthly Hosting Cost (in <?php echo esc_html(strtoupper(get_currency_info()['currency'])); ?>):</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="inline_hosting_cost" value="1" id="inline-cost-less-10">
                            <span class="radio-custom"></span>
                            <span class="radio-label"><?php echo esc_html(get_hosting_cost_label('1')); ?></span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="inline_hosting_cost" value="2" id="inline-cost-10-20">
                            <span class="radio-custom"></span>
                            <span class="radio-label"><?php echo esc_html(get_hosting_cost_label('2')); ?></span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="inline_hosting_cost" value="3" id="inline-cost-20-50">
                            <span class="radio-custom"></span>
                            <span class="radio-label"><?php echo esc_html(get_hosting_cost_label('3')); ?></span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="inline_hosting_cost" value="4" id="inline-cost-more-50">
                            <span class="radio-custom"></span>
                            <span class="radio-label"><?php echo esc_html(get_hosting_cost_label('4')); ?></span>
                        </label>
                    </div>
                </div>

                <div class="inline-form-actions">
                    <button type="submit" class="button button-primary">Update</button>
                    <button type="button" class="button button-secondary" id="cancel-inline-edit">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
