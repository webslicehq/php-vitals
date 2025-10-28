<?php

class PhpVitals
{
	private $startTime;
	private $results = [];
	private $totalTime = 0;

	public static function getTest(int $index = 0)
	{
		$string_test = "    the quick <b>brown</b> fox jumps <i>over</i> the lazy
			dog and eat <span>lorem ipsum</span><br/> Valar morghulis  <br/>\n\r
			hello \n we are out of spoons, Neo! <span class='alert alert-danger'>
			We are out of spoons, Neo!</span>      ";
		$string_complex = str_repeat($string_test, 2);
		$regex_pattern = '/[\s,]+/';
		$array_test = range(0, 999);
		$json_data = [
			'menu' => [
				'id' => 'file',
				'value' => 'File',
				'popup' => [
					'menuitem' => [
						['value' => 'New', 'onclick' => 'CreateNewDoc()'],
						['value' => 'Open', 'onclick' => 'OpenDoc()'],
						['value' => 'Close', 'onclick' => 'CloseDoc()'],
					],
				],
			],
		];

		$run_times = 50000;
		$run_times_slow = 5000;
		$run_times_slowest = 1;

		$tests = [
			[
				'category' => 'php_features',
				'name' => 'PHP: Exception',
				'function' => function () {
					try {
						throw new Exception('Test Exception');
					} catch (Exception $e) {
						$message = $e->getMessage();
					}
				},
				'iterations' => $run_times
			],
			[
				'category' => 'php_features',
				'name' => 'PHP: Type Hints',
				'function' => function () {
					$testFunc = function (string $a, int $b): string {
						return $a . $b;
					};
					$testFunc('test', 123);
				},
				'iterations' => $run_times
			],
			[
				'category' => 'php_features',
				'name' => 'PHP: OOP Public Properties',
				'function' => function () {
					$obj = new class {
						public $number = 0;
					};
					for ($i = 0; $i < 50; $i++) {
						$obj->number = $i;
						$dummy = $obj->number;
					}
				},
				'iterations' => $run_times
			],
			[
				'category' => 'php_features',
				'name' => 'PHP: OOP Getter/Setter',
				'function' => function () {
					$obj = new class {
						private $number = 0;

						public function getNumber()
						{
							return $this->number;
						}

						public function setNumber($n)
						{
							$this->number = $n;
						}
					};
					for ($i = 0; $i < 50; $i++) {
						$obj->setNumber($i);
						$dummy = $obj->getNumber();
					}
				},
				'iterations' => $run_times
			],
			[
				'category' => 'php_features',
				'name' => 'PHP: Magic Methods',
				'function' => function () {
					$obj = new class {
						private $number = 0;

						public function __get($n)
						{
							return $this->number;
						}

						public function __set($n, $v)
						{
							$this->number = $v;
						}
					};
					for ($i = 0; $i < 50; $i++) {
						$obj->number = $i;
						$dummy = $obj->number;
					}
				},
				'iterations' => $run_times
			],
			[
				'category' => 'php_features',
				'name' => 'PHP: Null Coalesce',
				'function' => function () {
					$arr = [0 => 0, 2 => 2, 4 => 4];
					for ($i = 0; $i < 50; $i++) {
						$dummy = $arr[$i % 5] ?? 0;
					}
				},
				'iterations' => $run_times
			],
			[
				'category' => 'php_features',
				'name' => 'PHP: Spaceship Operator',
				'function' => function () {
					for ($i = 0; $i < 50; $i++) {
						$dummy = $i % 5 <=> 2;
					}
				},
				'iterations' => $run_times
			],
			[
				'category' => 'math',
				'name' => 'Math: Simple',
				'function' => function () {
					$a = 0;
					for ($i = 0; $i < 50; $i++) {
						$a += $i;
						$a *= 2;
						$a -= $i;
						$a /= 2;
					}
				},
				'iterations' => $run_times
			],
			[
				'category' => 'math',
				'name' => 'Math: Complex',
				'function' => function () {
					$a = 0;
					for ($i = 0; $i < 50; $i++) {
						$a += sin($i) * cos($i);
						$a *= exp($i / 1000);
						$a = sqrt($a * $a);
					}
				},
				'iterations' => $run_times
			],
			[
				'category' => 'math',
				'name' => 'Math: Increment',
				'function' => function () {
					$a = 0;
					for ($i = 0; $i < 50; $i++) {
						++$a;
						++$a;
						++$a;
						++$a;
					}
				},
				'iterations' => $run_times
			],
			[
				'category' => 'math',
				'name' => 'Math: Decrement',
				'function' => function () {
					$a = 1000;
					for ($i = 0; $i < 50; $i++) {
						--$a;
						--$a;
						--$a;
						--$a;
					}
				},
				'iterations' => $run_times
			],
			[
				'category' => 'string',
				'name' => 'String: Concatenation',
				'function' => function () use ($string_test) {
					$str = '';
					for ($i = 0; $i < 50; $i++) {
						$str .= $string_test;
					}
				},
				'iterations' => $run_times
			],
			[
				'category' => 'string',
				'name' => 'String: str_replace',
				'function' => function () use ($string_complex) {
					str_replace(
						['quick', 'brown', 'fox', 'lazy'],
						['slow', 'black', 'bear', 'active'],
						$string_complex
					);
				},
				'iterations' => $run_times
			],
			[
				'category' => 'string',
				'name' => 'String: Regular Expression',
				'function' => function () use ($string_complex, $regex_pattern) {
					preg_match_all($regex_pattern, $string_complex, $matches);
				},
				'iterations' => $run_times
			],
			[
				'category' => 'string',
				'name' => 'String: Strpos',
				'function' => function () use ($string_complex) {
					strpos($string_complex, 'quick');
					strpos($string_complex, 'brown');
					strpos($string_complex, 'fox');
					strpos($string_complex, 'lazy');
				},
				'iterations' => $run_times
			],
			[
				'category' => 'string',
				'name' => 'String: Explode',
				'function' => function () use ($string_complex) {
					explode(' ', $string_complex);
				},
				'iterations' => $run_times
			],
			[
				'category' => 'array',
				'name' => 'Array: Loop',
				'function' => function () use ($array_test) {
					foreach ($array_test as $key => $value) {
						$dummy = $key + $value;
					}
				},
				'iterations' => $run_times
			],
			[
				'category' => 'array',
				'name' => 'Array: Sorting',
				'function' => function () use ($array_test) {
					$arr = $array_test;
					sort($arr);
				},
				'iterations' => $run_times_slow
			],
			[
				'category' => 'array',
				'name' => 'Array: Complex Sort',
				'function' => function () use ($array_test) {
					$arr = $array_test;
					usort($arr, function ($a, $b) {
						return strlen((string) $a) - strlen((string) $b);
					});
				},
				'iterations' => $run_times_slow
			],
			[
				'category' => 'array',
				'name' => 'Array: Fill',
				'function' => function () {
					$arr = array_fill(0, 10000, 'test');
				},
				'iterations' => $run_times_slow
			],
			[
				'category' => 'array',
				'name' => 'Array: Unset',
				'function' => function () {
					$arr = range(0, 500);
					for ($i = 0; $i < 500; $i++) {
						unset($arr[$i]);
					}
				},
				'iterations' => $run_times_slow
			],
			[
				'category' => 'array',
				'name' => 'Array: Copy',
				'function' => function () use ($array_test) {
					$arr = $array_test;
					$copy = $arr;
				},
				'iterations' => $run_times
			],
			[
				'category' => 'crypto',
				'name' => 'Hash: MD5',
				'function' => function () use ($string_complex) {
					md5($string_complex);
				},
				'iterations' => $run_times
			],
			[
				'category' => 'crypto',
				'name' => 'Hash: SHA256',
				'function' => function () use ($string_complex) {
					hash('sha256', $string_complex);
				},
				'iterations' => $run_times
			],
			[
				'category' => 'crypto',
				'name' => 'Crypto: Password Hash',
				'function' => function () {
					$hash = password_hash('test_password', PASSWORD_DEFAULT, ['cost' => 14]);
					password_verify('test_password', $hash);
				},
				'iterations' => $run_times_slowest
			],
            [
                'category' => 'file',
                'name' => 'File: Stream Operations',
                'function' => function () use ($string_complex) {
                $stream = fopen('php://memory', 'r+');
                fwrite($stream, $string_complex);
                rewind($stream);
                fread($stream, strlen($string_complex));
                fclose($stream);
                },
                'iterations' => $run_times_slow
            ],
            [
                'category' => 'file',
                'name' => 'File: Memory Stream',
                'function' => function () use ($string_complex) {
                $temp = fopen('php://temp', 'r+');
                fwrite($temp, $string_complex);
                rewind($temp);
                fread($temp, strlen($string_complex));
                fclose($temp);
                },
                'iterations' => $run_times_slow
            ],
			[
				'category' => 'serialization',
				'name' => 'Serialize: PHP',
				'function' => function () use ($array_test) {
					serialize($array_test);
				},
				'iterations' => $run_times
			],
			[
				'category' => 'serialization',
				'name' => 'Serialize: JSON',
				'function' => function () use ($json_data) {
					json_encode($json_data);
					json_decode(json_encode($json_data), true);
				},
				'iterations' => $run_times
			],
		];

        $test = $tests[$index];
		$test['total_tests'] = count($tests);

		return $test;
	}

