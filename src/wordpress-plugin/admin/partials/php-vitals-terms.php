<?php
if (!defined('WPINC')) {
	exit;
}
?>

<div class="php-vitals-terms">
    <div class="terms-container">
	<h2>Compare my results!</h2>
        <div class="terms-content">
			<?php if (get_option('phpvitals_terms_accepted')): ?>
				<p>You have already accepted the Terms of Use and Privacy Policy.</p></br>
				<p>If you would like to disable this feature, please uncheck the following checkbox:</p>
			<?php else: ?>
				<p>This plugin connects to <a href="https://phpvitals.com" target="_blank">PHP Vitals</a> to compare the user's benchmark test results (This is an optional service).</p></br>
				<p>If the "Compare my Results" option is active, each time the user runs the Benchmark Test their results will be shared with PHP Vitals.</p></br>
				<p>If you would like to share and compare your results, please read over and check the <a href="https://phpvitals.com/privacy-policy" target="_blank">Privacy Policy</a> and <a href="https://phpvitals.com/terms" target="_blank">Terms of Use</a>.</p></br>
			<?php endif; ?>
        </div>

        <form id="terms-acceptance-form" class="terms-form">
            <div class="terms-checkbox">
                <label>
                    <input type="checkbox" id="terms-accept" name="terms_accept"
						<?php echo get_option('phpvitals_terms_accepted') ? 'checked' : ''; ?>>
                    I have read and agree to the Terms of Use and Privacy Policy.
                </label>
            </div>
            <button type="submit" class="button button-primary button-large" id="accept-terms">Save</button>
        </form>
    </div>
</div>
