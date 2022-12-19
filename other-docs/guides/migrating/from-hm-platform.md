---
title: From HM Platform
order: 10
---
# Migrating from HM Platform

This guide covers how to migrate a HM Platform site to Altis.

## Before You Begin -- Database Configuration

Altis uses the `utf8mb4` MySQL database character set. It's important to check the charset configuration of the site you're migrating from and update it before importing the database into Altis to prevent data corruption.

## Setup Composer

If the project does not already have a `composer.json` in the root of the repository, you should run `composer init` from the project root. This will prompt you fill out the required fields. For now, don’t bother adding any dependencies. If you already have a `composer.json` move to Remove Dependencies.

## Remove Dependencies

We’ll need to remove all the dependencies that are part of Altis, as those will be installed by Altis itself. Firstly, remove any submodules that are included in Altis. For example, `wordpress` and `hm-platform` submodules. To correctly remove a submodule:

1. `git submodule deinit -f wordpress`
2. `git rm -rf wordpress`
3. `rm -rf .git/modules/wordpress`

Do the same for `hm-platform` if you have this project dependency. Altis also includes the following WordPress plugins, so if your project uses git submodules or composer to include them, you should remove them from your project (unless you plan to disable the Altis module that makes use of the plugin, and continue with the plugin directly):

- `altis-reusable-blocks`
- `asset-loader`
- `aws-analytics`
- `aws-rekognition`
- `aws-ses-wp-mail`
- `aws-xray`
- `authorship`
- `batcache`
- `browser-security`
- `cavalcade`
- `consent`
- `consent-api`
- `clean-html`
- `debug-bar-elasticpress`
- `delegated-oauth`
- `elasticpress`
- `hm-gtm`
- `hm-redirects`
- `ludicrousdb`
- `meta-tags`
- `query-monitor`
- `require-login`
- `safe-svg`
- `simple-local-avatars`
- `smart-media`
- `stream`
- `s3-uploads`
- `tachyon-plugin`
- `two-factor`
- `wordpress-seo`
- `wp-redis`
- `wp-simple-saml`
- `wp-user-signups`

## Move wp-config.php

Installing Altis will replace your `wp-config.php` so you should back it up if you have application level configuration in your `wp-config.php`.

## Installing Altis

Now you have your composer project up and running, it’s time to add Altis to the project by running the following command:

```sh
composer require altis/altis
```

Once Altis has been installed, you should see the `wordpress` directory back in the project root, a new `wp-config.php` and `index.php`. Add these 3 files to your project's `.gitignore` file, as they should not be committed to version control. If your project had no `.gitignore` file, one will have been created for you.

### Troubleshooting

If you run into any package version conflicts you can try the following steps:

1. Remove existing `vendor` directory and `composer.lock` if present and try again
   - Mac / Linux: `rm -rf vendor composer.lock`
   - Windows: `rmdir vendor && del composer.lock`
1. Run `composer show <package>` on the conflicting package to see where it's required and try rolling back to an older version until it works
1. If you still face issues raise a support request with a copy of the composer output

## Restore site configuration