	public function start()
	{
		[$usec, $sec] = explode(' ', microtime());
		$this->startTime = ((float) $usec + (float) $sec);
	}

	public function stop($title)
	{
		[$usec, $sec] = explode(' ', microtime());
		$time = ((float) $usec + (float) $sec) - $this->startTime;
		$this->results[$title] = $time;
		$this->totalTime += $time;

		return $time;
	}

	public function getResults()
	{
		return $this->results;
	}

	public function getTotalTime()
	{
		return $this->totalTime;
	}

	public function loadPage()
	{
		$server_software = $_SERVER['SERVER_SOFTWARE'];
		$date = date('Y-m-d H:i:s');
		$memory_limit = ini_get('memory_limit');
		$php_version = PHP_VERSION;
		
		function get_currency_info() {
			$currency = 'usd';
			$currency_symbol = "$";

			return [
				'currency' => $currency,
				'currency_symbol' => $currency_symbol
				];
		}

		echo <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Vitals Performance Test</title>
    <link href="https://phpvitals.com/phpvitals.css" rel="stylesheet">
    <script src="https://phpvitals.com/phpvitals.js"></script>
</head>
<body>
    <div class="container">
            <div class="system-info">
                <div>
                    <h1>PHP Vitals Performance Test</h1>
                </div>
                  <div class="grade-container">
                        <div id="grade-display" class="grade-display">--</div>
                        <div id="grade-description" class="grade-description">--</div>
                    </div>
                <div class="timer-score">
                    <div class="timer-container">
                        <div id="loading" class="loading" style="display: none;">
                            <div class="spinner"></div>
                            Running performance tests...
                        </div>
                    </div>
                </div>
                <div class="hosting-info" id="hosting-info">
                        <form id="hosting-info-form" class="hosting-info-form" style="font-size: 13px;">
                            <div class="form-section" id="hosting-info-section">
                                <h3>Hosting Information</h3>
                                <p>Help us understand your hosting setup for better performance comparisons.</p>
                                <div class="server-hosting-section">
                                    <div class="hosting-type-group">
                                        <label class="section-label">What type of hosting do you use?</label>
                                        <div class="radio-group">
                                            <label class="radio-option">
                                                <input type="radio" name="hosting_type" value="1" id="hosting-shared">
                                                <span class="radio-custom"></span>
                                                <span class="radio-label">Shared Hosting</span>
                                            </label>
                                            <label class="radio-option">
                                                <input type="radio" name="hosting_type" value="2" id="hosting-vps">
                                                <span class="radio-custom"></span>
                                                <span class="radio-label">VPS/Dedicated</span>
                                            </label>
                                            <label class="radio-option">
                                                <input type="radio" name="hosting_type" value="3" id="hosting-other">
                                                <span class="radio-custom"></span>
                                                <span class="radio-label">Self-hosted</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="hosting-cost-group">
                                        <label class="section-label">What is your monthly hosting cost? (in USD)</label>
                                        <div class="radio-group">
                                            <label class="radio-option">
                                                <input type="radio" name="hosting_cost" value="1" id="cost-less-10">
                                                <span class="radio-custom"></span>
                                                <span class="radio-label">$ (Less than $10/month)</span>
                                            </label>
                                            <label class="radio-option">
                                                <input type="radio" name="hosting_cost" value="2" id="cost-10-20">
                                                <span class="radio-custom"></span>
                                                <span class="radio-label">$$ ($10 to $20/month)</span>
                                            </label>
                                            <label class="radio-option">
                                                <input type="radio" name="hosting_cost" value="3" id="cost-20-50">
                                                <span class="radio-custom"></span>
                                                <span class="radio-label">$$$ ($20 to $50/month)</span>
                                            </label>
                                            <label class="radio-option">
                                                <input type="radio" name="hosting_cost" value="4" id="cost-more-50">
                                                <span class="radio-custom"></span>
                                                <span class="radio-label">$$$$ (More than $50/month)</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3>Terms and Privacy</h3>
                                <div class="terms-content">
                                    <p>This tool connects to <a href="https://phpvitals.com" target="_blank">PHP Vitals</a> to compare the user's benchmark test results.</p>
                                    <p>If you would like to share and compare your results, please read over and check the <a href="https://phpvitals.com/privacy-policy" target="_blank">Privacy Policy</a> and <a href="https://phpvitals.com/terms" target="_blank">Terms of Use</a>.</p>
                                </div>

                                <div class="terms-checkbox">
                                    <label>
                                        <input type="checkbox" id="terms-accept" name="terms_accept">
                                        I have read and agree to the Terms of Use and Privacy Policy.
                                    </label>
                                </div>
                                                    <div class="benchmark-controls">
                        <button id="run-benchmark" class="button button-primary button-large">
                            Run Benchmark
                        </button>
                        </div>
                            </div>
                        </form>
                    </div>
                <div id="results">
			                <table class="test-results-table" id="test-results-table" style="display: none;">
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
        </div>
</body>
</html>
EOT;
	}

