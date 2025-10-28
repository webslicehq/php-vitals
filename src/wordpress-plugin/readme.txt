=== PHP Vitals ===
Contributors: webslicehq
Plugin URI: https://phpvitals.com
Tags: benchmark, speed, hosting, performance, php
Requires at least: 6.2
Tested up to: 6.8
Requires PHP: 7.0
Stable tag: 1.2.1
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

How fast is your web host?

Dozens of PHP speed tests, 1 overall grade: The easy way to compare hosting performance.

== Description ==

## Bringing Transparency to Your Web Host’s PHP Performance

The faster your websites run, the better. Better SEO, better UX, better returns on your effort. But in any PHP-based system (including WordPress, Drupal, Joomla, Magento, and many others) there’s an important performance factor that’s hard for you to see and mostly beyond your control. It’s your web hosting. How fast does PHP run on the infrastructure that your web host provides?

PHP Vitals gives you a simple way to find out.


## A Free, Quick and Easy Speed Test

In a minute or two, PHP Vitals analyses exactly how well your server performs on dozens of different tests of PHP performance. When you run PHP Vitals you’ll see an overall grade, and you can see where your web host belongs on the PHP Vitals leaderboard.

You can’t see your server hardware. You don’t know how many others are sharing your resources. You don’t know if your hosting is rate-limited, over-provisioned, or dependent on ancient CPUs. PHP Vitals lifts the lid on what you’re really getting for your money.


## What Gets Tested

PHP Vitals focuses on base PHP functions. Base categories are:
- `php_features`
- `math`
- `string`
- `array`
- `crypto`
- `file`
- `serialization`

Specific operations that are tested include:
- PHP Class (getter/setter).
- Public properties and methods.
- Simple math operations.
- Complex math operations.
- String concatenations.
- Regular expressions (regex).
- Functions: explode, strpos.
- Operations: Array sorting, set, unset, copy, and others.
- Password hashing.
- MD5 hashing.
- SHA256 hashing.

There are no MySQL or database benchmarks included.


## Features

PHP Vitals suite of PHP performance benchmarking tests usually takes only 1-2 minutes to run. The tests give you live performance data.

Each time you run PHP Vitals you’ll see a breakdown of individual test data and an overall grade (A+ to F). Test data is objective, for example the time taken for each test. Grades are based on the overall time taken. The subjective grading scale is likely to change over time (e.g. an A in 2025 is unlikely to still be an A in 2027).

With the option to submit test results to the PHP Vitals leaderboard, you can let developers around the world see a summary of your results, and the hosting provider that it came from. Sharing is optional and no personal data is collected, shared or stored.

PHP Vitals data is presented through a beautiful and responsive admin interface. Run and share tests on demand, and build up a history of performance grades over time.


## Usage on WordPress

PHP Vitals is very straightforward. Install the plugin, open "PHP Vitals" from the main menu in your WordPress admin dashboard, and click “Run Benchmark”.


== Installation ==

Install from WordPress Plugins page. Alternatively, download the .zip file from phpvitals.com and install it manually through the Plugins option in your WordPress dashboard.


== Frequently Asked Questions ==
Q: Is PHP Vitals a free plugin?
A: Yes, it’s 100% free.

Q: Does PHP Vitals collect or report any of my personal data or website data?
A: No personal data or information about your website content is collected or shared.

Q: Why does PHP performance matter for WordPress?
A: WordPress is a PHP-based CMS, so WordPress performance (e.g. how fast your website loads for visitors) depends on PHP performance (i.e. how fast your hosting provider or your server runs PHP).

Q: Who created PHP Vitals?
A: We’re Webslice, a hosting company that loves LAMP stack and PHP development. We also love a fair fight, so we wanted a way to compare hosting providers (including ourselves) based on raw performance rather than who’s got the biggest advertising budget or the cosiest industry relationships.

We have a couple of sister companies with the same owners and team—SiteHost and MyHost.

== Changelog ==
= 1.2.1 =
* Adjust styles

= 1.2.0 =
* Move to Tools menu
* Adjust hashing and crypt tests
* Add hosting information
* Dynamically assigned grades

= 1.1.0 =
* Update Class and Function names
* Update readme

= 1.0.2 =
* Adjust the benchmark tests
* Fix the style in dashboard

= 1.0.1 =
* External services documented
* Links to Terms of Use and Privacy Policy

= 1.0.0 =
* Description and FAQ updated

= 0.9.8 =
* Minor fix to data sent to leaderboard

= 0.9.7 =
* Minor fix to database queries

= 0.9.6 =
* Initial relase

== Upgrade Notice ==
= 1.2.0 =
This release uses a dynamic grading system comparing your time to other servers around the world. It adjusts the hashing and crypt tests. It also moves PHP Vitals to the Tools menu in the WP Dashboard.

= 0.9.7 =
This release includes minor fixes to data sent to leaderboard and database query structure.

== Screenshots ==
1. Speedtest results in the WordPress Dashboard.

== External services ==

This plugin connects to [PHP Vitals](https://phpvitals.com) to compare the user's benchmark test results (This is an optional service).

If the "Compare my Results" option is active, each time the user runs the Benchmark Test their results will be shared with PHP Vitals.

This service is provided by PHP Vitals: [Privacy Policy](https://phpvitals.com/privacy-policy), [Terms of Use](https://phpvitals.com/terms)
