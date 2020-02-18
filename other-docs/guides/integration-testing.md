# Integration Testing

**Note**: It is recommended to upgrade to Altis v3 to get access to the built in zero config integration testing feature.

Integration testing allows you to run unit tests in the context of the full Altis application. This is important for testing the behaviour of custom code that needs to interact with the database and other services.

Because of the way Altis is loaded `phpunit` must be run in a specific way to ensure all modules and custom modules are set up correctly.

## Setting Up PHPUnit

The first step is to get PHPUnit added and set up for your project. Run the following command to add PHPUnit and the WP PHPunit testing framework to your project's dev dependencies:

```sh
composer require --dev phpunit/phpunit:^7.1.4 wp-phpunit/wp-phpunit:^5.2.0
```

### PHPUnit Configuration

Create a file called `phpunit.xml` (or `phpunit.xml.dist`) in your project root with the following content, or if you already have a `phpunit.xml` file make sure the key elements from the below example are copied over to it:

```xml
<phpunit
	bootstrap="tests/bootstrap.php"
	backupGlobals="false"
>
	<testsuites>
		<testsuite>
			<directory suffix="-test.php">tests</directory>
		</testsuite>
	</testsuites>
</phpunit>
```

The above file makes a few assumptions:

- Your `bootstrap.php` file is located in a directory called `tests` in the project root
- Your test classes are in files with the suffix `-test.php`

Feel free to make any updates to match the location of where you want your tests to go and the directories, prefixes and suffixes you're actually using.

### The Bootstrap File

The bootstrap file is loaded before the tests run, and is used to load Altis. Copy the following code to a file called `tests/bootstrap.php` (or wherever you configured the bootstrap file to be located in `phpunit.xml`).

If you already have a `bootstrap.php` you should only need to copy over any custom `tests_add_filter()` calls not present below to this template.

```php
<?php
/**
 * Bootstrap the plugin unit testing environment.
 */

define( 'ROOT_DIR', dirname( __DIR__ ) );
define( 'WP_PHP_BINARY', 'php' );

// Ensure tests are run in multisite mode.
define( 'WP_TESTS_MULTISITE', true );

// Set the path to the tests config file.
define( 'WP_TESTS_CONFIG_FILE_PATH', __DIR__ . '/wp-tests-config.php' );

// Get the WP Unit Test library directory from $_ENV.
$_tests_dir = getenv( 'WP_TESTS_DIR' ) ?: getenv( 'WP_PHPUNIT__DIR' );

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php" . PHP_EOL;
	exit( 1 );
}

// Give early access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually set the default theme.
 */
function _manually_load_theme() {
	register_theme_directory( WP_CONTENT_DIR . '/themes/' );
	add_filter( 'pre_option_template', function () {
		return 'default-theme-name';
	} );
	add_filter( 'pre_option_stylesheet', function () {
		return 'default-theme-name';
	} );
}

tests_add_filter( 'muplugins_loaded', '_manually_load_theme' );

/**
 * Manually load any non mu-plugins being tested.
 */
function _manually_load_plugins() {

}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugins' );

/**
 * Re-map the default `/uploads` folder with our own `/test-uploads` for tests.
 *
 * WordPress core runs a method (scan_user_uploads) on the first instance of
 * `WP_UnitTestCase`. This method scans every single folder and file in the
 * uploads directory. This becomes a problem with any significant quantity of
 * uploads having been pulled into a local environment and has been
 * known to take more than 5 minutes to run on certain local installs.
 *
 * This filter prevents any potential issues arising from running imports
 * locally and speeds up overall test execution. We do this by adding a unique
 * test uploads folder just for our tests to reduce load.
 */
tests_add_filter( 'upload_dir', function ( $dir ) {
	return array_map( function ( $item ) {
		if ( is_string( $item ) ) {
			$item = str_replace( '/uploads', '/test-uploads', $item );
		}
		return $item;
	}, $dir );
}, 20 );

// Load up the Altis and WordPress testing environment.
require $_tests_dir . '/includes/bootstrap.php';
```

### WordPress Configuration File

Finally you will need to create a file called `tests/wp-tests-config.php` (or in the directory you are using for tests). Copy the following template:

```php
<?php
/*
 * Tests config for Altis.
 */

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );
define( 'WP_CACHE_KEY_SALT', 'phpunit' );

// Prevents Local Chassis setting MULTISITE to true during db install.
if ( defined( 'WP_INSTALLING' ) ) {
	define( 'MULTISITE', false );
}

// Fake the user agent value.
$_SERVER['HTTP_USER_AGENT'] = 'Amazon CloudFront';

// Provide a reference to the app root directory early.
define( 'Altis\\ROOT_DIR', dirname( __DIR__ ) );

// Load the plugin API (like add_action etc) early, so everything loaded
// via the Composer autoloaders can using actions.
require_once Altis\ROOT_DIR . '/wordpress/wp-includes/plugin.php';

// Load the whole autoloader very early, this will also include
// all `autoload.files` from all modules.
require_once Altis\ROOT_DIR . '/vendor/autoload.php';

do_action( 'altis.loaded_autoloader' );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', Altis\ROOT_DIR . '/wordpress/' );
}

if ( ! defined( 'WP_CONTENT_DIR' ) ) {
	define( 'WP_CONTENT_DIR', Altis\ROOT_DIR . '/content' );
}

if ( ! defined( 'WP_CONTENT_URL' ) ) {
	$protocol = ! empty( $_SERVER['HTTPS'] ) ? 'https' : 'http';
	define( 'WP_CONTENT_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/content' );
}

if ( ! defined( 'WP_INSTALLING' ) || ! WP_INSTALLING ) {
	// Multisite is always enabled, unless some spooky
	// early loading code tried to change that of course.
	if ( ! defined( 'MULTISITE' ) ) {
		define( 'MULTISITE', true );
	}
}

if ( ! isset( $table_prefix ) ) {
	$table_prefix = 'wptests_';
}

/*
 * DB constants are expected to be provided by other modules, as they are
 * environment specific.
 */
$required_constants = [
	'DB_HOST',
	'DB_NAME',
	'DB_USER',
	'DB_PASSWORD',
];

foreach ( $required_constants as $constant ) {
	if ( ! defined( $constant ) ) {
		die( "$constant constant is not defined." );
	}
}
```

## Running Unit Tests

You're now ready to run your tests. To run the tests use the following command:

```sh
php -d auto_prepend_file=wordpress/wp-includes/plugin.php vendor/bin/phpunit
```

This will load the WordPress plugin API immediately before the `vendor/autoload.php` is loaded and ensures that the Altis modules and any custom modules using the `function_exists( 'add_action' )` guard will be properly loaded.

You will need to run these tests from your local development environment.

### Local Chassis

```sh
# On the host machine:
cd chassis
vagrant ssh
# In the chassis VM:
cd /chassis
php -d auto_prepend_file=wordpress/wp-includes/plugin.php vendor/bin/phpunit
```

### Local Server

```sh
# On the host machine
composer local-server shell
# On the docker VM:
php -d auto_prepend_file=wordpress/wp-includes/plugin.php vendor/bin/phpunit
```