	public function processPost()
	{
		@set_time_limit(0);

		$test_index = isset($_POST['test_index']) ? intval($_POST['test_index']) : 0;

		$test = $this->getTest($test_index);
		$iterations = $test['iterations'];

		try {
			$this->start();
			for ($i = 0; $i < $iterations; $i++) {
				try {
					$test['function']();
				} catch (Exception $e) {
					if (strpos($e->getMessage(), 'extension not available') !== false) {
						header('Content-Type: application/json');
						echo json_encode([
							'test_name' => $test['name'],
							'test_index' => $test_index,
							'category' => $test['category'],
							'time' => 0,
							'ops_per_ms' => 0,
							'total_tests' => $test['total_tests'],
							'skipped' => true,
							'skip_reason' => $e->getMessage(),
						]);

						return;
					}

					throw $e;
				}
			}
			$time = $this->stop($test['name']);

            $ops_per_ms = $iterations / $time / 1000;

			$test_results = [
				'test_name' => $test['name'],
				'test_index' => $test_index,
				'category' => $test['category'],
				'time' => $time,
				'ops_per_ms' => $ops_per_ms,
				'total_tests' => $test['total_tests'],
                'php_version' => PHP_VERSION,
                'php_release' => PHP_RELEASE_VERSION
			];

			header('Content-Type: application/json');
			echo json_encode([
				'success' => true,
				'data' => $test_results,
			]);
		} catch (Exception $e) {
			header('Content-Type: application/json');
			echo json_encode([
				'message' => 'Test failed: ' . $e->getMessage(),
				'test_name' => $test['name'],
			]);
		}
	}
}

$php_vitals = new PhpVitals();

switch ($_SERVER['REQUEST_METHOD']) {
	case 'POST':
		$php_vitals->processPost();

		break;
	case 'GET':
		$php_vitals->loadPage();

		break;
}
