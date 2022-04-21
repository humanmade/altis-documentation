# Updating PHP Version

On occasion Altis will make a PHP version update available. Once a particular version of PHP is out of support, updating to a later version will become mandatory to help protect the security of your application.

There are 2 key steps to getting ready for a new version of PHP:

- Checking your application is compatible with the new version and making any necessary updates
- Requesting the PHP upgrade for your cloud environments

## Altis Compatibility Chart

**Note:** PHP 8.0 will officially be available from the Altis v12 release date onwards. Altis v11 will run on PHP 8.0 but support is experimental. Keep reading to learn how to prepare your application for a new PHP version.

|Altis|PHP Version|
|-|-|
|v13|8.0|
|v12|7.4-8.0|
|v11|7.4-8.0 (experimental)|
|v7-v11|7.4|
|v5-v6|7.2-7.4|
|v4|7.0-7.4|
|v1-v3|7.0-7.2|

## Checking PHP Version Compatibiliity

**Note:** Before carrying out the following tasks locally, ensure your application is fully installed so that all code that will be deployed to the cloud is present.

### Switch Off Composer's Platform Check

In your `composer.json` you may have some code like the following:

```json
{
    "config": {
        "platform": {
            "php": "7.2"
        }
    }
}
```

If present, and the PHP version does not match the version you are upgrading to you should update it to reflect the new target PHP version.

In addition you _must_ add the following setting in the `config` property of your `composer.json` file:

```json
{
    "config": {
        "platform-check": false
    }
}
```

This will prevent Composer from exiting your application early while your cloud environment is being updated.

### Update All Altis Modules

The first task is to ensure your application has the latest patch versions of all Altis modules by running the following command in your project root:

```
composer update "altis/*" --with-all-dependencies
```

Afterwards commit the updated `composer.lock` file.

### Run The Compatibility Check

The next step is using the PHP Compatibility package for PHP Code Sniffer. The guide belows shows you how to install and use this tool globally, however you may wish to install them directly in your project and use them in CI as part of your code linting process.

First install the standard and dependencies using the following command:

```
composer global require dealerdirect/phpcodesniffer-composer-installer phpcompatibility/php-compatibility
```

Next run the standard against your codebase for the target PHP version, in this example PHP 8.0:

```
phpcs -p --standard=PHPCompatibility \
  --runtime-set testVersion 8.0 \
  --extensions=php \
  -d memory_limit=1G \
  --ignore=wordpress,vendor/altis,\*/tests/\* .
```

This can take a long time to run so may wish to run it just in your custom code directories.

**Note:** You _must_ fix anything reported as an "Error". Warnings can be ignored at your discretion.

Fix any reported errors (ignoring those listed in the section below) by upgrading affected third party plugins and amending first party custom code as needed following your standard code review and deployment processes.

#### Known Warnings and Errors

There are some known warnings in the following packages that you can safely ignore. Altis guarantees its core packages and their dependencies are verified as working with the versions outlined in the compatibility chart at the top of this page.

- `aws/aws-sdk-php`
- `humanmade/batcache`
- `phpunit/phpunit`
- `wp-phpunit/wp-phpunit`
- `lucatume/wp-browser`

You can add these to the ignored directories in the command when checking if desired; e.g. `--ignore=vendor/aws,vendor/guzzlehttp,...`.

## Requesting A Cloud Environment Update

Once you are confident that your application is compatible with the version of PHP to upgrade to you should do the following:

1. Deploy the updated application to the environment you wish to update, we recommend starting with your non production environments first
2. Create a support ticket for the target environment with the type "Task", titled "Upgrade to PHP 7.4", replacing "7.4" with the target version if necessary

**Note:** If you are upgrading to an Altis version that is not compatible with the environment's current PHP version you will need to request that the environment is updated _prior_ to deploying the upgrade.

See the [Getting Help With Altis guide](../getting-help-with-altis.md) for more information on creating support tickets.

The Altis team will follow up with you on the ticket once the environment has been updated.

Once your environment has been upgraded, the PHP upgrade will take effect upon the next application deployment.