If you have any custom configuration in your old `wp-config.php` (such as custom PHP constants, etc. You will need to put them into a new file outside of `wp-config.php` (as changes to this file are not allowed). It’s possible your project already has the configuration split out into a `.config` directory, but if not, create a file at `.config/load.php`.

Only copy across constants that your custom code actually needs. There should be no database constants, or any other WordPress type constants. If you have these already in your `.config` directory, you should delete those.

The `.config/load.php` file will be automatically included from the generated `wp-config.php`.

## Migrate `$hm_platform` options (when applicable)

In your old `wp-config.php` you’ll see there is a `global $hm_platform` that sets options for hm-platform. Only migrate anything that you specifically need to, as most likely Altis will have better defaults. In rare cases though, things will be disabled for good reason. In Altis, most of the same settings are supported, but it’s now done via the `composer.json` for configuration (as is all Altis configuration). You should be familiar with [Altis configuration](docs://getting-started/configuration.md) before continuing. `$hm_platform` options should go in the `altis.modules.cloud` section of the `extra` block in the `composer.json`.

```json
{
	"extra": {
		"altis": {
			"modules": {
				"cloud": {
					"batcache": false
				}
			}
		}
	}
}
```

## Remove the `SUBDOMAIN_INSTALL` constant

The `SUBDOMAIN_INSTALL` constant is not required any more but may have been present in your old `wp-config.php` file. WordPress will handle subdomain, subdirectory and custom domain names without this constant set. To accommodate this Altis has a modified "Add New Site" page in the network admin to allow you to choose from the different types of URL.

If you still require this constant for any reason then you must wrap it in a check to see if WordPress is in its initial installation step:

```php
if ( ! defined( 'WP_INITIAL_INSTALL' ) || ! WP_INITIAL_INSTALL ) {
	define( 'SUBDOMAIN_INSTALL', true );
}
```

## Ensure `SUNRISE` is not defined during install

The `SUNRISE` constant puts WordPress into multisite mode which will cause problems during the initial installation.

If your site uses `sunrise.php` update the code where you define `SUNRISE` like so:

```php
if ( ! defined( 'WP_INITIAL_INSTALL' ) || ! WP_INITIAL_INSTALL ) {
	define( 'SUNRISE', true );
}
```

## Rename content/plugins-mu to content/mu-plugins

Altis uses the standard WordPress must-use plugins directory of `content/mu-plugins` so if your project is using something different, it should be renamed.

## Add composer install to the build script

If your project was not previously using composer, you'll need to add a `composer install --no-dev` to your project's `.build-script`. Simply add the following line to your `.build-script` or create that file if it doesn't already exist.

```sh
composer install --no-dev
```

## Setup the local server

To install the [Docker development environment](docs://local-server/README.md) run:

```sh
composer require --dev altis/local-server
```

To start the docker server run `composer server start`. You should now be able to see the site at https://my-project.altis.dev where "my-project" is the project directory name.

### Docker alternative

If you are unable to use Docker on your computer, consider trying a [GitHub Codespaces environment](docs://dev-tools/cloud-dev-env/), which makes it possible to spin up a complete Altis development environment within your browser, without having to install any additional software on your computer.

## Migrating from a single site install

Altis is always configured to be a WordPress multisite, as such any sites that are not installed as multisite already, will need converting via the `multisite-convert` WP CLI command. Note that you will need to do this on your Cloud environments after deploying.

To convert an existing single site to a multisite install run the following command:

```
wp core multisite-convert

# On Local Server
composer server cli core multisite-convert
```

**Important:** Part of the conversion process will reset your main site's permalink structure. You should reset this via the admin Permalinks page immediately.

Once you have reset the permalink structure you should flush the cache:

```
wp cache flush

# On Local Server
composer server cli cache flush
```

There are several key differences between a single site and a multisite install:

- Network admin area for creating and managing sites
- Network level administration of themes and plugins
- Super admin role for Network Admin access
- Users can be members of any number of sites
- Users can be removed from a given site rather than deleted

[See the WordPress Multisite Network Administration guide for more detail](https://wordpress.org/support/article/multisite-network-administration/).

## Migrating email sending domain

It's quite possible your project specifies the wp mail sending domain via the `wp_mail_from` hook. This can now be specified as setting in the `composer.json`'s `extra.altis.modules.cloud.email-from-address` setting:

```json
{
	"modules": {
		"cloud": {
			"email-from-address": "webmaster@mydomainname.com"
		}
	}
}
```

## Optionally disable Altis branding

As this guide is for migrating a non-Altis project to use Altis, it's possible the client relationship and understanding does not warrant changing anything visible or user-facing. If you are sure this is an "under the hood" migration, and the client has not been on-boarded with Altis as a brand, you can disable the branding via the `altis.modules.cms.branding` setting:

```json
{
	"modules": {
		"cms": {
			"branding": false
		}
	}
}
```

## Optionally disable Altis features

There are some features of Altis that are user-facing and default-on that you might want to audit. For example, image [lazy loading](docs://media/lazy-loading.md) via Guassholder is on by default. Smart Media with Cropping UI is enabled by default. You should consult the Altis documentation for the behavior of specific modules. Again, unless there is specific reason to disable feature and modules, we recommend keeping them on.

Any module can be disabled by setting its `enabled` setting to `false`:

```json
{
	"extra": {
		"altis": {
			"modules": {
				"seo": {
					"enabled": false
				}
			}
		}
	}
}
```

## Deploying to Cloud

The first time Altis is deployed, depending on the exact configuration, there may be tasks to perform on deploy:

- Multisite conversion
- Resetting permalinks
- Running the `wp altis migrate` command

As always, be sure to test the migration and deployment in `development` or `staging` environments before rolling out to production.
