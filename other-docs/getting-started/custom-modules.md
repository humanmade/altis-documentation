---
order: 30
---
# Custom Modules

Platform is designed in a modular way, allowing your project to turn on and off individual modules as appropriate. You can use this same infrastructure for your own custom modules, allowing you to take advantage of frameworks such as the configuration and documentation modules.


## Location

Custom modules can be created in one of two ways:

* For project-specific modules, modules should be stored in the project's repository, and loaded in manually.
* For reusable modules, modules should be created as separate repositories, and published to a Composer repository.

We recommend developing modules following the project-specific guide first, then converting to reusable modules later.


## Project-Specific Modules

To start a new project-specific module to your project, first create a directory for the module to live in. We recommend using `content/mu-plugins/{your-module-name}`, to match the commonly-used [WordPress must-use plugins](https://codex.wordpress.org/Must_Use_Plugins) pattern.

Your module's file structure can be structured however you like, but we recommend following the [Human Made plugin structure](https://engineering.hmn.md/standards/structure/#plugin-structure). This structure ensures your code remains maintainable.

There are two basic parts required to creating a module: load your module's file in using the Composer autoloader, and register your module with the Platform core. We recommend having a main entrypoint file (conventionally called `load.php`) which handles loading and registering your module, with other code split into functions and classes in separate files (conventionally within an `inc` directory).


### Loading Your Module

In order to use your module, the module needs to be loaded in by the Composer autoloader. This allows the module to register itself with the Platform core, as well as load in any functions or classes it needs.

To load in your module's entrypoint file, add it to a `autoload.files` entry in your project's `composer.json`. For example, for a module called `your-module` with an entrypoint file called `load.php`, your `composer.json` should contain:

```json
"autoload": {
    "files": [
        "content/mu-plugins/your-module/load.php"
    ]
}
```

After adding this, run `composer dump-autoload` to regenerate the autoload files for your project.


### Registering Your Module

Within your module's entrypoint file, your module needs to register itself with the Platform core. To do this, you need to add an action on `hm-platform.modules.init` which calls `HM\Platform\Module::register`. This function call registers the module as well as any default configuration and a callback for the module.

A basic registration call looks like:

```php
use HM\Platform\Module;

add_action( 'hm-platform.modules.init', function () {
	Module::register(
		// Module ID:
		'your-module',

		// Module directory:
		__DIR__,

		// Human-readable name:
		'Your Module',

		// Default configuration, as an array:
		[
			// `enabled` is a special configuration key, which sets whether the module is
			// enabled by default or not.
			'enabled' => true,
		],

		// Function to call if the module is enabled.
		__NAMESPACE__ . '\\bootstrap'
	);
} );
```

We recommend using a function-exists-guard to only register your action if running in a Platform context, as the autoloader can also be used by static analysis and other tools including PHPUnit. Your function-exists-guard should look like:

```php
// Don't self-initialize if this is not a Platform execution.
if ( ! function_exists( 'add_action' ) ) {
	return;
}
```

Your entrypoint file can declare constants or load in other files as necessary to load functions and classes. A typical entrypoint file looks like:

```php
<?php

namespace YourProject;

use HM\Platform\Module;

const DIRECTORY = __DIR__;

// Load in namespaced-functions.
require_once __DIR__ . '/inc/namespace.php';

// Don't self-initialize if this is not a Platform execution.
if ( ! function_exists( 'add_action' ) ) {
	return;
}

add_action( 'hm-platform.modules.init', function () {
	Module::register(
		'your-module',
		DIRECTORY,
		'Your Module',
		[
			'enabled' => true,
		],
		__NAMESPACE__ . '\\bootstrap'
	);
} );
```


### Module Settings

When registering your module, you pass various parameters to `Module::register`. These are used to control the behaviour of your module.

Your module's directory is used for automated loading and parsing of documentation. The Documentation module automatically looks for a `docs` subdirectory within this directory and parses Markdown files from this.

The default configuration passed in integrates with the [configuration system](configuration.md), and can be used to configure your module on different environments, or to provide an easy way to enable and disable specific functionality. Within your module, you can use `HM\Platform\get_config()` to retrieve the resolved configuration; your module configuration can be accessed as `get_config( $id )`, where `$id` is the same ID you pass to the module registration.


## Reusable Modules

For reusable modules, follow the project-specific module development process above. Reusable modules are simply regular Composer packages, so you can follow existing guides on how to publish Composer packages, or follow the Platform-specific guide below.

Once you're ready to convert your module into a reusable module, the first step is to split your module's code into a separate repository. Composer requires separate packages to be published from separate repositories.

In your new repository, your module needs a `composer.json` to specify dependencies, the package's name, and autoloading code. Your package should declare a dependency on the core Platform package, fixed to the major Platform version you are working with. The autoloader specification should be migrated from your project's `composer.json` to the module's.

A basic `composer.json` should look like:

```
{
	"name": "company-name/your-project",
	"autoload": {
		"files": [
			"load.php"
		],
		"classmap": [
			"inc/",
		]
	},
	"require": {
		"humanmade/platform-core": "~1.0"
	}
}
```

For open-source packages, you should then publish this package to [Packagist](https://packagist.org/).

For private packages you don't want to publish publically, you can use Private Packagist or Satis; we recommend following the [Composer private packages guide](https://getcomposer.org/doc/articles/handling-private-packages-with-satis.md).

Once published, this can then be added to your project's `composer.json` file via the `composer require` command.


## Next Steps

Platform is built atop the open-source WordPress CMS. In addition to custom Platform modules, you can also use [third-party WordPress plugins](third-party-plugins.md) to take advantage of the open-source ecosystem.
