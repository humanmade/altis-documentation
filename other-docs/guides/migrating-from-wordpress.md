# Migrating a WordPress Codebase

This guide covers how to migrate a typical WordPress project to HM Platform.

### Setup Composer

If the project does not already have a `composer.json` in the root of the repository, you should run `composer init` from the project root. This will prompt you fill out the required fields. For now, don’t bother adding any dependencies. If you already have a `composer.json` move to Remove Dependencies.

### Remove Dependencies

We’ll need to remove all the dependencies that are part of HM Platform, as those will be installed by HM Platform itself. Firstly, remove any submodules that are included in HM Platform. For example, `wordpress` and `hm-platform` submodules. To correctly remove a submodule:

1. `git submodule deinit -f wordpress`
2. `git rm -rf wordpress`
3. `rm -rf .git/modules/wordpress`

Do the same for `hm-platform` if you have this project dependency. HM Platform also includes the following WordPress plugins, so if your project uses git submodules or composer to include them, you should remove them from your project (unless you plan to disable the HM Platform module that makes use of the plugin, and continue with the plugin directly):

- `smart-media`
- `gaussholder`
- `aws-rekognition`
- `two-factor`
- `stream`
- `wp-simple-saml`
- `delegated-oauth`
- `elasticpress`
- `hm-redirects`
- `msm-sitemap`
- `wp-seo`
- `amp`
- `facebook-instant-articles-wp`
- `meta-tags`
- `query-monitor`
- `hm-gtm`
- `workflows`
- `wp-user-signups`

### Move wp-config.php

Installing HM Platform will replace your `wp-config.php` so you should back it up if you have application level configuration in your `wp-config.php`.

### Installing HM Platform

Now you have your composer project up and running, it’s time to add HM Platform to the project. Do so by running `composer require humanmade/platform`.

Once HM Platform has been installed, you should see the `wordpress` directory back in the project root, a new `wp-config.php` and `index.php`. You should now also see these 3 paths added to your `.gitignore`.

### Restore site configuration

If you have any custom configuration in your old `wp-config.php` (such as custom PHP constants, etc. You will need to put them into a new file outside of `wp-config.php` (as changes to this file are not allowed). It’s possible your project already has the configuration split out into a `.config` directory, but if not, create a file at `.config/load.php`.

Only copy across constants that your custom code actually needs. There should be no database constants, or any other WordPress type constants. If you have these already in your `.config` directory, you should delete those.

To make `.config/load.php` actually be included and executed, you have to add it as a Composer autoload file. To do so, add an `autoload` section to your `composer.json` if you don’t already have one, and add `.config/load.php` as an autoload files.

```json
	"autoload": {
		"files": [
			".config/load.php"
		]
	},
```

You’ll need to run `composer dump-autoload` after doing this to make sure it’s actually loaded.

### Migrate $hm_platform options (when applicable)

In your old `wp-config.php` you’ll see there is a `global $hm_platform` that sets options for hm-platform. Only migrate anything that you specifically need to, as most likely HM Platform will have better defaults. In rare cases though, things will be disabled for good reason. In HM Platform, most of the same settings are supported, but it’s now done via the `composer.json` for configuration (as is all HM Platform configuration). You should be familiar with [HM Platform configuration](docs://getting-started/configuration.md) before continuing. `$hm_platform` options should go in the `platform.modules.cloud` section of the `extra` block in the `composer.json`.

```json
"extra": {
	"platform": {
		"modules": {
			"cloud": {
				"batcache": false
			}
		}
	}
},

```

### Rename content/plugins-mu to content/mu-plugins

HM Platform uses the standard WordPress must-use plugins directory of `content/mu-plugins` so if your project is using something different, it should be renamed.

### Add composer install to the build script

If you project was not previously using composer, you'll need to add a `composer install --no-dev` to your projects `.build-script`. Simply add the following line to your `.build-script` or create that file if it doesn't already exist.

```
composer install --no-dev
```

### Setup the local server

Assuming your project uses Chassis for local development, we’ll be removing the local Chassis install, and installing the HM Platform module. If you have a setup script (such as `.bin/setup.sh`) you should remove any Chassis setup / installation steps.
Once you have cleaned out Chassis, install the `humanmade/platform-local-chassis` composer package as a dev dependency.

```
composer require --dev humanmade/platform-local-chassis
```

Once completed, install and start your local server with `composer chassis init` and then `composer chassis start`. You should now be able to navigate to http://platform.local to see the site!

### Migrating email sending domain

It's quite possible your project specifies the wp mail sending domain via the `wp_mail_from` hook. This can now be specified as setting in the `composer.json`'s `extra.platform.modules.cloud.email.email-from-address` setting:

```json
"modules": {
    "cloud": {
        "email-from-address": "webmaster@mydomainname.com"
    }
}
```

### Optionally disable HM Platform branding

As this guide is for migrating a non-HM Platform project to use HM Platform, it's possible the client relationship and understanding does warrant changing anything visible or user-facing. If you are sure this is an "under the hood" migration, and the client has not been on-boarded with HM Platform as a brand, you can disable the branding via the `platform.modules.cms.branding` setting:

```json
"modules": {
    "cms": {
        "branding": false
    }
}
```

### Optionally disable HM Platform features

There are some features of HM Platform that are user-facing and default-on that you might want to audit. For example, image [lazy loading](docs://media/lazy-loading.md) via Guassholder is on by default. Smart Media with Cropping UI is enabled by default. You should consult the HM Platform documentation for behavior of specific modules. Again, unless there is specific reason to disable feature and modules, we recommend keeping them on.

Any module can be disabled by setting its `enabled` setting to `false`:

```json
"extra": {
	"platform": {
		"modules": {
			"seo": {
				"enabled": false
			}
		}
	}
}
```

### Deploying to Cloud

The first time HM Platform is deployed, depending on the exact configuration, there may be tasks to perform on deploy. HM Platform is always configured to be a WordPress multisite, as such any sites that are not on installed as Multisite already, will need converting via the `multisite-convert` WP CLI command.

As always, be sure to test the migration and deployment in `development` or `staging` environments before rolling out to production.
