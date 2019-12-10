---
order: 40
---
# Third-Party Plugins

Altis is built atop the open-source WordPress CMS. In addition to custom Altis modules, you can also use third-party WordPress plugins to take advantage of the open-source ecosystem.

There are two ways to add plugins to your project: either commit the plugin to your project (or use submodules), or manage it via Composer.


## Managing Plugins via Composer

Plugins can be managed via Composer dependencies, similar to how Altis modules are added.

Some plugins are natively available via Composer, and can be found on Packagist via the [`wordpress-plugin` type](https://packagist.org/?type=wordpress-plugin).

The starter project automatically configures Composer to install plugins into the correct place for your project, but if you started from scratch you may need to configure this yourself. First, install `composer/installers` as a dependency, which will allow installing these types of projects. Next, configure Composer under `extra.installer-paths` in your `composer.json` to place them into the correct directory:

```json
"extra": {
	"installer-paths": {
		"content/plugins/{$name}/": [
			"type:wordpress-plugin"
		],
		"content/themes/{$name}/": [
			"type:wordpress-theme"
		]
	}
}
```

Some plugins are not available natively on Packagist, and are available only via the [WordPress.org Plugin Repository](https://wordpress.org/plugins/). These can be installed via a third-party Composer repository called [WordPress Packagist](https://wpackagist.org/).

To set up and configure WordPress Packagist, first add the custom repository to your project's `composer.json` under a `repositories` key:

```json
"repositories": [
	{
		"type":"composer",
		"url":"https://wpackagist.org"
	}
]
```

To install plugins, you can now use `composer require wpackagist-plugin/{plugin-name}`, where `{plugin-name}` is the "slug" of the plugin from the WordPress.org Plugin Repository to install.

For example, [Akismet](https://wordpress.org/plugins/akismet/) is available at `https://wordpress.org/plugins/akismet/`, so the "slug" of the plugin is `akismet`. You can install this with:

```sh
composer require wpackagist-plugin/akismet
```


## Managing Plugins Manually

Third-party plugins should be placed inside the `content/plugins/` directory. Each plugin should be contained in its own directory within the plugins directory, with one file containing a [comment header](https://developer.wordpress.org/plugins/plugin-basics/#getting-started).

Generally speaking, we recommend using Git submodules if you're installing modules manually. This reduces the amount of code necessary in your repository, and makes managing updates much easier.


## Pre-Approved Plugins

We maintain a list of trusted 3rd party plugins that you can refer to for recommendations, [read more about our pre-approved plugins here](docs://guides/pre-approved-plugins.md).
