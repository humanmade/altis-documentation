# Updating PHP Version

On occasion Altis will make a PHP version update available. Once a particular version of PHP is out of support, updating
to a later version will become mandatory to help protect the security of your application.

There are 2 key steps to getting ready for a new version of PHP:

- Checking your application is compatible with the new version and making any necessary updates
- Requesting the PHP upgrade for your cloud environments

## Altis Compatibility Chart

| Altis | PHP 8.1       | PHP 8.2        | PHP 8.3        |
|-------|---------------|----------------|----------------|
| v22   | **Supported** | **Supported**  | **Supported**  |
| v21   | **Supported** | **Supported**  | *Experimental* |
| v20   | **Supported** | **Supported**  | *Experimental* |
| v19   | **Supported** | **Supported**  |                |

## Checking PHP Version Compatibility

**Note:** Before carrying out the following tasks locally, ensure your application is fully installed so that all code
that will be deployed to the cloud is present.

### Switch Off Composer's Platform Check

In your `composer.json` you may have some code like the following:

```json
{
    "config": {
        "platform": {
            "php": "8.2"
        }
    }
}
```

If present, and the PHP version does not match the version you are upgrading to you should update it to reflect the new
target PHP version.

In addition you *must* add the following setting in the `config` property of your `composer.json` file:

```json
{
    "config": {
        "platform-check": false
    }
}
```

This will prevent Composer from exiting your application early while your cloud environment is being updated.

### Update All Altis Modules

The first task is to ensure your application has the latest patch versions of all Altis modules by running the following
command in your project root:

```shell
composer update "altis/*" --with-all-dependencies
```

Afterwards commit the updated `composer.lock` file.

### Run The Compatibility Check

The next step is using the PHP Compatibility package for PHP Code Sniffer. The guide below shows you how to install and
use this tool globally, however you may wish to install them directly in your project and use them in CI as part of your
code linting process.

First install the standard and dependencies using the following command:

```shell
composer global require dealerdirect/phpcodesniffer-composer-installer phpcompatibility/php-compatibility
```

Next run the standard against your codebase for the target PHP version, in this example PHP 8.2:

```shell
phpcs -p --standard=PHPCompatibility \
  --runtime-set testVersion 8.2 \
  --extensions=php \
  -d memory_limit=1G \
  --ignore=wordpress,vendor/altis,\*/tests/\* .
```

This can take a long time to run so may wish to run it just in your custom code directories.

**Note:** You *must* fix anything reported as an "Error". Warnings can be ignored at your discretion.

Fix any reported errors (ignoring those listed in the section below) by upgrading affected third party plugins and
amending first party custom code as needed following your standard code review and deployment processes.

#### Known Warnings and Errors

There are some known warnings in the following packages that you can safely ignore. Altis guarantees its core packages
and their dependencies are verified as working with the versions outlined in the compatibility chart at the top of this
page.

- `aws/aws-sdk-php`
- `humanmade/batcache`
- `phpunit/phpunit`
- `wp-phpunit/wp-phpunit`
- `lucatume/wp-browser`

You can add these to the ignored directories in the command when checking if desired;
e.g. `--ignore=vendor/aws,vendor/guzzlehttp,...`.

## Requesting A Cloud Environment Update

Once you are confident that your application is compatible with the version of PHP to upgrade to you should do the
following for each environment:

1. Create a support ticket for the target environment with the type "Task", titled "Upgrade to PHP 8.2", replacing "8.2"
   with the target version if necessary ( noting that the updated PHP version will not be deployed automatically until
   the next application deployment ).
2. Wait for the Altis team to confirm the environment has been updated.
3. Deploy the updated application code.

**Note:** We recommend starting with your non production environments first.

See the [Getting Help With Altis guide](../getting-help-with-altis.md) for more information on creating support tickets.

The Altis team will follow up with you on the ticket once the environment has been updated.

Once your environment has been upgraded, the PHP upgrade will take effect upon the next application deployment.

## Re-Enable Composer's Platform Check

If you previously disabled the platform check in Composer, you can now re-enable it once everything has been upgraded.
Remove the `"platform-check": false` line if you added it.

Additionally, we recommend setting the PHP version explicitly, which will ensure the correct packages are installed for
your PHP version.

The easiest way to add this is to run:

```sh
composer config platform.php 8.2
```

(Replace 8.2 with your desired new PHP version.)
