# Migrating a WordPress Codebase

This guide covers how to migrate a typical WordPress project to Altis.

**Tip:** Throughout this guide, the following conventions are used:

* File names ending with a `/` (such as `wp-admin/`) indicate a directory (folder)
* Shell commands are indicated by a line starting with `$`, and other lines indicate output. Do not copy the `$`.


## Preparing the codebase

### Traditional vs Skeleton

Before you start, identify whether your project is structured in traditional WordPress style, or in WordPress Skeleton style.

In traditional WordPress style, your codebase will look like this:

* `wp-admin/`
* `wp-content/` (plugins and themes live here)
* `wp-includes/`
* `index.php`
* `wp-config.php`
* (etc)

In WordPress Skeleton style, your codebase will look like this:

* `content/` (plugins and themes live here)
* `wp/` or `wordpress/`
* `index.php`
* `wp-config.php`

Altis uses a WordPress Skeleton-style codebase layout, and you'll need to migrate to this style. Generally, you'll only need to preserve the `wp-content/` or `content/` directory; this is your "content" directory.


### Remove existing copy of WordPress

Altis installs and manages WordPress for you, so your codebase should not contain a copy of it. (When we add Altis modules later, you'll get a fresh copy managed by Composer.)

For a traditional codebase:

1. Delete the `wp-admin/` and `wp-includes/` directories (but *not* `wp-content/`)
2. Delete files prefixed with `wp-` (such as `wp-load.php`, `wp-settings.php`, etc)
3. Delete `index.php`, `license.txt`, `readme.html`, and `xmlrpc.php`
3. Rename `wp-content/` to `content/`

For a Skeleton-style codebase:

1. Delete the `wp/` or `wordpress/` directory. If this is managed as a git submodule, you'll need to remove the submodule:
	1. `git submodule deinit -f wp`
	2. `git rm -rf wp`
	3. `rm -rf .git/modules/wp`
2. Delete `index.php`

You should now have a codebase which looks like this:

* `.git/` (optional)
* `content/`
* `wp-config.php`


### Back up your configuration

Rename `wp-config.php` to `wp-config-backup.php`.

Altis manages your wp-config for you, but you'll want to keep a copy of your existing configuration in case you need it later.


### Remove plugins managed by Altis

To provide its functionality, Altis bundles some plugins. You should delete these from your `content/plugins/` directory if you have them.

Currently, Altis bundles the following plugins:

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


### Set up Composer

Next, we're going to add configuration for Composer. Composer is a dependency manager for PHP, and is how you'll manage installing WordPress and its dependencies.

If you don't already have Composer configuration in place, run `composer init` and follow the prompts. When asked if you would like to define your dependencies or dev dependencies, enter "n". When asked if you would like to add PSR-4 autoloading, enter "n".

This will create a `composer.json` file containing the configuration for your project. You can manage this file through various `composer` subcommands, or manually edit it.

Next, we need to add some extra configuration to allow Altis to set itself up. Open `composer.json` in your code editor, and add the following lines inside the top-level object (make sure to add any commas as needed for valid JSON syntax):

```json
    "extra": {
        "installer-paths": {
            "content/mu-plugins/{$name}/": [
                "type:wordpress-muplugin"
            ],
            "content/plugins/{$name}/": [
                "type:wordpress-plugin"
            ],
            "content/themes/{$name}/": [
                "type:wordpress-theme"
            ]
        }
    },
    "config": {
        "platform": {
            "php": "8.0",
            "ext-mbstring": "8.0"
        },
        "allow-plugins": {
            "composer/installers": true,
            "johnpbloch/wordpress-core-installer": true,
            "altis/cms-installer": true,
            "altis/dev-tools-command": true,
            "altis/core": true,
            "altis/local-server": true
        }
    }
```

This will allow Altis to run Composer plugins for custom commands, as well as ensuring any third party plugins or themes you add are placed into the right place.

Save the file and close it.


### Add Altis packages to your project

Now, let's add Altis to your codebase.

Run `composer require altis/altis` from the command line. You'll see Composer install Altis and its dependencies.

Next, run `composer require --dev altis/local-server`. You'll see Composer install the [Local Server](docs://local-server/).

Once these have been installed, your codebase will contain some new directories and files, and should look like:

* `.config/`
* `content/`
* `vendor/`
* `wordpress/`
* `.build-script`
* `.gitignore`
* `composer.json`
* `composer.lock`
* `index.php`
* `wp-config.php`
* `wp-config-backup.php` (if you followed the backup step above)

If you didn't have a `.gitignore` before, Altis will have created one for you. If you have an existing file, ensure that you add `vendor/`, `wordpress/`, `index.php`, and `wp-config.php` to it.


#### Troubleshooting

If you run into any package version conflicts you can try the following steps:

1. Remove existing `vendor` directory and `composer.lock` if present and try again
   - Mac / Linux: `rm -rf vendor composer.lock`
   - Windows: `rmdir vendor && del composer.lock`
1. Run `composer show <package>` on the conflicting package to see where it's required and try rolling back to an older version until it works
1. If you still face issues raise a support request with a copy of the composer output


### Restore custom wp-config configuration

If you have any custom configuration in your `wp-config-backup.php` (such as custom PHP constants, etc), you will need to put them into a new file as Altis manages `wp-config.php` for you. This could include license keys or plugin configuration constants.

Altis will automatically load `.config/load.php` for this purpose. Copy any custom configuration into this file.

**Only copy across constants that your custom code actually needs.** There should be no database constants, or any other WordPress type constants. If you have these already in your `.config` directory, you should delete those.

If you're not sure if you need to copy anything over, don't copy anything for now. You can always come back and copy it across later.

Ensure that you do not copy over any WordPress constants. In particular, **do not copy the following code**:

* Database configuration: `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_HOST`, `$table_prefix`
* URL configuration: `WP_HOME`, `WP_SITEURL`, `WP_CONTENT_DIR`, `WP_CONTENT_URL`
* Multisite configuration: `MULTISITE`, `SUBDOMAIN_INSTALL`, `DOMAIN_CURRENT_SITE`, `PATH_CURRENT_SITE`

These values are managed for you by Altis and must not be set.


## Preparing your data

With your codebase converted, we can now work on preparing the data from your current site.

The easiest way to import existing content is to [use WXR imports with the WordPress Importer](https://wordpress.org/support/article/importing-content/). This will automatically adjust your database if necessary, and will handle downloading any media assets that are necessary.

If you have a substantial amount of data to transfer, you may want to consider a [manual data migration](manual-data-migration.md) instead. This will allow you to precisely control the migration, and works better with larger sites, but requires manual work.
